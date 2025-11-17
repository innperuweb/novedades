<?php

declare(strict_types=1);

final class InfoClienteModel
{
    public static function obtenerPorSlug(string $slug): ?array
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare('SELECT * FROM info_cliente WHERE slug = :slug LIMIT 1');
            $stmt->execute([':slug' => $slug]);

            $data = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $data !== false ? $data : null;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    public static function actualizarContenido(string $slug, string $contenido): bool
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare('UPDATE info_cliente SET contenido = :contenido WHERE slug = :slug LIMIT 1');

            return $stmt->execute([
                ':contenido' => $contenido,
                ':slug' => $slug,
            ]);
        } catch (\Throwable $exception) {
            return false;
        }
    }
}
