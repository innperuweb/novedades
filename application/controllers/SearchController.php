<?php

// Archivo base para desarrollo futuro del módulo Search
// No modificar vistas ni lógica visual existente
// Conectado al router principal desde index.php

require_once APP_PATH . '/controllers/BaseController.php';

class SearchController extends BaseController
{
    public function index(): void
    {
        $term = isset($_GET['q']) ? sanitize((string) $_GET['q']) : '';
        $resultados = [];

        if ($term !== '') {
            $resultados = ProductoModel::buscar($term);
        }

        $this->render('search/index', [
            'moduleAction' => 'index',
            'term' => $term,
            'resultados' => $resultados,
        ]);
    }

    public function ajax(): void
    {
        if (!isset($_GET['term'])) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode([]);

            return;
        }

        $term = sanitize((string) $_GET['term']);
        $resultados = $term === '' ? [] : ProductoModel::buscar($term);

        header('Content-Type: application/json');
        echo json_encode($resultados);
    }
}
