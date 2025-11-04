<?php

declare(strict_types=1);

final class CategoriaModel
{
    public function obtenerCategoriasConSubcategorias(): array
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->query(
                'SELECT c.id, c.nombre, c.slug, c.descripcion, c.activo, '
                . 's.id AS sub_id, s.nombre AS sub_nombre, s.slug AS sub_slug, s.activo AS sub_activo '
                . 'FROM categorias c '
                . 'LEFT JOIN subcategorias s ON s.categoria_id = c.id '
                . 'WHERE c.activo = 1 '
                . 'ORDER BY c.nombre ASC, s.nombre ASC'
            );
            $filas = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $exception) {
            return [];
        }

        $categorias = [];

        foreach ($filas as $fila) {
            $categoriaId = (int) ($fila['id'] ?? 0);

            if ($categoriaId === 0) {
                continue;
            }

            if (!isset($categorias[$categoriaId])) {
                $categorias[$categoriaId] = [
                    'id' => $categoriaId,
                    'nombre' => (string) ($fila['nombre'] ?? ''),
                    'slug' => (string) ($fila['slug'] ?? ''),
                    'descripcion' => (string) ($fila['descripcion'] ?? ''),
                    'subcategorias' => [],
                ];
            }

            $subcategoriaId = $fila['sub_id'] ?? null;
            $subcategoriaActiva = (int) ($fila['sub_activo'] ?? 0) === 1;

            if ($subcategoriaId === null || !$subcategoriaActiva) {
                continue;
            }

            $categorias[$categoriaId]['subcategorias'][] = [
                'id' => (int) $subcategoriaId,
                'nombre' => (string) ($fila['sub_nombre'] ?? ''),
                'slug' => (string) ($fila['sub_slug'] ?? ''),
            ];
        }

        return array_values($categorias);
    }

    public function obtenerCategoriaPorSlug(string $slug): ?array
    {
        $slug = trim($slug);

        if ($slug === '') {
            return null;
        }

        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare('SELECT * FROM categorias WHERE slug = :slug AND activo = 1 LIMIT 1');
            $stmt->execute([':slug' => $slug]);
            $categoria = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($categoria === false) {
                return null;
            }

            $categoria['id'] = (int) ($categoria['id'] ?? 0);
            $categoria['subcategorias'] = [];

            $subStmt = $pdo->prepare(
                'SELECT id, nombre, slug FROM subcategorias WHERE categoria_id = :categoria AND activo = 1 ORDER BY nombre ASC'
            );
            $subStmt->execute([':categoria' => $categoria['id']]);
            $subcategorias = $subStmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            foreach ($subcategorias as &$subcategoria) {
                $subcategoria['id'] = (int) ($subcategoria['id'] ?? 0);
                $subcategoria['nombre'] = (string) ($subcategoria['nombre'] ?? '');
                $subcategoria['slug'] = (string) ($subcategoria['slug'] ?? '');
            }

            unset($subcategoria);

            $categoria['subcategorias'] = $subcategorias;

            return $categoria;
        } catch (\Throwable $exception) {
            return null;
        }
    }
}
