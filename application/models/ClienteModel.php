<?php

declare(strict_types=1);

final class ClienteModel
{
    public static function obtenerPorEmail(string $email): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM clientes WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);

        $cliente = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $cliente === false ? null : $cliente;
    }

    public static function crear(array $data): int
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            'INSERT INTO clientes (nombre, apellidos, email, telefono, direccion, distrito, referencia)
             VALUES (:nombre, :apellidos, :email, :telefono, :direccion, :distrito, :referencia)'
        );
        $stmt->execute($data);

        return (int) $pdo->lastInsertId();
    }

    public static function obtenerOcrear(array $data): int
    {
        $cliente = self::obtenerPorEmail($data['email']);
        if ($cliente !== null) {
            return (int) $cliente['id'];
        }

        return self::crear($data);
    }
}
