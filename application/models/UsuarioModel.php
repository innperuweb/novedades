<?php

// Archivo base para desarrollo futuro del módulo Usuario
// No modificar vistas ni lógica visual existente
// Conectado al router principal desde index.php

class UsuarioModel
{
    public function getAll(): array
    {
        $pdo = Database::connect();
        // TODO: Implementar consulta para obtener todos los usuarios.

        return [];
    }

    public function getById($id): ?array
    {
        $pdo = Database::connect();
        // TODO: Implementar consulta para obtener un usuario por ID.

        return null;
    }
}
