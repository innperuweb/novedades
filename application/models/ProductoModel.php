<?php

// Archivo base para desarrollo futuro del módulo Producto
// No modificar vistas ni lógica visual existente
// Conectado al router principal desde index.php

class ProductoModel
{
    protected ?string $tablaImagenes = null;

    public function getAll(): array
    {
        $pdo = Database::connect();
        $tablaImagenes = $this->obtenerTablaImagenes();
        $selectImagen = '';

        if ($tablaImagenes !== null) {
            $ordenPartes = [];
            if ($this->columnaExisteEnTabla($tablaImagenes, 'es_principal')) {
                $ordenPartes[] = 'pi.es_principal DESC';
            }
            if ($this->columnaExisteEnTabla($tablaImagenes, 'orden')) {
                $ordenPartes[] = 'pi.orden ASC';
            }
            $ordenPartes[] = 'pi.id ASC';
            $ordenSql = implode(', ', $ordenPartes);

            $selectImagen = ', (SELECT pi.ruta FROM ' . $tablaImagenes . ' pi WHERE pi.producto_id = p.id ORDER BY ' . $ordenSql . ' LIMIT 1) AS imagen_principal';
        }

        $sql = 'SELECT p.*' . $selectImagen . ' FROM productos p '
            . 'WHERE p.visible = 1 AND p.estado = 1 AND p.stock >= 0 ORDER BY p.id DESC';

        $stmt = $pdo->query($sql);

        $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        foreach ($productos as &$producto) {
            $producto['colores'] = $this->decodificarLista($producto['colores'] ?? null);
            $producto['tallas'] = $this->decodificarLista($producto['tallas'] ?? null);
            $producto['stock'] = (int) ($producto['stock'] ?? 0);
            if (!empty($producto['imagen_principal'])) {
                $producto['imagen'] = $producto['imagen_principal'];
            }
        }
        unset($producto);

        return $productos;
    }

    public function getById($id): ?array
    {
        if ($id === null) {
            return null;
        }

        $producto = $this->fetchProductoDesdeBaseDeDatos($id);
        if ($producto !== null) {
            $producto['colores'] = $this->decodificarLista($producto['colores'] ?? null);
            $producto['tallas'] = $this->decodificarLista($producto['tallas'] ?? null);
            $producto['imagenes'] = $this->getImagenes((int) ($producto['id'] ?? 0));
            $producto['stock'] = (int) ($producto['stock'] ?? 0);
            if (!empty($producto['imagen_principal'])) {
                $producto['imagen'] = $producto['imagen_principal'];
            }
        }

        $mock = $this->getMockProductos();
        $mockProducto = $mock[$id] ?? null;

        if ($producto === null) {
            return $mockProducto;
        }

        if ($mockProducto !== null) {
            if (empty($producto['colores'])) {
                $producto['colores'] = $mockProducto['colores'] ?? [];
            }

            if (empty($producto['tallas'])) {
                $producto['tallas'] = $mockProducto['tallas'] ?? [];
            }
        } else {
            $producto['colores'] = $producto['colores'] ?? [];
            $producto['tallas'] = $producto['tallas'] ?? [];
        }

        if (!empty($producto['imagenes'])) {
            $principal = $producto['imagenes'][0]['ruta'] ?? '';
            if ($principal !== '') {
                $producto['imagen'] = $principal;
            }
        }

        return $producto;
    }

    public function buscarProductos($term): array
    {
        $pdo = Database::connect();
        $tablaImagenes = $this->obtenerTablaImagenes();
        $selectImagen = '';

        if ($tablaImagenes !== null) {
            $ordenPartes = [];
            if ($this->columnaExisteEnTabla($tablaImagenes, 'es_principal')) {
                $ordenPartes[] = 'pi.es_principal DESC';
            }
            if ($this->columnaExisteEnTabla($tablaImagenes, 'orden')) {
                $ordenPartes[] = 'pi.orden ASC';
            }
            $ordenPartes[] = 'pi.id ASC';
            $ordenSql = implode(', ', $ordenPartes);

            $selectImagen = ', (SELECT pi.ruta FROM ' . $tablaImagenes . ' pi WHERE pi.producto_id = p.id ORDER BY ' . $ordenSql . ' LIMIT 1) AS imagen_principal';
        }

        $sql = 'SELECT p.id, p.nombre, p.precio, p.imagen' . $selectImagen
            . ' FROM productos p WHERE (p.nombre LIKE :term OR p.descripcion LIKE :term)'
            . ' AND p.visible = 1 AND p.estado = 1 AND p.stock >= 0 LIMIT 10';

        $stmt = $pdo->prepare($sql);
        $likeTerm = '%' . $term . '%';
        $stmt->execute([':term' => $likeTerm]);

        $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        foreach ($resultados as &$producto) {
            if (!empty($producto['imagen_principal'])) {
                $producto['imagen'] = $producto['imagen_principal'];
            }
        }
        unset($producto);

        return $resultados;
    }

    private function fetchProductoDesdeBaseDeDatos($id): ?array
    {
        try {
            $pdo = Database::connect();
            $tablaImagenes = $this->obtenerTablaImagenes();
            $selectImagen = '';

            if ($tablaImagenes !== null) {
                $ordenPartes = [];
                if ($this->columnaExisteEnTabla($tablaImagenes, 'es_principal')) {
                    $ordenPartes[] = 'pi.es_principal DESC';
                }
                if ($this->columnaExisteEnTabla($tablaImagenes, 'orden')) {
                    $ordenPartes[] = 'pi.orden ASC';
                }
                $ordenPartes[] = 'pi.id ASC';
                $ordenSql = implode(', ', $ordenPartes);

                $selectImagen = ', (SELECT pi.ruta FROM ' . $tablaImagenes . ' pi WHERE pi.producto_id = p.id ORDER BY ' . $ordenSql . ' LIMIT 1) AS imagen_principal';
            }

            $sql = 'SELECT p.*' . $selectImagen . ' FROM productos p '
                . 'WHERE p.id = :id AND p.visible = 1 AND p.estado = 1 AND p.stock >= 0 LIMIT 1';

            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $result !== false ? $result : null;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function getMockProductos(): array
    {
        return [
            1 => [
                'id' => 1,
                'nombre' => 'Polera Oversize',
                'precio' => 89.90,
                'imagen' => 'producto1.jpg',
                'colores' => ['Negro', 'Blanco', 'Azul'],
                'tallas' => ['S', 'M', 'L', 'XL'],
            ],
            2 => [
                'id' => 2,
                'nombre' => 'Pantalón Jogger',
                'precio' => 119.90,
                'imagen' => 'producto2.jpg',
                'colores' => ['Gris', 'Negro'],
                'tallas' => ['28', '30', '32', '34'],
            ],
        ];
    }

    private function decodificarLista($valor): array
    {
        if ($valor === null || $valor === '') {
            return [];
        }

        if (is_array($valor)) {
            return array_values(array_map('strval', $valor));
        }

        $decoded = json_decode((string) $valor, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_map('strval', $decoded));
        }

        $partes = preg_split('/[;,]+/', (string) $valor) ?: [];
        $partes = array_map(static function ($item): string {
            return trim((string) $item);
        }, $partes);
        $partes = array_filter($partes, static fn ($item): bool => $item !== '');

        return array_values(array_unique($partes));
    }

    public function getImagenes(int $productoId): array
    {
        if ($productoId <= 0) {
            return [];
        }

        try {
            $pdo = Database::connect();
            $tablaImagenes = $this->obtenerTablaImagenes();
            if ($tablaImagenes === null) {
                return [];
            }

            $tienePrincipal = $this->columnaExisteEnTabla($tablaImagenes, 'es_principal');
            $tieneOrden = $this->columnaExisteEnTabla($tablaImagenes, 'orden');

            $columnas = 'ruta';
            if ($tienePrincipal) {
                $columnas .= ', es_principal';
            }
            if ($tieneOrden) {
                $columnas .= ', orden';
            }

            $ordenPartes = [];
            if ($tienePrincipal) {
                $ordenPartes[] = 'es_principal DESC';
            }
            if ($tieneOrden) {
                $ordenPartes[] = 'orden ASC';
            }
            $ordenPartes[] = 'id ASC';
            $ordenSql = implode(', ', $ordenPartes);

            $stmt = $pdo->prepare('SELECT ' . $columnas . ' FROM ' . $tablaImagenes . ' WHERE producto_id = :id ORDER BY ' . $ordenSql);
            $stmt->execute([':id' => $productoId]);

            $imagenes = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            return array_map(static function ($item) use ($tienePrincipal, $tieneOrden): array {
                return [
                    'ruta' => trim((string) ($item['ruta'] ?? '')),
                    'es_principal' => $tienePrincipal ? (int) ($item['es_principal'] ?? 0) : 0,
                    'orden' => $tieneOrden ? (int) ($item['orden'] ?? 0) : 0,
                ];
            }, $imagenes);
        } catch (\Throwable $exception) {
            return [];
        }
    }

    protected function columnaExisteEnTabla(string $tabla, string $columna): bool
    {
        static $cache = [];

        $clave = $tabla . '.' . $columna;
        if (array_key_exists($clave, $cache)) {
            return $cache[$clave];
        }

        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :tabla AND column_name = :columna');
        $stmt->execute([
            ':tabla' => $tabla,
            ':columna' => $columna,
        ]);

        $cache[$clave] = ((int) $stmt->fetchColumn()) > 0;

        return $cache[$clave];
    }

    protected function obtenerTablaImagenes(): ?string
    {
        if ($this->tablaImagenes !== null) {
            return $this->tablaImagenes;
        }

        if ($this->tablaExiste('producto_imagenes')) {
            $this->tablaImagenes = 'producto_imagenes';

            return $this->tablaImagenes;
        }

        if ($this->tablaExiste('productos_imagenes')) {
            $this->tablaImagenes = 'productos_imagenes';

            return $this->tablaImagenes;
        }

        $this->tablaImagenes = null;

        return null;
    }

    protected function tablaExiste(string $tabla): bool
    {
        static $cache = [];

        if (array_key_exists($tabla, $cache)) {
            return $cache[$tabla];
        }

        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :tabla');
        $stmt->execute([':tabla' => $tabla]);

        $cache[$tabla] = ((int) $stmt->fetchColumn()) > 0;

        return $cache[$tabla];
    }

    public static function obtenerPorSubcategoria(string $slug): array
    {
        $slug = trim($slug);

        if ($slug === '') {
            return [];
        }

        try {
            $db = Database::connect();
            $model = new self();
            $tablaImagenes = $model->obtenerTablaImagenes();
            $selectImagen = '';

            if ($tablaImagenes !== null) {
                $ordenPartes = [];
                if ($model->columnaExisteEnTabla($tablaImagenes, 'es_principal')) {
                    $ordenPartes[] = 'pi.es_principal DESC';
                }
                if ($model->columnaExisteEnTabla($tablaImagenes, 'orden')) {
                    $ordenPartes[] = 'pi.orden ASC';
                }
                $ordenPartes[] = 'pi.id ASC';
                $ordenSql = implode(', ', $ordenPartes);

                $selectImagen = ', (SELECT pi.ruta FROM ' . $tablaImagenes . ' pi WHERE pi.producto_id = p.id ORDER BY ' . $ordenSql . ' LIMIT 1) AS imagen_principal';
            }

            $stmt = $db->prepare(
                'SELECT p.*' . $selectImagen . ' FROM productos p ' .
                'INNER JOIN subcategorias s ON p.subcategoria_id = s.id ' .
                'WHERE s.slug = :slug AND p.estado = 1 AND p.visible = 1 AND p.stock >= 0 ORDER BY p.id DESC'
            );
            $stmt->execute([':slug' => $slug]);

            $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            foreach ($productos as &$producto) {
                if (!empty($producto['imagen_principal'])) {
                    $producto['imagen'] = $producto['imagen_principal'];
                }
            }
            unset($producto);

            return $productos;
        } catch (\Throwable $exception) {
            return [];
        }
    }

    public static function obtenerFiltrados(string $slug, string $orden): array
    {
        $slug = trim($slug);

        if ($slug === '') {
            return [];
        }

        $ordenSQL = match ($orden) {
            'precio_asc' => 'p.precio ASC',
            'precio_desc' => 'p.precio DESC',
            'nombre_asc' => 'p.nombre ASC',
            'nombre_desc' => 'p.nombre DESC',
            default => 'p.id DESC',
        };

        try {
            $db = Database::connect();
            $model = new self();
            $tablaImagenes = $model->obtenerTablaImagenes();
            $selectImagen = '';

            if ($tablaImagenes !== null) {
                $ordenPartes = [];
                if ($model->columnaExisteEnTabla($tablaImagenes, 'es_principal')) {
                    $ordenPartes[] = 'pi.es_principal DESC';
                }
                if ($model->columnaExisteEnTabla($tablaImagenes, 'orden')) {
                    $ordenPartes[] = 'pi.orden ASC';
                }
                $ordenPartes[] = 'pi.id ASC';
                $ordenSqlImagen = implode(', ', $ordenPartes);

                $selectImagen = ', (SELECT pi.ruta FROM ' . $tablaImagenes . ' pi WHERE pi.producto_id = p.id ORDER BY ' . $ordenSqlImagen . ' LIMIT 1) AS imagen_principal';
            }

            $stmt = $db->prepare(
                'SELECT p.*' . $selectImagen . ' FROM productos p ' .
                'INNER JOIN subcategorias s ON p.subcategoria_id = s.id ' .
                'WHERE s.slug = :slug AND p.estado = 1 AND p.visible = 1 AND p.stock >= 0 ORDER BY ' . $ordenSQL
            );
            $stmt->execute([':slug' => $slug]);

            $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            foreach ($productos as &$producto) {
                if (!empty($producto['imagen_principal'])) {
                    $producto['imagen'] = $producto['imagen_principal'];
                }
            }
            unset($producto);

            return $productos;
        } catch (\Throwable $exception) {
            return [];
        }
    }

    public static function filtrarPorPrecio(string $slug, float $min, float $max): array
    {
        $slug = trim($slug);

        if ($slug === '') {
            return [];
        }

        $min = (float) $min;
        $max = (float) $max;

        try {
            $db = Database::connect();
            $model = new self();
            $tablaImagenes = $model->obtenerTablaImagenes();
            $selectImagen = '';

            if ($tablaImagenes !== null) {
                $ordenPartes = [];
                if ($model->columnaExisteEnTabla($tablaImagenes, 'es_principal')) {
                    $ordenPartes[] = 'pi.es_principal DESC';
                }
                if ($model->columnaExisteEnTabla($tablaImagenes, 'orden')) {
                    $ordenPartes[] = 'pi.orden ASC';
                }
                $ordenPartes[] = 'pi.id ASC';
                $ordenSql = implode(', ', $ordenPartes);

                $selectImagen = ', (SELECT pi.ruta FROM ' . $tablaImagenes . ' pi WHERE pi.producto_id = p.id ORDER BY ' . $ordenSql . ' LIMIT 1) AS imagen_principal';
            }

            $stmt = $db->prepare(
                'SELECT p.*' . $selectImagen . ' FROM productos p ' .
                'INNER JOIN subcategorias s ON p.subcategoria_id = s.id ' .
                'WHERE s.slug = :slug AND p.precio BETWEEN :min AND :max AND p.estado = 1 '
                . 'AND p.visible = 1 AND p.stock >= 0 ORDER BY p.id DESC'
            );
            $stmt->execute([
                ':slug' => $slug,
                ':min' => $min,
                ':max' => $max,
            ]);

            $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            foreach ($productos as &$producto) {
                if (!empty($producto['imagen_principal'])) {
                    $producto['imagen'] = $producto['imagen_principal'];
                }
            }
            unset($producto);

            return $productos;
        } catch (\Throwable $exception) {
            return [];
        }
    }

    public static function buscar(string $termino): array
    {
        $termino = trim($termino);

        if ($termino === '') {
            return [];
        }

        try {
            $db = Database::connect();
            $model = new self();
            $tablaImagenes = $model->obtenerTablaImagenes();
            $selectImagen = '';

            if ($tablaImagenes !== null) {
                $ordenPartes = [];
                if ($model->columnaExisteEnTabla($tablaImagenes, 'es_principal')) {
                    $ordenPartes[] = 'pi.es_principal DESC';
                }
                if ($model->columnaExisteEnTabla($tablaImagenes, 'orden')) {
                    $ordenPartes[] = 'pi.orden ASC';
                }
                $ordenPartes[] = 'pi.id ASC';
                $ordenSql = implode(', ', $ordenPartes);

                $selectImagen = ', (SELECT pi.ruta FROM ' . $tablaImagenes . ' pi WHERE pi.producto_id = p.id ORDER BY ' . $ordenSql . ' LIMIT 1) AS imagen_principal';
            }

            $stmt = $db->prepare(
                'SELECT p.*' . $selectImagen . ' FROM productos p '
                . 'WHERE (p.nombre LIKE :q OR p.descripcion LIKE :q) '
                . 'AND p.estado = 1 AND p.visible = 1 AND p.stock >= 0 ORDER BY p.id DESC'
            );
            $like = '%' . $termino . '%';
            $stmt->execute([':q' => $like]);

            $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            foreach ($productos as &$producto) {
                if (!empty($producto['imagen_principal'])) {
                    $producto['imagen'] = $producto['imagen_principal'];
                }
            }
            unset($producto);

            return $productos;
        } catch (\Throwable $exception) {
            return [];
        }
    }
}
