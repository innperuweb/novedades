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
            $producto['colores'] = $this->decodificarCampoJson($producto['colores'] ?? null);
            $producto['tallas'] = $this->decodificarCampoJson($producto['tallas'] ?? null);
            $producto['subcategorias'] = [];
            $producto['subcategorias_nombres'] = [];
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

        $producto['colores'] = $this->decodificarCampoJson($producto['colores'] ?? null);
        $producto['tallas'] = $this->decodificarCampoJson($producto['tallas'] ?? null);
        $producto['subcategorias'] = $this->obtenerSubcategoriasAsignadas($id);

        return $producto;
    }

    public function crear(array $data, array $subcategorias): int
    {
        $pdo = Database::connect();
        $payload = $this->mapearDatos($data, false);
        $columns = array_keys($payload);
        $placeholders = array_map(static fn ($column): string => ':' . $column, $columns);
        $sql = 'INSERT INTO productos (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';

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
            return $valor;
        }

        $decoded = json_decode((string) $valor, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        $partes = array_map('trim', explode(',', (string) $valor));
        $partes = array_filter($partes, static fn ($item): bool => $item !== '');

        return array_values($partes);
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
            'descripcion' => (string) ($data['descripcion'] ?? ''),
            'precio' => (float) ($data['precio'] ?? 0),
            'stock' => isset($data['stock']) ? (int) $data['stock'] : 0,
            'sku' => trim((string) ($data['sku'] ?? '')),
            'imagen' => trim((string) ($data['imagen'] ?? '')),
            'activo' => isset($data['activo']) && $data['activo'] ? 1 : 0,
        ];

        if (isset($data['colores'])) {
            $colores = $this->normalizarLista($data['colores']);
            $mapa['colores'] = json_encode($colores, JSON_UNESCAPED_UNICODE);
        }

        if (isset($data['tallas'])) {
            $tallas = $this->normalizarLista($data['tallas']);
            $mapa['tallas'] = json_encode($tallas, JSON_UNESCAPED_UNICODE);
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

    private function sincronizarSubcategorias(int $productoId, array $subcategorias): void
    {
        if (!$this->tablaExiste('producto_subcategoria')) {
            return;
        }

        $pdo = Database::connect();
        $pdo->prepare('DELETE FROM producto_subcategoria WHERE producto_id = :producto')
            ->execute([':producto' => $productoId]);

        if ($subcategorias === []) {
            return;
        }

        $stmt = $pdo->prepare('INSERT INTO producto_subcategoria (producto_id, subcategoria_id) VALUES (:producto, :subcategoria)');
        foreach ($subcategorias as $subcategoriaId) {
            $stmt->execute([
                ':producto' => $productoId,
                ':subcategoria' => (int) $subcategoriaId,
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

    private function tablaExiste(string $tabla): bool
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

    private function generarSlug(string $texto): string
    {
        $texto = strtolower(trim($texto));
        $texto = preg_replace('/[^a-z0-9]+/i', '-', $texto ?? '') ?? '';
        $texto = trim($texto, '-');

        return $texto !== '' ? $texto : uniqid('producto_', true);
    }
}
