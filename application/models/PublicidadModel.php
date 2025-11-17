<?php

declare(strict_types=1);

final class PublicidadModel
{
    public function obtenerTodas(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query('SELECT * FROM publicidad_home ORDER BY posicion ASC');
        $registros = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $publicidades = [];
        foreach ($registros as $registro) {
            $posicion = (int) ($registro['posicion'] ?? 0);
            $publicidades[$posicion] = $registro;
        }

        return $publicidades;
    }

    public function obtenerPorPosicion(int $posicion): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM publicidad_home WHERE posicion = :posicion LIMIT 1');
        $stmt->execute([':posicion' => $posicion]);
        $registro = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $registro !== false ? $registro : null;
    }

    public function actualizarPorPosicion(int $posicion, array $data): bool
    {
        $pdo = Database::connect();
        $actual = $this->obtenerPorPosicion($posicion);

        $payload = [
            ':posicion' => $posicion,
            ':imagen' => (string) ($data['imagen'] ?? ''),
            ':titulo' => (string) ($data['titulo'] ?? ''),
            ':subtitulo' => (string) ($data['subtitulo'] ?? ''),
            ':texto' => (string) ($data['texto'] ?? ''),
        ];

        if ($actual !== null) {
            $stmt = $pdo->prepare('UPDATE publicidad_home
                           SET imagen = :imagen, titulo = :titulo, subtitulo = :subtitulo, texto = :texto
                           WHERE posicion = :posicion');

            return $stmt->execute($payload);
        }

        $stmt = $pdo->prepare('INSERT INTO publicidad_home (posicion, imagen, titulo, subtitulo, texto, actualizado_en)
                       VALUES (:posicion, :imagen, :titulo, :subtitulo, :texto, NOW())');

        return $stmt->execute($payload);
    }
}
