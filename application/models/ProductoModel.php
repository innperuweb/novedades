<?php

// Archivo base para desarrollo futuro del módulo Producto
// No modificar vistas ni lógica visual existente
// Conectado al router principal desde index.php

class ProductoModel
{
    public function getAll(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query('SELECT * FROM productos');

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById($id): ?array
    {
        if ($id === null) {
            return null;
        }

        $producto = $this->fetchProductoDesdeBaseDeDatos($id);
        $mock = $this->getMockProductos();
        $mockProducto = $mock[$id] ?? null;

        if ($producto === null) {
            return $mockProducto;
        }

        if ($mockProducto !== null) {
            if (empty($producto['imagen']) && !empty($mockProducto['imagen'])) {
                $producto['imagen'] = $mockProducto['imagen'];
            }

            $producto['colores'] = $mockProducto['colores'] ?? [];
            $producto['tallas'] = $mockProducto['tallas'] ?? [];
        } else {
            $producto['colores'] = $producto['colores'] ?? [];
            $producto['tallas'] = $producto['tallas'] ?? [];
        }

        return $producto;
    }

    public function buscarProductos($term): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT id, nombre, imagen, precio FROM productos WHERE nombre LIKE ? OR descripcion LIKE ? LIMIT 10');
        $likeTerm = '%' . $term . '%';
        $stmt->execute([$likeTerm, $likeTerm]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function fetchProductoDesdeBaseDeDatos($id): ?array
    {
        try {
            $pdo = Database::connect();
            $stmt = $pdo->prepare('SELECT * FROM productos WHERE id = :id');
            $stmt->execute([':id' => $id]);

            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            return $result !== false ? $result : null;
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function getMockProductos(): array
    {
        return [
            1 => [
                'id' => 1,
                'nombre' => 'Polera Oversize',
                'precio' => 89.90,
                'imagen' => 'producto1.jpg',
                'colores' => ['Negro', 'Blanco', 'Azul'],
                'tallas' => ['S', 'M', 'L', 'XL'],
            ],
            2 => [
                'id' => 2,
                'nombre' => 'Pantalón Jogger',
                'precio' => 119.90,
                'imagen' => 'producto2.jpg',
                'colores' => ['Gris', 'Negro'],
                'tallas' => ['28', '30', '32', '34'],
            ],
        ];
    }
}
