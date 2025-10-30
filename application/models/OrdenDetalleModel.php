<?php

declare(strict_types=1);

class OrdenDetalleModel
{
    public static function crear(int $ordenId, array $items): void
    {
        if ($items === []) {
            return;
        }

        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            "INSERT INTO orden_detalle (
                orden_id, producto_id, nombre_producto, color, talla,
                cantidad, precio_unitario, subtotal
            ) VALUES (
                :orden_id, :producto_id, :nombre_producto, :color, :talla,
                :cantidad, :precio_unitario, :subtotal
            )"
        );

        foreach ($items as $item) {
            $stmt->execute([
                ':orden_id' => $ordenId,
                ':producto_id' => (int) ($item['id'] ?? 0),
                ':nombre_producto' => (string) ($item['nombre'] ?? ''),
                ':color' => (string) ($item['color'] ?? ''),
                ':talla' => (string) ($item['talla'] ?? ''),
                ':cantidad' => (int) ($item['cantidad'] ?? 0),
                ':precio_unitario' => (float) ($item['precio'] ?? 0),
                ':subtotal' => (float) (($item['precio'] ?? 0) * ($item['cantidad'] ?? 0)),
            ]);
        }
    }

    public static function obtenerPorOrden(int $ordenId): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM orden_detalle WHERE orden_id = :orden_id');
        $stmt->execute([':orden_id' => $ordenId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
