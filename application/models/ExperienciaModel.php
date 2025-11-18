<?php

declare(strict_types=1);

final class ExperienciaModel
{
    public function listar(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query('SELECT * FROM experiencias ORDER BY orden ASC, id DESC');

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function obtenerPorId(int $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM experiencias WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $experiencia = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $experiencia !== false ? $experiencia : null;
    }

    public function crear(array $data): int
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('INSERT INTO experiencias (nombre, texto, imagen, visible, orden, creado_en) VALUES (:nombre, :texto, :imagen, :visible, :orden, NOW())');
        $stmt->execute([
            ':nombre' => (string) ($data['nombre'] ?? ''),
            ':texto' => (string) ($data['texto'] ?? ''),
            ':imagen' => (string) ($data['imagen'] ?? ''),
            ':visible' => isset($data['visible']) && (int) $data['visible'] === 1 ? 1 : 0,
            ':orden' => (int) ($data['orden'] ?? 0),
        ]);

        return (int) $pdo->lastInsertId();
    }

    public function actualizar(int $id, array $data): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('UPDATE experiencias SET nombre = :nombre, texto = :texto, imagen = :imagen, visible = :visible, orden = :orden, actualizado_en = NOW() WHERE id = :id');

        return $stmt->execute([
            ':nombre' => (string) ($data['nombre'] ?? ''),
            ':texto' => (string) ($data['texto'] ?? ''),
            ':imagen' => (string) ($data['imagen'] ?? ''),
            ':visible' => isset($data['visible']) && (int) $data['visible'] === 1 ? 1 : 0,
            ':orden' => (int) ($data['orden'] ?? 0),
            ':id' => $id,
        ]);
    }

    public function eliminar(int $id): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('DELETE FROM experiencias WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }

    public function obtenerVisibles(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM experiencias WHERE visible = 1 ORDER BY orden ASC, id DESC');
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }
}
