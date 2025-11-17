<?php

declare(strict_types=1);

final class PublicidadModel
{
    public function obtener(): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query('SELECT * FROM publicidad_home ORDER BY id DESC LIMIT 1');
        $registro = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $registro !== false ? $registro : null;
    }

    public function actualizar(array $data): bool
    {
        $pdo = Database::connect();
        $actual = $this->obtener();

        $payload = [
            ':imagen' => (string) ($data['imagen'] ?? ''),
            ':titulo' => (string) ($data['titulo'] ?? ''),
            ':subtitulo' => (string) ($data['subtitulo'] ?? ''),
            ':texto' => (string) ($data['texto'] ?? ''),
        ];

        if ($actual !== null) {
            $stmt = $pdo->prepare('UPDATE publicidad_home
                           SET imagen = :imagen, titulo = :titulo, subtitulo = :subtitulo, texto = :texto
                           WHERE id = :id');

            $payload[':id'] = (int) ($actual['id'] ?? 0);

            return $stmt->execute($payload);
        }

        $stmt = $pdo->prepare('INSERT INTO publicidad_home (imagen, titulo, subtitulo, texto, actualizado_en)
                       VALUES (:imagen, :titulo, :subtitulo, :texto, NOW())');

        return $stmt->execute($payload);
    }
}
