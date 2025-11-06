<?php

declare(strict_types=1);

final class AdminProductoModel extends ProductoModel
{
    private ?array $tablaColumnas = null;

    public function obtenerTodos(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query('SELECT * FROM productos ORDER BY id DESC');
        $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        foreach ($productos as &$producto) {
            $producto['colores'] = $this->sanitizarOpciones($this->decodificarCampoJson($producto['colores'] ?? null));
            $producto['tallas'] = $this->sanitizarOpciones($this->decodificarCampoJson($producto['tallas'] ?? null));
            $producto['subcategorias'] = [];
            $producto['subcategorias_nombres'] = [];
            $producto['stock'] = (int) ($producto['stock'] ?? 0);
            $producto['sku'] = trim((string) ($producto['sku'] ?? ''));
            $producto['visible'] = (int) ($producto['visible'] ?? ($producto['activo'] ?? 0));
            $producto['estado'] = (int) ($producto['estado'] ?? ($producto['activo'] ?? 0));
            $producto['activo'] = ($producto['visible'] === 1 && $producto['estado'] === 1) ? 1 : 0;
        }
        unset($producto);

        if ($productos !== [] && $this->tablaExiste('producto_subcategoria') && $this->tablaExiste('subcategorias')) {
            $ids = array_column($productos, 'id');
            $relaciones = $this->obtenerRelacionesSubcategorias($ids);
            foreach ($productos as &$producto) {
                $productoId = (int) ($producto['id'] ?? 0);
                if (isset($relaciones[$productoId])) {
                    $producto['subcategorias'] = $relaciones[$productoId]['ids'] ?? [];
                    $producto['subcategorias_nombres'] = $relaciones[$productoId]['nombres'] ?? [];
                }
            }
            unset($producto);
        }

        return $productos;
    }

    public function obtenerProducto(int $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM productos WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $producto = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($producto === false) {
            return null;
        }

        $producto['colores'] = $this->sanitizarOpciones($this->decodificarCampoJson($producto['colores'] ?? null));
        $producto['tallas'] = $this->sanitizarOpciones($this->decodificarCampoJson($producto['tallas'] ?? null));
        $producto['subcategorias'] = $this->obtenerSubcategoriasAsignadas($id);
        $producto['stock'] = (int) ($producto['stock'] ?? 0);
        $producto['sku'] = trim((string) ($producto['sku'] ?? ''));
        $producto['visible'] = (int) ($producto['visible'] ?? ($producto['activo'] ?? 0));
        $producto['estado'] = (int) ($producto['estado'] ?? ($producto['activo'] ?? 0));
        $producto['activo'] = ($producto['visible'] === 1 && $producto['estado'] === 1) ? 1 : 0;

        return $producto;
    }

    public function crear(array $data, array $subcategorias): int
    {
        $pdo = Database::connect();
        $payload = $this->mapearDatos($data, false);
        $sets = [];
        foreach ($payload as $column => $_) {
            $sets[] = $column . ' = :' . $column;
        }

        if ($sets === []) {
            throw new \RuntimeException('No hay datos suficientes para crear el producto.');
        }

        $sql = 'INSERT INTO productos SET ' . implode(', ', $sets);

        $stmt = $pdo->prepare($sql);
        $stmt->execute($payload);

        $id = (int) $pdo->lastInsertId();
        $this->sincronizarSubcategorias($id, $subcategorias);

        return $id;
    }

    public function actualizarProducto(int $id, array $data, array $subcategorias): bool
    {
        $payload = $this->mapearDatos($data, true);
        $pdo = Database::connect();

        if ($payload === []) {
            $this->sincronizarSubcategorias($id, $subcategorias);

            return true;
        }

        $sets = [];
        foreach ($payload as $column => $_) {
            $sets[] = $column . ' = :' . $column;
        }

        $payload['id'] = $id;

        $sql = 'UPDATE productos SET ' . implode(', ', $sets) . ' WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $resultado = $stmt->execute($payload);

        $this->sincronizarSubcategorias($id, $subcategorias);

        return $resultado;
    }

    public function eliminarProducto(int $id): bool
    {
        $pdo = Database::connect();

        if ($this->tablaExiste('producto_subcategoria')) {
            $pdo->prepare('DELETE FROM producto_subcategoria WHERE producto_id = :id')->execute([':id' => $id]);
        }

        $stmt = $pdo->prepare('DELETE FROM productos WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }

    public function obtenerSubcategoriasAsignadas(int $productoId): array
    {
        if (!$this->tablaExiste('producto_subcategoria')) {
            return [];
        }

        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT subcategoria_id FROM producto_subcategoria WHERE producto_id = :id');
        $stmt->execute([':id' => $productoId]);

        return array_map('intval', $stmt->fetchAll(\PDO::FETCH_COLUMN));
    }

    private function decodificarCampoJson($valor): array
    {
        if ($valor === null || $valor === '') {
            return [];
        }

        if (is_array($valor)) {
            $items = $valor;
        } else {
            $valorCadena = (string) $valor;
            $decoded = json_decode($valorCadena, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $items = $decoded;
            } else {
                $items = preg_split('/[;,]+/', $valorCadena) ?: [];
            }
        }

        $items = array_map(static fn ($item): string => trim((string) $item), $items);
        $items = array_filter($items, static fn ($item): bool => $item !== '');

        return array_values($items);
    }

    private function obtenerRelacionesSubcategorias(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $pdo = Database::connect();
        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare(
            'SELECT ps.producto_id, ps.subcategoria_id, s.nombre '
            . 'FROM producto_subcategoria ps '
            . 'JOIN subcategorias s ON s.id = ps.subcategoria_id '
            . 'WHERE ps.producto_id IN (' . $in . ')'
        );
        $stmt->execute($ids);

        $relaciones = [];
        while ($fila = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $productoId = (int) ($fila['producto_id'] ?? 0);
            $relaciones[$productoId]['ids'][] = (int) ($fila['subcategoria_id'] ?? 0);
            $relaciones[$productoId]['nombres'][] = (string) ($fila['nombre'] ?? '');
        }

        foreach ($relaciones as &$relacion) {
            if (isset($relacion['ids'])) {
                $relacion['ids'] = array_values(array_unique($relacion['ids']));
            }
            if (isset($relacion['nombres'])) {
                $relacion['nombres'] = array_values(array_unique($relacion['nombres']));
            }
        }
        unset($relacion);

        return $relaciones;
    }

    private function mapearDatos(array $data, bool $actualizacion): array
    {
        $columnas = $this->obtenerColumnas();
        $resultado = [];

        $mapa = [
            'nombre' => trim((string) ($data['nombre'] ?? '')),
            'marca' => trim((string) ($data['marca'] ?? '')),
            'descripcion' => (string) ($data['descripcion'] ?? ''),
            'precio' => (float) ($data['precio'] ?? 0),
            'stock' => max(0, (int) ($data['stock'] ?? 0)),
            'sku' => trim((string) ($data['sku'] ?? '')),
            'visible' => isset($data['visible']) ? (int) $data['visible'] : 0,
            'estado' => isset($data['estado']) ? (int) $data['estado'] : (isset($data['visible']) ? (int) $data['visible'] : 0),
        ];

        // La columna legacy `imagen`/`imagen_url` se mantiene solo por compatibilidad histórica
        // y no debe actualizarse ni leerse desde el backend nuevo.

        if (array_key_exists('tabla_tallas', $data)) {
            $valorTabla = $data['tabla_tallas'] ?? '';
            $mapa['tabla_tallas'] = $valorTabla === null ? null : trim((string) $valorTabla);
        }

        if (array_key_exists('color', $data)) {
            $mapa['color'] = trim((string) ($data['color'] ?? ''));
        }

        if (array_key_exists('talla', $data)) {
            $mapa['talla'] = trim((string) ($data['talla'] ?? ''));
        }

        if (isset($data['colores'])) {
            $colores = $this->normalizarLista($data['colores']);
            $mapa['colores'] = json_encode($colores, JSON_UNESCAPED_UNICODE);
        }

        if (isset($data['tallas'])) {
            $tallas = $this->normalizarLista($data['tallas']);
            $mapa['tallas'] = json_encode($tallas, JSON_UNESCAPED_UNICODE);
        }

        if (in_array('activo', $columnas, true)) {
            $mapa['activo'] = $mapa['estado'] ?? ($mapa['visible'] ?? 0);
        }

        if (in_array('slug', $columnas, true)) {
            $mapa['slug'] = $this->generarSlug($mapa['nombre'] ?? '');
        }

        $fechaActual = date('Y-m-d H:i:s');
        if (!$actualizacion && in_array('creado_en', $columnas, true)) {
            $mapa['creado_en'] = $fechaActual;
        }
        if (in_array('actualizado_en', $columnas, true)) {
            $mapa['actualizado_en'] = $fechaActual;
        }

        foreach ($mapa as $columna => $valor) {
            if (in_array($columna, $columnas, true)) {
                $resultado[$columna] = $valor;
            }
        }

        return $resultado;
    }

    private function normalizarLista($valor): array
    {
        if (is_array($valor)) {
            $items = $valor;
        } else {
            $items = preg_split('/[;,]+/', (string) $valor) ?: [];
        }

        $items = array_map(static function ($item): string {
            return trim((string) $item);
        }, $items);
        $items = array_filter($items, static fn ($item): bool => $item !== '');

        return array_values(array_unique($items));
    }

    private function sanitizarOpciones(array $items): array
    {
        $items = array_map(static fn ($valor): string => trim((string) $valor), $items);
        $items = array_filter($items, static fn ($valor): bool => $valor !== '');

        return array_values(array_unique($items));
    }

    public function limpiarTablaTallas(int $productoId): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('UPDATE productos SET tabla_tallas = NULL WHERE id = :id');

        return $stmt->execute([':id' => $productoId]);
    }

    private function sincronizarSubcategorias(int $productoId, array $subcategorias): void
    {
        if (!$this->tablaExiste('producto_subcategoria')) {
            return;
        }

        $subcategoriasLimpias = array_values(array_unique(array_filter(array_map(
            static fn ($valor): int => (int) $valor,
            $subcategorias
        ), static fn (int $valor): bool => $valor > 0)));

        $pdo = Database::connect();

        if ($subcategoriasLimpias === []) {
            $pdo->prepare('DELETE FROM producto_subcategoria WHERE producto_id = :producto')
                ->execute([':producto' => $productoId]);

            return;
        }

        $placeholders = [];
        $params = [':producto' => $productoId];
        foreach ($subcategoriasLimpias as $indice => $subcategoriaId) {
            $placeholder = ':sub' . $indice;
            $placeholders[] = $placeholder;
            $params[$placeholder] = $subcategoriaId;
        }

        $sqlEliminar = 'DELETE FROM producto_subcategoria WHERE producto_id = :producto'
            . ' AND subcategoria_id NOT IN (' . implode(',', $placeholders) . ')';
        $pdo->prepare($sqlEliminar)->execute($params);

        $stmt = $pdo->prepare(
            'INSERT INTO producto_subcategoria (producto_id, subcategoria_id) '
            . 'VALUES (:producto, :subcategoria) '
            . 'ON DUPLICATE KEY UPDATE subcategoria_id = VALUES(subcategoria_id)'
        );

        foreach ($subcategoriasLimpias as $subcategoriaId) {
            $stmt->execute([
                ':producto' => $productoId,
                ':subcategoria' => $subcategoriaId,
            ]);
        }
    }

    private function obtenerColumnas(): array
    {
        if ($this->tablaColumnas !== null) {
            return $this->tablaColumnas;
        }

        $pdo = Database::connect();
        try {
            $stmt = $pdo->query('DESCRIBE productos');
            $this->tablaColumnas = $stmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];
        } catch (\Throwable $exception) {
            $this->tablaColumnas = [];
        }

        return $this->tablaColumnas;
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

    private function generarSlug(string $texto): string
    {
        $texto = strtolower(trim($texto));
        $texto = preg_replace('/[^a-z0-9]+/i', '-', $texto ?? '') ?? '';
        $texto = trim($texto, '-');

        return $texto !== '' ? $texto : uniqid('producto_', true);
    }
}
