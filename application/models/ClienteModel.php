<?php

declare(strict_types=1);

final class ClienteModel
{
    public static function obtenerPorId(int $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM clientes WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);

        $cliente = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $cliente === false ? null : $cliente;
    }

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
            'INSERT INTO clientes (nombre, apellidos, email, password, telefono, direccion, distrito, referencia)
             VALUES (:nombre, :apellidos, :email, :password, :telefono, :direccion, :distrito, :referencia)'
        );

        $stmt->execute([
            ':nombre' => $data['nombre'] ?? '',
            ':apellidos' => $data['apellidos'] ?? '',
            ':email' => $data['email'] ?? '',
            ':password' => $data['password'] ?? null,
            ':telefono' => $data['telefono'] ?? '',
            ':direccion' => $data['direccion'] ?? '',
            ':distrito' => $data['distrito'] ?? '',
            ':referencia' => $data['referencia'] ?? '',
        ]);

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

    public static function validarLogin(string $email, string $password): ?array
    {
        $cliente = self::obtenerPorEmail($email);

        if (
            $cliente !== null &&
            isset($cliente['password']) &&
            $cliente['password'] !== '' &&
            $cliente['password'] !== null &&
            password_verify($password, (string) $cliente['password'])
        ) {
            $pdo = Database::connect();
            $stmt = $pdo->prepare('UPDATE clientes SET fecha_ultimo_login = NOW() WHERE id = ?');
            $stmt->execute([$cliente['id']]);

            return $cliente;
        }

        return null;
    }
}
