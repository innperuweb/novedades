<?php

declare(strict_types=1);

final class AdminUsuarioModel
{
    public function listar(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query('SELECT id, nombre, email, rol, activo, creado_en, ultimo_acceso FROM admin_usuarios ORDER BY nombre');

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public function buscarPorEmail(string $email): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM admin_usuarios WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => strtolower(trim($email))]);
        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $usuario !== false ? $usuario : null;
    }

    public function obtenerPorId(int $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM admin_usuarios WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $usuario !== false ? $usuario : null;
    }

    public function crear(array $data): int
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            'INSERT INTO admin_usuarios (nombre, email, pass_hash, rol, activo, creado_en) VALUES (:nombre, :email, :pass_hash, :rol, :activo, NOW())'
        );

        $stmt->execute([
            ':nombre' => trim((string) ($data['nombre'] ?? '')),
            ':email' => strtolower(trim((string) ($data['email'] ?? ''))),
            ':pass_hash' => $data['pass_hash'],
            ':rol' => $data['rol'] ?? 'admin',
            ':activo' => $data['activo'] ?? 1,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public function actualizar(int $id, array $data): bool
    {
        $pdo = Database::connect();
        $campos = [
            'nombre' => trim((string) ($data['nombre'] ?? '')),
            'email' => strtolower(trim((string) ($data['email'] ?? ''))),
            'rol' => $data['rol'] ?? 'admin',
            'activo' => $data['activo'] ?? 1,
        ];

        $set = 'nombre = :nombre, email = :email, rol = :rol, activo = :activo';
        $params = [
            ':nombre' => $campos['nombre'],
            ':email' => $campos['email'],
            ':rol' => $campos['rol'],
            ':activo' => $campos['activo'],
            ':id' => $id,
        ];

        if (!empty($data['pass_hash'])) {
            $set .= ', pass_hash = :pass_hash';
            $params[':pass_hash'] = $data['pass_hash'];
        }

        $stmt = $pdo->prepare('UPDATE admin_usuarios SET ' . $set . ', actualizado_en = NOW() WHERE id = :id');

        return $stmt->execute($params);
    }

    public function eliminar(int $id): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('DELETE FROM admin_usuarios WHERE id = :id');

        return $stmt->execute([':id' => $id]);
    }

    public function registrarAcceso(int $id): void
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('UPDATE admin_usuarios SET ultimo_acceso = NOW() WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}
