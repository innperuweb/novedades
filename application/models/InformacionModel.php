<?php

declare(strict_types=1);

final class InformacionModel
{
    private const CAMPOS = [
        'telefono1',
        'telefono2',
        'email',
        'facebook',
        'instagram',
        'youtube',
        'tiktok',
        'mensaje_header',
    ];

    private const CAMPOS_POR_TIPO = [
        'contacto' => ['telefono1', 'telefono2', 'email'],
        'redes'    => ['facebook', 'instagram', 'youtube', 'tiktok'],
        'header'   => ['mensaje_header'],
    ];

    public static function obtenerPorTipo(string $tipo): ?array
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare('SELECT * FROM informacion WHERE tipo = :tipo LIMIT 1');
            $stmt->execute([':tipo' => $tipo]);

            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $resultado !== false ? $resultado : null;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    public static function actualizarPorTipo(string $tipo, array $data): bool
    {
        $camposPermitidos = self::CAMPOS_POR_TIPO[$tipo] ?? [];

        if ($camposPermitidos === []) {
            return false;
        }

        $payload = [':tipo' => $tipo];
        $sets = [];

        foreach ($camposPermitidos as $campo) {
            $payload[':' . $campo] = (string) ($data[$campo] ?? '');
            $sets[] = "$campo = :$campo";
        }

        if ($sets === []) {
            return false;
        }

        $sql = 'UPDATE informacion SET ' . implode(', ', $sets) . ' WHERE tipo = :tipo';

        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare($sql);

            return $stmt->execute($payload);
        } catch (\Throwable $exception) {
            return false;
        }
    }

    public static function crearSiNoExiste(string $tipo): bool
    {
        if (self::obtenerPorTipo($tipo) !== null) {
            return true;
        }

        $valores = [
            ':tipo'           => $tipo,
            ':telefono1'      => '',
            ':telefono2'      => '',
            ':email'          => '',
            ':facebook'       => '',
            ':instagram'      => '',
            ':youtube'        => '',
            ':tiktok'         => '',
            ':mensaje_header' => '',
        ];

        $columnas = implode(', ', ['tipo', ...self::CAMPOS]);
        $placeholders = implode(', ', array_keys($valores));

        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare("INSERT INTO informacion ($columnas) VALUES ($placeholders)");

            return $stmt->execute($valores);
        } catch (\Throwable $exception) {
            return false;
        }
    }

    public static function obtenerCamposPorTipo(string $tipo): array
    {
        $campos = self::CAMPOS_POR_TIPO[$tipo] ?? [];

        return array_fill_keys($campos, '');
    }
}
