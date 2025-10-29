<?php

// Archivo base para desarrollo futuro del módulo Search
// No modificar vistas ni lógica visual existente
// Conectado al router principal desde index.php

require_once APP_PATH . '/controllers/BaseController.php';

class SearchController extends BaseController
{
    public function index(): void
    {
        $this->render('search/index', [
            'moduleAction' => 'index',
        ]);
    }

    public function detalle(): void
    {
        $this->render('search/index', [
            'moduleAction' => 'detalle',
        ]);
    }

    public function crear(): void
    {
        $this->render('search/index', [
            'moduleAction' => 'crear',
        ]);
    }

    public function editar(): void
    {
        $this->render('search/index', [
            'moduleAction' => 'editar',
        ]);
    }

    public function eliminar(): void
    {
        $this->render('search/index', [
            'moduleAction' => 'eliminar',
        ]);
    }
}
