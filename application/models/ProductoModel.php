<?php

// Archivo base para desarrollo futuro del módulo Producto
// No modificar vistas ni lógica visual existente
// Conectado al router principal desde index.php

class ProductoModel
{
    protected const TABLA_IMAGENES = 'producto_imagenes';
    protected ?string $tablaImagenes = null;

    public function getAll(): array
    {
        $pdo = Database::connect();
        $tablaImagenes = $this->obtenerTablaImagenes();
        $selectImagen = '';

        if ($tablaImagenes !== null) {
            $ordenSql = $this->construirOrdenImagenes();
            $selectImagen = ', (SELECT pi.ruta FROM ' . $tablaImagenes . ' pi WHERE pi.producto_id = p.id ORDER BY ' . $ordenSql . ' LIMIT 1) AS imagen_principal';
        }

        $sql = 'SELECT p.*' . $selectImagen . ' FROM productos p ' .
            'WHERE p.visible = 1 AND p.estado = 1 AND p.stock >= 0 ORDER BY p.id DESC';

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
            $producto['colores'] = $this->limpiarOpciones($this->decodificarLista($producto['colores'] ?? null));
            $producto['tallas'] = $this->limpiarOpciones($this->decodificarLista($producto['tallas'] ?? null));
            $producto['imagenes'] = $this->obtenerImagenesPorProducto((int) ($producto['id'] ?? 0));
            $producto['stock'] = (int) ($producto['stock'] ?? 0);
            if (array_key_exists('tabla_tallas', $producto)) {
                $producto['tabla_tallas'] = trim((string) ($producto['tabla_tallas'] ?? ''));
            }
            if (!empty($producto['imagen_principal'])) {
                $producto['imagen'] = $producto['imagen_principal'];
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
            $ordenSql = $this->construirOrdenImagenes();
            $selectImagen = ', (SELECT pi.ruta FROM ' . $tablaImagenes . ' pi WHERE pi.producto_id = p.id ORDER BY ' . $ordenSql . ' LIMIT 1) AS imagen_principal';
        }

        $sql = 'SELECT p.id, p.nombre, p.precio' . $selectImagen
            . ' FROM productos p WHERE (p.nombre LIKE :term OR p.descripcion LIKE :term)'
            . ' AND p.visible = 1 AND p.estado = 1 AND p.stock >= 0 LIMIT 10';

        $stmt = $pdo->prepare($sql);
        $likeTerm = '%' . $term . '%';
        $stmt->execute([':term' => $likeTerm]);

        $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        foreach ($resultados as &$producto) {
            if (!empty($producto['imagen_principal'])) {
                $producto['imagen'] = $producto['imagen_principal'];
            } else {
                $producto['imagen'] = $producto['imagen'] ?? '';
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
                $ordenSql = $this->construirOrdenImagenes();
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

    private function limpiarOpciones($valor): array
    {
        if (!is_array($valor)) {
            return [];
        }

        $items = array_map(static fn ($item): string => trim((string) $item), $valor);
        $items = array_filter($items, static fn ($item): bool => $item !== '');

        return array_values(array_unique($items));
    }

    public function obtenerImagenesPorProducto(int $productoId): array
    {
        return $this->resolverImagenesPorProducto($productoId);
    }

    public function getImagenes(int $productoId): array
    {
        return $this->resolverImagenesPorProducto($productoId);
    }

    private function resolverImagenesPorProducto(int $productoId): array
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

            $ordenSql = $this->construirOrdenImagenes('');

            $columnas = ['id', 'ruta'];
            $tieneNombre = $this->columnaExisteEnTabla($tablaImagenes, 'nombre');
            $tienePrincipal = $this->columnaExisteEnTabla($tablaImagenes, 'es_principal');
            $tieneOrden = $this->columnaExisteEnTabla($tablaImagenes, 'orden');

            if ($tieneNombre) {
                $columnas[] = 'nombre';
            }
            if ($tienePrincipal) {
                $columnas[] = 'es_principal';
            }
            if ($tieneOrden) {
                $columnas[] = 'orden';
            }

            $stmt = $pdo->prepare('SELECT ' . implode(', ', $columnas) . ' FROM ' . $tablaImagenes . ' WHERE producto_id = :id ORDER BY ' . $ordenSql);
            $stmt->execute([':id' => $productoId]);

            $imagenes = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            return array_map(static function (array $item) use ($tieneNombre, $tienePrincipal, $tieneOrden): array {
                return [
                    'id' => (int) ($item['id'] ?? 0),
                    'ruta' => trim((string) ($item['ruta'] ?? '')),
                    'nombre' => $tieneNombre ? trim((string) ($item['nombre'] ?? '')) : '',
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

        if (!preg_match('/^[A-Za-z0-9_]+$/', $tabla) || !preg_match('/^[A-Za-z0-9_]+$/', $columna)) {
            $cache[$clave] = false;

            return $cache[$clave];
        }

        try {
            $pdo = Database::connect();
            $driver = strtolower((string) $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));

            switch ($driver) {
                case 'mysql':
                    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :tabla AND column_name = :columna');
                    $stmt->execute([
                        ':tabla' => $tabla,
                        ':columna' => $columna,
                    ]);
                    $cache[$clave] = ((int) $stmt->fetchColumn()) > 0;

                    return $cache[$clave];
                case 'pgsql':
                case 'postgres':
                    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = current_schema() AND table_name = :tabla AND column_name = :columna');
                    $stmt->execute([
                        ':tabla' => $tabla,
                        ':columna' => $columna,
                    ]);
                    $cache[$clave] = ((int) $stmt->fetchColumn()) > 0;

                    return $cache[$clave];
                case 'sqlsrv':
                case 'dblib':
                case 'mssql':
                    $stmt = $pdo->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = :tabla AND COLUMN_NAME = :columna');
                    $stmt->execute([
                        ':tabla' => $tabla,
                        ':columna' => $columna,
                    ]);
                    $cache[$clave] = ((int) $stmt->fetchColumn()) > 0;

                    return $cache[$clave];
                case 'sqlite':
                    $existe = false;
                    $resultado = $pdo->query("PRAGMA table_info('$tabla')");
                    if ($resultado !== false) {
                        $columnas = $resultado->fetchAll(\PDO::FETCH_ASSOC) ?: [];
                        foreach ($columnas as $info) {
                            $nombre = isset($info['name']) ? (string) $info['name'] : '';
                            if (strcasecmp($nombre, $columna) === 0) {
                                $existe = true;
                                break;
                            }
                        }
                    }
                    $cache[$clave] = $existe;

                    return $cache[$clave];
                default:
                    $sql = sprintf('SELECT * FROM %s LIMIT 0', $tabla);
                    $stmt = $pdo->query($sql);
                    if ($stmt !== false) {
                        $encontrada = false;
                        $columnCount = $stmt->columnCount();
                        for ($i = 0; $i < $columnCount; $i++) {
                            $meta = $stmt->getColumnMeta($i);
                            $nombre = isset($meta['name']) ? (string) $meta['name'] : '';
                            if (strcasecmp($nombre, $columna) === 0) {
                                $encontrada = true;
                                break;
                            }
                        }
                        $cache[$clave] = $encontrada;

                        return $cache[$clave];
                    }
                    break;
            }
        } catch (\Throwable $exception) {
            // Ignorar y continuar con el valor por defecto
        }

        $cache[$clave] = false;

        return $cache[$clave];
    }

    protected function construirOrdenImagenes(string $alias = 'pi'): string
    {
        $tabla = $this->obtenerTablaImagenes();

        $alias = trim($alias);
        if ($alias !== '') {
            $alias = rtrim($alias, '.') . '.';
        }

        $componentes = [];

        if ($tabla !== null && $this->columnaExisteEnTabla($tabla, 'es_principal')) {
            $componentes[] = $alias . 'es_principal DESC';
        }

        if ($tabla !== null && $this->columnaExisteEnTabla($tabla, 'orden')) {
            $componentes[] = $alias . 'orden ASC';
        }

        $componentes[] = $alias . 'id ASC';

        return implode(', ', $componentes);
    }

    protected function obtenerTablaImagenes(): ?string
    {
        $this->asegurarTablaImagenes();

        return $this->tablaImagenes;
    }

    protected function asegurarTablaImagenes(): void
    {
        if ($this->tablaImagenes !== null && $this->tablaExiste($this->tablaImagenes)) {
            $this->asegurarColumnasTablaImagenes($this->tablaImagenes);

            return;
        }

        $tablaPrincipal = static::TABLA_IMAGENES;
        $tablasAlternativas = ['productos_imagenes', 'producto_imagen', 'productos_imagen'];

        if ($this->tablaExiste($tablaPrincipal)) {
            $this->tablaImagenes = $tablaPrincipal;
            $this->asegurarColumnasTablaImagenes($tablaPrincipal);

            return;
        }

        foreach ($tablasAlternativas as $tablaAlternativa) {
            if ($this->tablaExiste($tablaAlternativa)) {
                $this->tablaImagenes = $tablaAlternativa;
                $this->asegurarColumnasTablaImagenes($tablaAlternativa);

                return;
            }
        }

        $this->crearTablaImagenes($tablaPrincipal);

        if ($this->tablaExiste($tablaPrincipal)) {
            $this->tablaImagenes = $tablaPrincipal;
            $this->asegurarColumnasTablaImagenes($tablaPrincipal);

            return;
        }

        $this->tablaImagenes = null;
    }

    protected function crearTablaImagenes(string $tabla): void
    {
        try {
            $pdo = Database::connect();
            $sql = 'CREATE TABLE IF NOT EXISTS ' . $tabla . ' ('
                . 'id INT AUTO_INCREMENT PRIMARY KEY,'
                . ' producto_id INT NOT NULL,'
                . " nombre VARCHAR(255) NOT NULL DEFAULT '',"
                . " ruta VARCHAR(255) NOT NULL DEFAULT '',"
                . ' es_principal TINYINT(1) NOT NULL DEFAULT 0,'
                . ' orden INT NOT NULL DEFAULT 0,'
                . ' creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,'
                . ' CONSTRAINT fk_' . $tabla . '_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,'
                . ' INDEX idx_' . $tabla . '_producto (producto_id)'
                . ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';
            $pdo->exec($sql);
        } catch (\Throwable $exception) {
            // Ignorar errores de creación
        }
    }

    protected function asegurarColumnasTablaImagenes(string $tabla): void
    {
        try {
            $pdo = Database::connect();
            $driver = strtolower((string) $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));

            $columnas = [
                'nombre' => [
                    'sql' => "ALTER TABLE $tabla ADD COLUMN nombre VARCHAR(255) NOT NULL DEFAULT ''",
                    'sqlite' => "ALTER TABLE $tabla ADD COLUMN nombre TEXT NOT NULL DEFAULT ''",
                ],
                'ruta' => [
                    'sql' => "ALTER TABLE $tabla ADD COLUMN ruta VARCHAR(255) NOT NULL DEFAULT ''",
                    'sqlite' => "ALTER TABLE $tabla ADD COLUMN ruta TEXT NOT NULL DEFAULT ''",
                ],
                'es_principal' => [
                    'sql' => "ALTER TABLE $tabla ADD COLUMN es_principal TINYINT(1) NOT NULL DEFAULT 0",
                    'sqlite' => "ALTER TABLE $tabla ADD COLUMN es_principal INTEGER NOT NULL DEFAULT 0",
                ],
                'orden' => [
                    'sql' => "ALTER TABLE $tabla ADD COLUMN orden INT NOT NULL DEFAULT 0",
                    'sqlite' => "ALTER TABLE $tabla ADD COLUMN orden INTEGER NOT NULL DEFAULT 0",
                ],
            ];

            foreach ($columnas as $columna => $sentencias) {
                if ($this->columnaExisteEnTabla($tabla, $columna)) {
                    continue;
                }

                $sql = $sentencias['sql'];
                if ($driver === 'sqlite' && isset($sentencias['sqlite'])) {
                    $sql = $sentencias['sqlite'];
                }

                try {
                    $pdo->exec($sql);
                } catch (\Throwable $ignored) {
                    // La columna ya existe o el cambio no es necesario
                }
            }

            try {
                $pdo->exec('ALTER TABLE ' . $tabla . ' ADD INDEX idx_' . $tabla . '_producto (producto_id)');
            } catch (\Throwable $ignored) {
                // índice ya existe o base de datos no soporta la instrucción
            }
        } catch (\Throwable $exception) {
            // Ignorar
        }
    }

    protected function tablaExiste(string $tabla): bool
    {
        static $cache = [];

        if (array_key_exists($tabla, $cache)) {
            return $cache[$tabla];
        }

        if (!preg_match('/^[A-Za-z0-9_]+$/', $tabla)) {
            $cache[$tabla] = false;

            return $cache[$tabla];
        }

        try {
            $pdo = Database::connect();
            $driver = strtolower((string) $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));

            switch ($driver) {
                case 'mysql':
                    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :tabla');
                    $stmt->execute([':tabla' => $tabla]);
                    $cache[$tabla] = ((int) $stmt->fetchColumn()) > 0;

                    return $cache[$tabla];
                case 'pgsql':
                case 'postgres':
                    $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = current_schema() AND table_name = :tabla');
                    $stmt->execute([':tabla' => $tabla]);
                    $cache[$tabla] = ((int) $stmt->fetchColumn()) > 0;

                    return $cache[$tabla];
                case 'sqlsrv':
                case 'dblib':
                case 'mssql':
                    $stmt = $pdo->prepare('SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = :tabla');
                    $stmt->execute([':tabla' => $tabla]);
                    $cache[$tabla] = ((int) $stmt->fetchColumn()) > 0;

                    return $cache[$tabla];
                case 'sqlite':
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM sqlite_master WHERE type = 'table' AND name = :tabla");
                    $stmt->execute([':tabla' => $tabla]);
                    $cache[$tabla] = ((int) $stmt->fetchColumn()) > 0;

                    return $cache[$tabla];
                default:
                    $sql = sprintf('SELECT 1 FROM %s LIMIT 1', $tabla);
                    $stmt = $pdo->query($sql);
                    $cache[$tabla] = $stmt !== false;

                    return $cache[$tabla];
            }
        } catch (\Throwable $exception) {
            // Ignorar y continuar con el valor por defecto
        }

        $cache[$tabla] = false;

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
                $ordenSql = $model->construirOrdenImagenes();
                $selectImagen = ', (SELECT pi.ruta FROM ' . $tablaImagenes . ' pi WHERE pi.producto_id = p.id ORDER BY ' . $ordenSql . ' LIMIT 1) AS imagen_principal';
            }

            $stmt = $db->prepare(
                'SELECT DISTINCT p.*' . $selectImagen . ' FROM productos p ' .
                'INNER JOIN producto_subcategoria ps ON ps.producto_id = p.id ' .
                'INNER JOIN subcategorias s ON s.id = ps.subcategoria_id ' .
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
                $ordenSqlImagen = $model->construirOrdenImagenes();
                $selectImagen = ', (SELECT pi.ruta FROM ' . $tablaImagenes . ' pi WHERE pi.producto_id = p.id ORDER BY ' . $ordenSqlImagen . ' LIMIT 1) AS imagen_principal';
            }

            $stmt = $db->prepare(
                'SELECT DISTINCT p.*' . $selectImagen . ' FROM productos p ' .
                'INNER JOIN producto_subcategoria ps ON ps.producto_id = p.id ' .
                'INNER JOIN subcategorias s ON s.id = ps.subcategoria_id ' .
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
                $ordenSql = $model->construirOrdenImagenes();
                $selectImagen = ', (SELECT pi.ruta FROM ' . $tablaImagenes . ' pi WHERE pi.producto_id = p.id ORDER BY ' . $ordenSql . ' LIMIT 1) AS imagen_principal';
            }

            $stmt = $db->prepare(
                'SELECT DISTINCT p.*' . $selectImagen . ' FROM productos p ' .
                'INNER JOIN producto_subcategoria ps ON ps.producto_id = p.id ' .
                'INNER JOIN subcategorias s ON s.id = ps.subcategoria_id ' .
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
                $ordenSql = $model->construirOrdenImagenes();
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
