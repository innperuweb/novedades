<?php

class ProductoModel
{
    public function getProductos(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query('SELECT * FROM productos');

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
