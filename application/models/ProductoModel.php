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

        $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

        foreach ($productos as &$producto) {
            $producto['colores'] = $this->decodificarLista($producto['colores'] ?? null);
            $producto['tallas'] = $this->decodificarLista($producto['tallas'] ?? null);
        }
        unset($producto);

        return $productos;
    }

    public function getById($id): ?array
    {
        if ($id === null) {
            return null;
        }

        $producto = $this->fetchProductoDesdeBaseDeDatos($id);
        if ($producto !== null) {
            $producto['colores'] = $this->decodificarLista($producto['colores'] ?? null);
            $producto['tallas'] = $this->decodificarLista($producto['tallas'] ?? null);
            $producto['imagenes'] = $this->getImagenes((int) ($producto['id'] ?? 0));
        }

        $mock = $this->getMockProductos();
        $mockProducto = $mock[$id] ?? null;

        if ($producto === null) {
            return $mockProducto;
        }

        if ($mockProducto !== null) {
            if (empty($producto['colores'])) {
                $producto['colores'] = $mockProducto['colores'] ?? [];
            }

            if (empty($producto['tallas'])) {
                $producto['tallas'] = $mockProducto['tallas'] ?? [];
            }
        } else {
            $producto['colores'] = $producto['colores'] ?? [];
            $producto['tallas'] = $producto['tallas'] ?? [];
        }

        if (!empty($producto['imagenes'])) {
            $principal = $producto['imagenes'][0]['ruta'] ?? '';
            if ($principal !== '') {
                $producto['imagen'] = $principal;
            }
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

    private function decodificarLista($valor): array
    {
        if ($valor === null || $valor === '') {
            return [];
        }

        if (is_array($valor)) {
            return array_values(array_map('strval', $valor));
        }

        $decoded = json_decode((string) $valor, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_map('strval', $decoded));
        }

        $partes = preg_split('/[;,]+/', (string) $valor) ?: [];
        $partes = array_map(static function ($item): string {
            return trim((string) $item);
        }, $partes);
        $partes = array_filter($partes, static fn ($item): bool => $item !== '');

        return array_values(array_unique($partes));
    }

    public function getImagenes(int $productoId): array
    {
        if ($productoId <= 0) {
            return [];
        }

        try {
            $pdo = Database::connect();
            $tienePrincipal = $this->columnaExisteEnTabla('producto_imagenes', 'es_principal');

            $columnas = $tienePrincipal ? 'ruta, es_principal' : 'ruta';
            $orden = $tienePrincipal ? 'es_principal DESC, id ASC' : 'id ASC';

            $stmt = $pdo->prepare('SELECT ' . $columnas . ' FROM producto_imagenes WHERE producto_id = :id ORDER BY ' . $orden);
            $stmt->execute([':id' => $productoId]);

            $imagenes = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];

            return array_map(static function ($item) use ($tienePrincipal): array {
                return [
                    'ruta' => trim((string) ($item['ruta'] ?? '')),
                    'es_principal' => $tienePrincipal ? (int) ($item['es_principal'] ?? 0) : 0,
                ];
            }, $imagenes);
        } catch (\Throwable $exception) {
            return [];
        }
    }

    private function columnaExisteEnTabla(string $tabla, string $columna): bool
    {
        static $cache = [];

        $clave = $tabla . '.' . $columna;
        if (array_key_exists($clave, $cache)) {
            return $cache[$clave];
        }

        $pdo = Database::connect();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = :tabla AND column_name = :columna');
        $stmt->execute([
            ':tabla' => $tabla,
            ':columna' => $columna,
        ]);

        $cache[$clave] = ((int) $stmt->fetchColumn()) > 0;

        return $cache[$clave];
    }
}
