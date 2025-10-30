<?php

declare(strict_types=1);

class OrdenModel
{
    public static function crear(array $data): int
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            "INSERT INTO ordenes (
                nro_orden, nombre, apellidos, email, telefono, dni, direccion,
                distrito, referencia, metodo_envio, costo_envio, metodo_pago,
                subtotal, total, estado
            ) VALUES (
                :nro_orden, :nombre, :apellidos, :email, :telefono, :dni, :direccion,
                :distrito, :referencia, :metodo_envio, :costo_envio, :metodo_pago,
                :subtotal, :total, :estado
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
}
