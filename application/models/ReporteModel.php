<?php

// Archivo base para desarrollo futuro del módulo Reporte
// No modificar vistas ni lógica visual existente
// Conectado al router principal desde index.php

class ReporteModel
{
    public function getAll(): array
    {
        $pdo = Database::connect();
        // TODO: Implementar consulta para obtener todos los reportes.

        return [];
    }

    public function getById($id): ?array
    {
        $pdo = Database::connect();
        // TODO: Implementar consulta para obtener un reporte por ID.

        return null;
    }
}
