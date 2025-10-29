<?php

// Archivo base para desarrollo futuro del módulo Admin
// No modificar vistas ni lógica visual existente
// Conectado al router principal desde index.php

class AdminModel
{
    public function authenticate(string $username, string $password): bool
    {
        $pdo = Database::connect();
        // TODO: Implementar autenticación de administradores.

        return false;
    }
}
