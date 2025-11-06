<?php

// Archivo base para desarrollo futuro del módulo Producto
// No modificar vistas ni lógica visual existente
// Conectado al router principal desde index.php

class ProductoModel
{

    public function getAll(): array
    {
        $pdo = Database::connect();

        $sql = 'SELECT p.* FROM productos p ' . 'WHERE p.visible = 1 AND p.estado = 1 AND p.stock >= 0 ORDER BY p.id DESC';

        $stmt = $pdo->query($sql);

        $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        foreach ($productos as &$producto) {
            $producto['colores'] = $this->limpiarOpciones($this->decodificarLista($producto['colores'] ?? null));
            $producto['tallas'] = $this->limpiarOpciones($this->decodificarLista($producto['tallas'] ?? null));
            $producto['subcategorias'] = [];
            $producto['subcategorias_nombres'] = [];
            $producto['stock'] = (int) ($producto['stock'] ?? 0);
            if (array_key_exists('tabla_tallas', $producto)) {
                $producto['tabla_tallas'] = trim((string) ($producto['tabla_tallas'] ?? ''));
            }
            if (isset($producto['imagen'])) {
                $producto['imagen'] = trim((string) $producto['imagen']);
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
            $producto['colores'] = $this->limpiarOpciones($this->decodificarLista($producto['colores'] ?? null));
            $producto['tallas'] = $this->limpiarOpciones($this->decodificarLista($producto['tallas'] ?? null));
            $producto['stock'] = (int) ($producto['stock'] ?? 0);
            if (array_key_exists('tabla_tallas', $producto)) {
                $producto['tabla_tallas'] = trim((string) ($producto['tabla_tallas'] ?? ''));
            }
            if (isset($producto['imagen'])) {
                $producto['imagen'] = trim((string) $producto['imagen']);
            }
        }

        $mock = $this->getMockProductos();
        $mockProducto = $mock[$id] ?? null;

        if ($producto === null) {
            if ($mockProducto !== null) {
                $mockProducto['colores'] = $this->limpiarOpciones($mockProducto['colores'] ?? []);
                $mockProducto['tallas'] = $this->limpiarOpciones($mockProducto['tallas'] ?? []);
            }

            return $mockProducto;
        }

        $producto['colores'] = $this->limpiarOpciones($producto['colores'] ?? []);
        $producto['tallas'] = $this->limpiarOpciones($producto['tallas'] ?? []);

        return $producto;
    }

    public function buscarProductos($term): array
    {
        $pdo = Database::connect();

        $sql = 'SELECT p.id, p.nombre, p.precio, p.imagen FROM productos p ' .
            'WHERE (p.nombre LIKE :term OR p.descripcion LIKE :term) ' .
            'AND p.visible = 1 AND p.estado = 1 AND p.stock >= 0 LIMIT 10';

        $stmt = $pdo->prepare($sql);
        $likeTerm = '%' . $term . '%';
        $stmt->execute([':term' => $likeTerm]);

        $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        foreach ($resultados as &$producto) {
            if (isset($producto['imagen'])) {
                $producto['imagen'] = trim((string) $producto['imagen']);
            } else {
                $producto['imagen'] = '';
            }
        }
        unset($producto);

        return $resultados;
    }

    private function fetchProductoDesdeBaseDeDatos($id): ?array
    {
        try {
            $pdo = Database::connect();
            $sql = 'SELECT p.* FROM productos p ' .
                'WHERE p.id = :id AND p.visible = 1 AND p.estado = 1 AND p.stock >= 0 LIMIT 1';

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

    private function limpiarOpciones($valor): array
    {
        if (!is_array($valor)) {
            return [];
        }

        $items = array_map(static fn ($item): string => trim((string) $item), $valor);
        $items = array_filter($items, static fn ($item): bool => $item !== '');

        return array_values(array_unique($items));
    }

    public static function obtenerPorSubcategoria(string $slug): array
    {
        $slug = trim($slug);

        if ($slug === '') {
            return [];
        }

        try {
            $db = Database::connect();
            $stmt = $db->prepare(
                'SELECT DISTINCT p.* FROM productos p ' .
                'INNER JOIN producto_subcategoria ps ON ps.producto_id = p.id ' .
                'INNER JOIN subcategorias s ON s.id = ps.subcategoria_id ' .
                'WHERE s.slug = :slug AND p.estado = 1 AND p.visible = 1 AND p.stock >= 0 ORDER BY p.id DESC'
            );
            $stmt->execute([':slug' => $slug]);

            $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            foreach ($productos as &$producto) {
                if (isset($producto['imagen'])) {
                    $producto['imagen'] = trim((string) $producto['imagen']);
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
            $stmt = $db->prepare(
                'SELECT DISTINCT p.* FROM productos p ' .
                'INNER JOIN producto_subcategoria ps ON ps.producto_id = p.id ' .
                'INNER JOIN subcategorias s ON s.id = ps.subcategoria_id ' .
                'WHERE s.slug = :slug AND p.estado = 1 AND p.visible = 1 AND p.stock >= 0 ORDER BY ' . $ordenSQL
            );
            $stmt->execute([':slug' => $slug]);

            $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            foreach ($productos as &$producto) {
                if (isset($producto['imagen'])) {
                    $producto['imagen'] = trim((string) $producto['imagen']);
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
            $stmt = $db->prepare(
                'SELECT DISTINCT p.* FROM productos p ' .
                'INNER JOIN producto_subcategoria ps ON ps.producto_id = p.id ' .
                'INNER JOIN subcategorias s ON s.id = ps.subcategoria_id ' .
                'WHERE s.slug = :slug AND p.precio BETWEEN :min AND :max ' .
                'AND p.estado = 1 AND p.visible = 1 AND p.stock >= 0 ORDER BY p.id DESC'
            );
            $stmt->execute([':slug' => $slug, ':min' => $min, ':max' => $max]);

            $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            foreach ($productos as &$producto) {
                if (isset($producto['imagen'])) {
                    $producto['imagen'] = trim((string) $producto['imagen']);
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
            $stmt = $db->prepare(
                'SELECT p.* FROM productos p ' .
                'WHERE (p.nombre LIKE :q OR p.descripcion LIKE :q) ' .
                'AND p.estado = 1 AND p.visible = 1 AND p.stock >= 0 ORDER BY p.id DESC'
            );
            $like = '%' . $termino . '%';
            $stmt->execute([':q' => $like]);

            $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            foreach ($productos as &$producto) {
                if (isset($producto['imagen'])) {
                    $producto['imagen'] = trim((string) $producto['imagen']);
                }
            }
            unset($producto);

            return $productos;
        } catch (\Throwable $exception) {
            return [];
        }
    }
}
