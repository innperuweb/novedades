<?php

declare(strict_types=1);

class OrdenModel
{
    public static function crearOrden(array $data, array $productos): int
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare(
            "INSERT INTO ordenes 
            (id_cliente, nro_orden, metodo_envio, costo_envio, metodo_pago, subtotal, total, estado)
            VALUES (:id_cliente, :nro_orden, :metodo_envio, :costo_envio, :metodo_pago, :subtotal, :total, 'Pendiente')"
        );
        $stmt->execute($data);
        $idOrden = (int) $pdo->lastInsertId();

        foreach ($productos as $item) {
            $idProducto = isset($item['id']) ? (int) $item['id'] : null;

            if ($idProducto === null || $idProducto <= 0) {
                continue;
            }

            $cantidad = (int) ($item['cantidad'] ?? 1);
            $precio = (float) ($item['precio'] ?? 0);
            $subtotal = $cantidad * $precio;

            $detalle = $pdo->prepare(
                "INSERT INTO orden_detalle (id_orden, id_producto, color, talla, cantidad, precio_unitario, subtotal)
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $detalle->execute([
                $idOrden,
                $idProducto,
                $item['color'] ?? null,
                $item['talla'] ?? null,
                $cantidad,
                $precio,
                $subtotal,
            ]);
        }

        return $idOrden;
    }

    public static function obtenerPorCliente(int $idCliente): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM ordenes WHERE id_cliente = ? ORDER BY fecha DESC');
        $stmt->execute([$idCliente]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function obtenerOrdenCompleta(string $nroOrden): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            "SELECT o.*, c.nombre, c.apellidos, c.direccion, c.distrito, c.referencia, c.telefono
            FROM ordenes o
            JOIN clientes c ON o.id_cliente = c.id
            WHERE o.nro_orden = ?"
        );
        $stmt->execute([$nroOrden]);
        $orden = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($orden === false) {
            return null;
        }

        $detalleStmt = $pdo->prepare(
            "SELECT od.*, p.nombre
            FROM orden_detalle od
            JOIN productos p ON od.id_producto = p.id
            WHERE od.id_orden = ?"
        );
        $detalleStmt->execute([(int) $orden['id']]);
        $orden['detalle'] = $detalleStmt->fetchAll(\PDO::FETCH_ASSOC);

        return $orden;
    }

    public static function eliminarOrden(int $id): void
    {
        $pdo = Database::connect();
        $pdo->prepare('DELETE FROM orden_detalle WHERE id_orden = ?')->execute([$id]);
        $pdo->prepare('DELETE FROM ordenes WHERE id = ?')->execute([$id]);
    }
}
