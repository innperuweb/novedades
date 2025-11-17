<?php

declare(strict_types=1);

final class SliderModel
{
    public function listar(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query('SELECT * FROM slider_home ORDER BY id DESC');

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function obtenerPorId(int $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM slider_home WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $slider = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $slider !== false ? $slider : null;
    }

    public function crear(array $data): int
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('INSERT INTO slider_home (imagen, titulo, subtitulo, boton_url, visible, creado_en)
                       VALUES (:imagen, :titulo, :subtitulo, :boton_url, :visible, NOW())');
        $stmt->execute([
            ':imagen' => (string) ($data['imagen'] ?? ''),
            ':titulo' => (string) ($data['titulo'] ?? ''),
            ':subtitulo' => (string) ($data['subtitulo'] ?? ''),
            ':boton_url' => (string) ($data['boton_url'] ?? ''),
            ':visible' => isset($data['visible']) && (int) $data['visible'] === 1 ? 1 : 0,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public function actualizar(int $id, array $data): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('UPDATE slider_home 
                       SET imagen = :imagen, titulo = :titulo, subtitulo = :subtitulo, 
                           boton_url = :boton_url, visible = :visible 
                       WHERE id = :id');

        return $stmt->execute([
            ':imagen' => (string) ($data['imagen'] ?? ''),
            ':titulo' => (string) ($data['titulo'] ?? ''),
            ':subtitulo' => (string) ($data['subtitulo'] ?? ''),
            ':boton_url' => (string) ($data['boton_url'] ?? ''),
            ':visible' => isset($data['visible']) && (int) $data['visible'] === 1 ? 1 : 0,
            ':id' => $id,
        ]);
    }

    public function eliminar(int $id): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('DELETE FROM slider_home WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }

    public function obtenerVisibles(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM slider_home WHERE visible = 1 ORDER BY id DESC');
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}