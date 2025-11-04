<?php

declare(strict_types=1);

final class AdminClienteModel extends ClienteModel
{
    public static function listar(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query(
            'SELECT c.*, COUNT(o.id) AS total_ordenes, MAX(o.fecha) AS ultima_compra '
            . 'FROM clientes c '
            . 'LEFT JOIN ordenes o ON o.id_cliente = c.id '
            . 'GROUP BY c.id '
            . 'ORDER BY c.fecha_registro DESC'
        );

        return $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
    }

    public static function obtenerDetalle(int $id): ?array
    {
        $cliente = parent::obtenerPorId($id);

        if ($cliente === null) {
            return null;
        }

        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM ordenes WHERE id_cliente = :cliente ORDER BY fecha DESC');
        $stmt->execute([':cliente' => $id]);
        $cliente['ordenes'] = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        return $cliente;
    }
}
