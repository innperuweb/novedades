<?php

declare(strict_types=1);

final class SubcategoriaModel
{
    public static function obtenerPorSlug(string $slug): ?array
    {
        $slug = trim($slug);

        if ($slug === '') {
            return null;
        }

        try {
            $db = Database::connect();
            $stmt = $db->prepare(
                'SELECT id, nombre, slug, categoria_id, activo ' .
                'FROM subcategorias WHERE slug = :slug LIMIT 1'
            );
            $stmt->execute([':slug' => $slug]);
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($resultado === false || (int) ($resultado['activo'] ?? 0) !== 1) {
                return null;
            }

            $resultado['id'] = (int) ($resultado['id'] ?? 0);
            $resultado['categoria_id'] = (int) ($resultado['categoria_id'] ?? 0);

            return $resultado;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    public static function obtenerPorCategoria(?int $categoriaId): array
    {
        $categoriaId = $categoriaId ?? 0;

        if ($categoriaId <= 0) {
            return [];
        }

        try {
            $db = Database::connect();
            $stmt = $db->prepare(
                'SELECT id, nombre, slug FROM subcategorias ' .
                'WHERE categoria_id = :categoria AND activo = 1 ORDER BY nombre ASC'
            );
            $stmt->execute([':categoria' => $categoriaId]);
            $subcategorias = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $exception) {
            return [];
        }

        foreach ($subcategorias as &$subcategoria) {
            $subcategoria['id'] = (int) ($subcategoria['id'] ?? 0);
            $subcategoria['nombre'] = (string) ($subcategoria['nombre'] ?? '');
            $subcategoria['slug'] = (string) ($subcategoria['slug'] ?? '');
        }
        unset($subcategoria);

        return $subcategorias;
    }
}
