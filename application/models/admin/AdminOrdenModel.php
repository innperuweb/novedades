<?php

declare(strict_types=1);

final class AdminOrdenModel extends OrdenModel
{
    public const ESTADOS = ['Pendiente', 'Pagado', 'Enviado', 'Entregado', 'Cancelado'];

    public static function listar(array $filtros = []): array
    {
        $pdo = Database::connect();
        $sql = 'SELECT o.*, c.nombre AS cliente_nombre, c.apellidos AS cliente_apellidos, c.email AS cliente_email '
             . 'FROM ordenes o '
             . 'LEFT JOIN clientes c ON c.id = o.id_cliente';
        $where = [];
        $params = [];

        if (!empty($filtros['estado']) && in_array($filtros['estado'], self::ESTADOS, true)) {
            $where[] = 'o.estado = :estado';
            $params[':estado'] = $filtros['estado'];
        }

        if (!empty($filtros['busqueda'])) {
            $where[] = '(o.nro_orden LIKE :search OR c.email LIKE :search OR c.nombre LIKE :search OR c.apellidos LIKE :search)';
            $params[':search'] = '%' . $filtros['busqueda'] . '%';
        }

        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY o.fecha DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public static function contarTotal(): int
    {
        $pdo = Database::connect();
        $stmt = $pdo->query('SELECT COUNT(*) FROM ordenes');

        return (int) $stmt->fetchColumn();
    }

    public static function contarPorEstado(string $estado): int
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM ordenes WHERE estado = :estado');
        $stmt->execute([':estado' => $estado]);

        return (int) $stmt->fetchColumn();
    }

    public static function totalVentas(): float
    {
        $pdo = Database::connect();
        $estados = ['Pagado', 'Enviado', 'Entregado'];
        $placeholders = implode(',', array_fill(0, count($estados), '?'));
        $stmt = $pdo->prepare('SELECT COALESCE(SUM(total),0) FROM ordenes WHERE estado IN (' . $placeholders . ')');
        $stmt->execute($estados);

        return (float) $stmt->fetchColumn();
    }

    public static function ultimasOrdenes(int $limit = 5): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            'SELECT o.*, c.nombre AS cliente_nombre, c.apellidos AS cliente_apellidos '
            . 'FROM ordenes o '
            . 'LEFT JOIN clientes c ON c.id = o.id_cliente '
            . 'ORDER BY o.fecha DESC LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public static function obtenerPorId(int $id): ?array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare(
            'SELECT o.*, c.nombre AS cliente_nombre, c.apellidos AS cliente_apellidos, c.email AS cliente_email, '
            . 'c.telefono AS cliente_telefono, c.direccion AS cliente_direccion, c.distrito AS cliente_distrito '
            . 'FROM ordenes o '
            . 'LEFT JOIN clientes c ON c.id = o.id_cliente '
            . 'WHERE o.id = :id LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $orden = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($orden === false) {
            return null;
        }

        $detalleStmt = $pdo->prepare(
            'SELECT od.*, p.nombre AS producto_nombre '
            . 'FROM orden_detalle od '
            . 'LEFT JOIN productos p ON p.id = od.producto_id '
            . 'WHERE od.orden_id = :ordenId'
        );
        $detalleStmt->execute([':ordenId' => $id]);
        $orden['detalle'] = $detalleStmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        return $orden;
    }

    public static function actualizarEstado(int $id, string $estado): bool
    {
        if (!in_array($estado, self::ESTADOS, true)) {
            return false;
        }

        $pdo = Database::connect();
        $stmt = $pdo->prepare('UPDATE ordenes SET estado = :estado WHERE id = :id');

        return $stmt->execute([
            ':estado' => $estado,
            ':id' => $id,
        ]);
    }
}
