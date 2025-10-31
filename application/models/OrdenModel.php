<?php

declare(strict_types=1);

class OrdenModel
{
    public static function crear(array $data): int
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            "INSERT INTO ordenes (
                id_cliente, nro_orden, nombre, apellidos, email, telefono, dni, direccion,
                distrito, referencia, metodo_envio, metodo_envio_texto, costo_envio,
                metodo_pago, subtotal, total, estado
            ) VALUES (
                :id_cliente, :nro_orden, :nombre, :apellidos, :email, :telefono, :dni, :direccion,
                :distrito, :referencia, :metodo_envio, :metodo_envio_texto, :costo_envio,
                :metodo_pago, :subtotal, :total, 'Pendiente'
            )"
        );
        $stmt->execute($data);

        return (int) $pdo->lastInsertId();
    }

    public static function obtenerPorNumero(string $numero): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM ordenes WHERE nro_orden = :numero LIMIT 1');
        $stmt->execute([':numero' => $numero]);

        $orden = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $orden === false ? null : $orden;
    }

    public static function obtenerPorEmail(string $email): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM ordenes WHERE email = ? ORDER BY fecha DESC');
        $stmt->execute([$email]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function obtenerPorCliente(int $idCliente): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM ordenes WHERE id_cliente = ? ORDER BY fecha DESC');
        $stmt->execute([$idCliente]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function eliminarPorNro(string $numero): bool
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('DELETE FROM ordenes WHERE nro_orden = ?');

        return $stmt->execute([$numero]);
    }
}
