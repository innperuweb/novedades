<?php

// Archivo base para desarrollo futuro del módulo Producto
// No modificar vistas ni lógica visual existente
// Conectado al router principal desde index.php

class ProductoModel
{
    private const SECCIONES_PERMITIDAS = ['tienda', 'novedades', 'ofertas', 'populares', 'por_mayor'];

    public function getAll(): array
    {
        return $this->obtenerTodos();
    }

    public function listarConPrincipalPorSeccion(?string $seccion = null): array
    {
        $pdo = Database::connect();

        $sql = "
      SELECT p.*,
             (
               SELECT CONCAT('uploads/productos/', pi.producto_id, '/', pi.nombre)
               FROM producto_imagenes pi
               WHERE pi.producto_id = p.id
               ORDER BY pi.es_principal DESC, pi.id ASC
               LIMIT 1
             ) AS ruta_principal
      FROM productos p
    ";

        $params = [];
        $where = 'WHERE p.visible = 1 AND p.estado = 1 AND p.stock >= 0';

        $seccion = $seccion !== null ? trim($seccion) : null;
        if ($seccion === '' || $seccion === 'tienda') {
            $seccion = null;
        }

        if ($seccion !== null) {
            if (!in_array($seccion, self::SECCIONES_PERMITIDAS, true)) {
                return [];
            }

            $sql .= ' INNER JOIN producto_categorias_web pcw ON pcw.producto_id = p.id';
            $where .= ' AND pcw.seccion = :sec';
            $params[':sec'] = $seccion;
        }

        $sql .= ' ' . $where . ' ORDER BY p.id DESC';

        if ($params !== []) {
            $st = $pdo->prepare($sql);
            $st->execute($params);
        } else {
            $st = $pdo->query($sql);
        }

        $productos = $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        return $this->normalizarListadoProductos($productos);
    }

    public function obtenerTodos(): array
    {
        return $this->listarConPrincipalPorSeccion(null);
    }

    public function obtenerProductosAleatorios(int $limite = 15): array
    {
        if ($limite <= 0) {
            return [];
        }

        try {
            $pdo = Database::connect();
            $sql =
                'SELECT p.id, p.nombre, '
                . "       (SELECT CONCAT('uploads/productos/', pi.producto_id, '/', pi.nombre) FROM producto_imagenes pi WHERE pi.producto_id = p.id ORDER BY pi.es_principal DESC, pi.id ASC LIMIT 1) AS ruta_principal "
                . 'FROM productos p '
                . 'WHERE p.visible = 1 AND p.estado = 1 AND p.stock > 0 '
                . 'ORDER BY RAND() '
                . 'LIMIT ' . (int) $limite;
            $stmt = $pdo->query($sql);

            $productos = $stmt !== false ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
        } catch (\Throwable $exception) {
            return [];
        }

        foreach ($productos as &$producto) {
            $producto['id'] = (int) ($producto['id'] ?? 0);
            $producto['nombre'] = trim((string) ($producto['nombre'] ?? ''));
            if (isset($producto['ruta_principal'])) {
                $producto['ruta_principal'] = trim((string) $producto['ruta_principal']);
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
            if (isset($producto['ruta_principal'])) {
                $producto['ruta_principal'] = trim((string) $producto['ruta_principal']);
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

    public function obtenerImagenesPorProducto(int $productoId): array
    {
        if ($productoId <= 0) {
            return [];
        }

        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare(
                'SELECT id, producto_id, nombre, ruta, es_principal, orden, creado_en '
                    . 'FROM producto_imagenes WHERE producto_id = :producto '
                    . 'ORDER BY COALESCE(orden, 0) ASC, id ASC'
            );
            $stmt->execute([':producto' => $productoId]);
            $imagenes = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $exception) {
            return [];
        }

        foreach ($imagenes as &$imagen) {
            $imagen['id'] = (int) ($imagen['id'] ?? 0);
            $imagen['producto_id'] = (int) ($imagen['producto_id'] ?? 0);
            $imagen['es_principal'] = (int) ($imagen['es_principal'] ?? 0);
            $imagen['orden'] = (int) ($imagen['orden'] ?? 0);
            $imagen['ruta'] = trim((string) ($imagen['ruta'] ?? ''));
            $imagen['nombre'] = trim((string) ($imagen['nombre'] ?? ''));
        }
        unset($imagen);

        return $imagenes;
    }

    public function obtenerImagenPrincipalRuta(int $productoId): ?string
    {
        if ($productoId <= 0) {
            return null;
        }

        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare(
                'SELECT ruta FROM producto_imagenes WHERE producto_id = ? ORDER BY es_principal DESC, id ASC LIMIT 1'
            );
            $stmt->execute([$productoId]);
            $ruta = $stmt->fetchColumn();

            if ($ruta === false) {
                return null;
            }

            $ruta = trim((string) $ruta);

            return $ruta !== '' ? $ruta : null;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    public function obtenerImagenPrincipalURL(int $productoId): string
    {
        $placeholder = base_url('public/assets/img/no-image.jpg');

        $ruta = $this->obtenerImagenPrincipalRuta($productoId);
        if ($ruta === null) {
            return $placeholder;
        }

        $ruta = trim($ruta);
        $ruta = str_replace('\\', '/', $ruta);
        $ruta = trim($ruta, '/');
        if ($ruta === '') {
            return $placeholder;
        }

        $directory = dirname($ruta);
        $directory = ($directory === '.' || $directory === DIRECTORY_SEPARATOR) ? '' : $directory;
        $filename = basename($ruta);

        if ($filename === '' || $filename === '.' || $filename === '..') {
            return $placeholder;
        }

        $relativeDir = '';
        if ($directory !== '') {
            $normalizedDir = str_replace('\\', '/', $directory);
            $relativeDir = trim($normalizedDir, '/');
        }

        $uploadsBase = 'uploads/productos';
        $uploadsUrlBase = rtrim(base_url($uploadsBase), '/');
        $encodedFilename = rawurlencode($filename);
        $url = $uploadsUrlBase . '/' . ($relativeDir !== '' ? $relativeDir . '/' : '') . $encodedFilename;

        $relativePath = $uploadsBase . '/' . ($relativeDir !== '' ? $relativeDir . '/' : '') . $filename;
        $relativePathNormalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        if ($documentRoot !== '') {
            $documentRoot = rtrim((string) $documentRoot, '/\\');
            $baseUrlPath = parse_url(base_url(), PHP_URL_PATH);
            $baseUrlPath = $baseUrlPath !== null ? trim($baseUrlPath, '/') : '';
            $docRelative = $baseUrlPath !== '' ? $baseUrlPath . '/' . trim($relativePath, '/') : trim($relativePath, '/');
            $docRelative = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $docRelative);
            $documentPath = $documentRoot . DIRECTORY_SEPARATOR . $docRelative;

            if (is_file($documentPath)) {
                return $url;
            }
        }

        $projectRoot = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 2);
        $projectRoot = rtrim($projectRoot, '/\\');
        $projectPath = $projectRoot . DIRECTORY_SEPARATOR . $relativePathNormalized;

        if (is_file($projectPath)) {
            return $url;
        }

        return $placeholder;
    }

    public function urlImagenPrincipalDeFila(array $fila): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base = rtrim("$scheme://$host", '/') . '/novedades';

        $uploadsRel = '/uploads/productos';
        $placeholder = $base . '/public/assets/img/no-image.jpg';

        $ruta = $fila['ruta_principal'] ?? null;
        if (!$ruta) {
            $productoId = isset($fila['id']) ? (int) $fila['id'] : 0;
            if ($productoId > 0) {
                $ruta = $this->obtenerImagenPrincipalRuta($productoId);
            }
        }

        if (!$ruta) {
            return $placeholder;
        }

        $ruta = str_replace('\\', '/', (string) $ruta);
        $ruta = trim($ruta);
        if ($ruta === '') {
            return $placeholder;
        }

        $ruta = trim($ruta, '/');
        if (strpos($ruta, $uploadsRel) !== false) {
            $pos = strpos($ruta, $uploadsRel);
            if ($pos !== false) {
                $ruta = ltrim(substr($ruta, $pos + strlen($uploadsRel)), '/');
            }
        }

        $dir = dirname($ruta);
        $dir = ($dir === '.' || $dir === DIRECTORY_SEPARATOR) ? '' : $dir;
        $file = basename($ruta);

        if ($file === '' || $file === '.' || $file === '..') {
            return $placeholder;
        }

        $encodedFile = rawurlencode($file);
        $dirSegment = $dir !== '' ? trim(str_replace('\\', '/', $dir), '/') . '/' : '';
        $url = $base . $uploadsRel . '/' . $dirSegment . $encodedFile;

        $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\');
        $local = $docRoot . '/novedades' . $uploadsRel . '/' . $dirSegment . $file;

        if ($docRoot !== '' && is_file($local)) {
            return $url;
        }

        $projectRoot = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 2);
        $projectRoot = rtrim($projectRoot, '/\\');
        $relativeLocal = 'uploads/productos/' . $dirSegment . $file;
        $projectPath = $projectRoot . DIRECTORY_SEPARATOR . trim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativeLocal), DIRECTORY_SEPARATOR);

        return is_file($projectPath) ? $url : $placeholder;
    }

    public function obtenerImagenPrincipal(int $producto_id): ?string
    {
        $ruta = $this->obtenerImagenPrincipalRuta($producto_id);

        return $ruta ?? 'no-image.jpg';
    }



    public function buscarProductos(string $query): array
    {
        if ($query === '') {
            return [];
        }

        try {
            $pdo = Database::connect();

            $sql = "SELECT p.*, 
                       (SELECT CONCAT('uploads/productos/', pi.producto_id, '/', pi.nombre)
                        FROM producto_imagenes pi
                        WHERE pi.producto_id = p.id
                        ORDER BY pi.es_principal DESC, pi.id ASC
                        LIMIT 1) AS ruta_principal
                FROM productos p
                WHERE (LOWER(p.nombre) LIKE :q1 
                       OR CAST(p.id AS CHAR) LIKE :q2)
                  AND p.visible = 1 
                  AND p.estado = 1 
                  AND p.stock >= 0
                ORDER BY p.id DESC";

            $stmt = $pdo->prepare($sql);

            // Vincula ambas variables
            $like = '%' . mb_strtolower($query, 'UTF-8') . '%';
            $stmt->bindValue(':q1', $like, \PDO::PARAM_STR);
            $stmt->bindValue(':q2', $like, \PDO::PARAM_STR);

            $stmt->execute();
            $resultados = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            // 🔍 Logs
            error_log("🧾 Filas devueltas: " . count($resultados));
            if (!empty($resultados)) {
                error_log("🧩 Primer resultado: " . json_encode($resultados[0], JSON_UNESCAPED_UNICODE));
            }

            foreach ($resultados as &$r) {
                $r['nombre'] = trim((string)($r['nombre'] ?? ''));
                $r['precio'] = (float)($r['precio'] ?? 0);
                $r['ruta_principal'] = trim((string)($r['ruta_principal'] ?? ''));
            }

            return $resultados;
        } catch (\Throwable $e) {
            error_log("❌ Error en buscarProductos: " . $e->getMessage());
            return [];
        }
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
        $partes = array_filter($partes, static fn($item): bool => $item !== '');

        return array_values(array_unique($partes));
    }

    private function limpiarOpciones($valor): array
    {
        if (!is_array($valor)) {
            return [];
        }

        $items = array_map(static fn($item): string => trim((string) $item), $valor);
        $items = array_filter($items, static fn($item): bool => $item !== '');

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
                'SELECT DISTINCT p.*, ' .
                    "       (SELECT CONCAT('uploads/productos/', pi.producto_id, '/', pi.nombre) FROM producto_imagenes pi WHERE pi.producto_id = p.id ORDER BY pi.es_principal DESC, pi.id ASC LIMIT 1) AS ruta_principal "
                    . 'FROM productos p '
                    . 'INNER JOIN producto_subcategoria ps ON ps.producto_id = p.id '
                    . 'INNER JOIN subcategorias s ON s.id = ps.subcategoria_id '
                    . 'WHERE s.slug = :slug AND p.estado = 1 AND p.visible = 1 AND p.stock >= 0 ORDER BY p.id DESC'
            );
            $stmt->execute([':slug' => $slug]);

            $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            foreach ($productos as &$producto) {
                if (isset($producto['imagen'])) {
                    $producto['imagen'] = trim((string) $producto['imagen']);
                }
                if (isset($producto['ruta_principal'])) {
                    $producto['ruta_principal'] = trim((string) $producto['ruta_principal']);
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
                'SELECT DISTINCT p.*, ' .
                    "       (SELECT CONCAT('uploads/productos/', pi.producto_id, '/', pi.nombre) FROM producto_imagenes pi WHERE pi.producto_id = p.id ORDER BY pi.es_principal DESC, pi.id ASC LIMIT 1) AS ruta_principal "
                    . 'FROM productos p '
                    . 'INNER JOIN producto_subcategoria ps ON ps.producto_id = p.id '
                    . 'INNER JOIN subcategorias s ON s.id = ps.subcategoria_id '
                    . 'WHERE s.slug = :slug AND p.estado = 1 AND p.visible = 1 AND p.stock >= 0 ORDER BY ' . $ordenSQL
            );
            $stmt->execute([':slug' => $slug]);

            $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            foreach ($productos as &$producto) {
                if (isset($producto['imagen'])) {
                    $producto['imagen'] = trim((string) $producto['imagen']);
                }
                if (isset($producto['ruta_principal'])) {
                    $producto['ruta_principal'] = trim((string) $producto['ruta_principal']);
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
                'SELECT DISTINCT p.*, ' .
                    "       (SELECT CONCAT('uploads/productos/', pi.producto_id, '/', pi.nombre) FROM producto_imagenes pi WHERE pi.producto_id = p.id ORDER BY pi.es_principal DESC, pi.id ASC LIMIT 1) AS ruta_principal "
                    . 'FROM productos p '
                    . 'INNER JOIN producto_subcategoria ps ON ps.producto_id = p.id '
                    . 'INNER JOIN subcategorias s ON s.id = ps.subcategoria_id '
                    . 'WHERE s.slug = :slug AND p.precio BETWEEN :min AND :max '
                    . 'AND p.estado = 1 AND p.visible = 1 AND p.stock >= 0 ORDER BY p.id DESC'
            );
            $stmt->execute([':slug' => $slug, ':min' => $min, ':max' => $max]);

            $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            foreach ($productos as &$producto) {
                if (isset($producto['imagen'])) {
                    $producto['imagen'] = trim((string) $producto['imagen']);
                }
                if (isset($producto['ruta_principal'])) {
                    $producto['ruta_principal'] = trim((string) $producto['ruta_principal']);
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
        $modelo = new self();

        return $modelo->buscarProductos($termino);
    }

    public function obtenerSeccionesProducto(int $producto_id): array
    {
        if ($producto_id <= 0) {
            return [];
        }

        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare('SELECT seccion FROM producto_categorias_web WHERE producto_id = ?');
            $stmt->execute([$producto_id]);
            $secciones = $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];
        } catch (\Throwable $exception) {
            return [];
        }

        $secciones = array_map(static fn($item): string => trim((string) $item), $secciones);
        $secciones = array_values(array_intersect($secciones, self::SECCIONES_PERMITIDAS));

        return $secciones;
    }

    public function guardarSeccionesProducto(int $producto_id, array $secciones): void
    {
        if ($producto_id <= 0) {
            return;
        }

        $secciones = array_map(static fn($item): string => trim((string) $item), $secciones);
        $secciones = array_values(array_intersect($secciones, self::SECCIONES_PERMITIDAS));
        $secciones = array_values(array_unique($secciones));

        try {
            $pdo = Database::connect();
            $pdo->prepare('DELETE FROM producto_categorias_web WHERE producto_id = ?')->execute([$producto_id]);

            if ($secciones === []) {
                return;
            }

            $stmt = $pdo->prepare('INSERT INTO producto_categorias_web (producto_id, seccion) VALUES (?, ?)');
            foreach ($secciones as $seccion) {
                $stmt->execute([$producto_id, $seccion]);
            }
        } catch (\Throwable $exception) {
            // Silenciar errores para no interrumpir el flujo principal del guardado.
        }
    }

    public function obtenerPorSeccion(string $seccion): array
    {
        $seccion = trim($seccion);

        if ($seccion === '' || !in_array($seccion, self::SECCIONES_PERMITIDAS, true)) {
            return [];
        }

        return $this->listarConPrincipalPorSeccion($seccion === 'tienda' ? null : $seccion);
    }

    private function normalizarListadoProductos(array $productos): array
    {
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
            if (array_key_exists('ruta_principal', $producto)) {
                $producto['ruta_principal'] = trim((string) ($producto['ruta_principal'] ?? ''));
            }
        }
        unset($producto);

        return $productos;
    }
}
