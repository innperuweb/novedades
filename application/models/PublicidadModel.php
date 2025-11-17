<?php

declare(strict_types=1);

final class PublicidadModel
{
    /**
     * Obtiene todas las publicidades indexadas por posición.
     * Retorna un array con claves 1,2,3,4 aunque no existan registros todavía.
     */
    public function obtenerTodas(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query('SELECT * FROM publicidad_home ORDER BY posicion ASC');
        $registros = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        $publicidades = [];

        // Cargar registros existentes indexados por posición
        foreach ($registros as $registro) {
            $pos = (int)($registro['posicion'] ?? 0);
            if ($pos > 0) {
                $publicidades[$pos] = $registro;
            }
        }

        // Asegurar que todas las posiciones existan aunque estén vacías
        for ($pos = 1; $pos <= 4; $pos++) {
            if (!isset($publicidades[$pos])) {
                $publicidades[$pos] = [
                    'posicion'  => $pos,
                    'imagen'    => '',
                    'titulo'    => '',
                    'subtitulo' => '',
                    'texto'     => '',
                ];
            }
        }

        return $publicidades;
    }

    /**
     * Obtiene una publicidad por posición.
     */
    public function obtenerPorPosicion(int $posicion): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM publicidad_home WHERE posicion = :posicion LIMIT 1');
        $stmt->execute([':posicion' => $posicion]);
        $registro = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $registro !== false ? $registro : null;
    }

    /**
     * Inserta o actualiza la publicidad por posición.
     */
    public function actualizarPorPosicion(int $posicion, array $data): bool
    {
        $pdo = Database::connect();
        $actual = $this->obtenerPorPosicion($posicion);

        $payload = [
            ':posicion'  => $posicion,
            ':imagen'    => (string)($data['imagen'] ?? ''),
            ':titulo'    => (string)($data['titulo'] ?? ''),
            ':subtitulo' => (string)($data['subtitulo'] ?? ''),
            ':texto'     => (string)($data['texto'] ?? ''),
        ];

        if ($actual !== null) {
            // Update
            $stmt = $pdo->prepare(
                'UPDATE publicidad_home
                 SET imagen = :imagen, titulo = :titulo, subtitulo = :subtitulo, texto = :texto, actualizado_en = NOW()
                 WHERE posicion = :posicion'
            );
            return $stmt->execute($payload);
        }

        // Insert
        $stmt = $pdo->prepare(
            'INSERT INTO publicidad_home (posicion, imagen, titulo, subtitulo, texto, actualizado_en)
             VALUES (:posicion, :imagen, :titulo, :subtitulo, :texto, NOW())'
        );

        return $stmt->execute($payload);
    }
}
