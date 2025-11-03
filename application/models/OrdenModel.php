<?php

declare(strict_types=1);

class OrdenModel
{
    public static function crearOrden(array $data, array $productos): int
    {
        $pdo = Database::connect();

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO ordenes
                (id_cliente, nro_orden, nombre, apellidos, email, telefono, dni, direccion, distrito, referencia, metodo_envio, metodo_envio_texto, costo_envio, metodo_pago, subtotal, total, estado)
                VALUES (:id_cliente, :nro_orden, :nombre, :apellidos, :email, :telefono, :dni, :direccion, :distrito, :referencia, :metodo_envio, :metodo_envio_texto, :costo_envio, :metodo_pago, :subtotal, :total, 'Pendiente')"
            );

            $executed = $stmt->execute($data);

            if ($executed === false) {
                $errorInfo = $stmt->errorInfo();
                $errorMessage = '❌ Error al guardar orden: ' . implode(' | ', $errorInfo);
                error_log($errorMessage);
                echo '<pre>ERROR SQL: ';
                print_r($errorInfo);
                echo '</pre>';

                throw new \RuntimeException($errorMessage);
            }

            $idOrden = (int) $pdo->lastInsertId();

            if ($productos !== []) {
                OrdenDetalleModel::crear($idOrden, $productos);
            }

            return $idOrden;
        } catch (\PDOException $exception) {
            error_log('Error al guardar la orden: ' . $exception->getMessage());
            throw $exception;
        } catch (\Throwable $exception) {
            error_log('Error inesperado al guardar la orden: ' . $exception->getMessage());
            throw $exception;
        }
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
            JOIN productos p ON od.producto_id = p.id
            WHERE od.orden_id = ?"
        );
        $detalleStmt->execute([(int) $orden['id']]);
        $orden['detalle'] = $detalleStmt->fetchAll(\PDO::FETCH_ASSOC);

        return $orden;
    }

    public static function eliminarOrden(int $id): void
    {
        $pdo = Database::connect();
        $pdo->prepare('DELETE FROM orden_detalle WHERE orden_id = ?')->execute([$id]);
        $pdo->prepare('DELETE FROM ordenes WHERE id = ?')->execute([$id]);
    }
}
