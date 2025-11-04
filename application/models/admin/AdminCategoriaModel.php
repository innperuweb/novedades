<?php

declare(strict_types=1);

final class AdminCategoriaModel
{
    public function listarCategorias(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query(
            'SELECT c.*, COUNT(s.id) AS total_subcategorias '
            . 'FROM categorias c '
            . 'LEFT JOIN subcategorias s ON s.categoria_id = c.id '
            . 'GROUP BY c.id '
            . 'ORDER BY c.nombre'
        );

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function obtenerCategoria(int $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM categorias WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $categoria = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $categoria !== false ? $categoria : null;
    }

    public function guardarCategoria(array $data): int
    {
        $pdo = Database::connect();

        if (!empty($data['id'])) {
            $stmt = $pdo->prepare(
                'UPDATE categorias SET nombre = :nombre, slug = :slug, descripcion = :descripcion, activo = :activo '
                . 'WHERE id = :id'
            );
            $stmt->execute([
                ':nombre' => trim((string) ($data['nombre'] ?? '')),
                ':slug' => $this->generarSlug($data['nombre'] ?? ''),
                ':descripcion' => (string) ($data['descripcion'] ?? ''),
                ':activo' => isset($data['activo']) && $data['activo'] ? 1 : 0,
                ':id' => (int) $data['id'],
            ]);

            return (int) $data['id'];
        }

        $stmt = $pdo->prepare(
            'INSERT INTO categorias (nombre, slug, descripcion, activo, creado_en) VALUES (:nombre, :slug, :descripcion, :activo, NOW())'
        );
        $stmt->execute([
            ':nombre' => trim((string) ($data['nombre'] ?? '')),
            ':slug' => $this->generarSlug($data['nombre'] ?? ''),
            ':descripcion' => (string) ($data['descripcion'] ?? ''),
            ':activo' => isset($data['activo']) && $data['activo'] ? 1 : 0,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public function eliminarCategoria(int $id): bool
    {
        $pdo = Database::connect();
        $pdo->prepare('DELETE FROM subcategorias WHERE categoria_id = :id')->execute([':id' => $id]);
        $stmt = $pdo->prepare('DELETE FROM categorias WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }

    public function listarSubcategorias(?int $categoriaId = null): array
    {
        $pdo = Database::connect();
        $sql = 'SELECT s.*, c.nombre AS categoria_nombre FROM subcategorias s JOIN categorias c ON c.id = s.categoria_id';
        $params = [];

        if ($categoriaId !== null) {
            $sql .= ' WHERE s.categoria_id = :categoria';
            $params[':categoria'] = $categoriaId;
        }

        $sql .= ' ORDER BY s.nombre';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function obtenerSubcategoria(int $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM subcategorias WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $subcategoria = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $subcategoria !== false ? $subcategoria : null;
    }

    public function guardarSubcategoria(array $data): int
    {
        $pdo = Database::connect();
        $categoriaId = (int) ($data['categoria_id'] ?? 0);

        if (!empty($data['id'])) {
            $stmt = $pdo->prepare(
                'UPDATE subcategorias SET nombre = :nombre, slug = :slug, descripcion = :descripcion, activo = :activo, categoria_id = :categoria '
                . 'WHERE id = :id'
            );
            $stmt->execute([
                ':nombre' => trim((string) ($data['nombre'] ?? '')),
                ':slug' => $this->generarSlug($data['nombre'] ?? ''),
                ':descripcion' => (string) ($data['descripcion'] ?? ''),
                ':activo' => isset($data['activo']) && $data['activo'] ? 1 : 0,
                ':categoria' => $categoriaId,
                ':id' => (int) $data['id'],
            ]);

            return (int) $data['id'];
        }

        $stmt = $pdo->prepare(
            'INSERT INTO subcategorias (categoria_id, nombre, slug, descripcion, activo, creado_en) VALUES (:categoria_id, :nombre, :slug, :descripcion, :activo, NOW())'
        );
        $stmt->execute([
            ':categoria_id' => $categoriaId,
            ':nombre' => trim((string) ($data['nombre'] ?? '')),
            ':slug' => $this->generarSlug($data['nombre'] ?? ''),
            ':descripcion' => (string) ($data['descripcion'] ?? ''),
            ':activo' => isset($data['activo']) && $data['activo'] ? 1 : 0,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public function eliminarSubcategoria(int $id): bool
    {
        $pdo = Database::connect();

        if ($this->tablaExiste('producto_subcategoria')) {
            $pdo->prepare('DELETE FROM producto_subcategoria WHERE subcategoria_id = :id')->execute([':id' => $id]);
        }

        $stmt = $pdo->prepare('DELETE FROM subcategorias WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }

    private function generarSlug(string $texto): string
    {
        $texto = strtolower(trim($texto));
        $texto = preg_replace('/[^a-z0-9]+/i', '-', $texto ?? '') ?? '';
        $texto = trim($texto, '-');

        return $texto !== '' ? $texto : uniqid('categoria_', true);
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
}
