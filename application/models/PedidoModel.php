<?php

// Archivo base para desarrollo futuro del módulo Pedido
// No modificar vistas ni lógica visual existente
// Conectado al router principal desde index.php

class PedidoModel
{
    public function getAll(): array
    {
        $pdo = Database::connect();
        // TODO: Implementar consulta para obtener todos los pedidos.

        return [];
    }

    public function getById($id): ?array
    {
        $pdo = Database::connect();
        // TODO: Implementar consulta para obtener un pedido por ID.

        return null;
    }
}
