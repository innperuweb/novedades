<?php

// Archivo base para desarrollo futuro del módulo Admin
// No modificar vistas ni lógica visual existente
// Conectado al router principal desde index.php

require_once APP_PATH . '/controllers/BaseController.php';

class AdminController extends BaseController
{
    public function index(): void
    {
        $this->login();
    }

    public function login(): void
    {
        $this->render('admin/login', [
            'moduleAction' => 'login',
        ]);
    }

    public function dashboard(): void
    {
        require_login();
        $this->render('admin/dashboard', [
            'moduleAction' => 'dashboard',
        ]);
    }

    public function productos(): void
    {
        require_login();
        $this->render('admin/productos', [
            'moduleAction' => 'productos',
        ]);
    }

    public function detalle(): void
    {
        require_login();
        $this->render('admin/productos', [
            'moduleAction' => 'detalle',
        ]);
    }

    public function crear(): void
    {
        require_login();
        $this->render('admin/productos', [
            'moduleAction' => 'crear',
        ]);
    }

    public function editar(): void
    {
        require_login();
        $this->render('admin/productos', [
            'moduleAction' => 'editar',
        ]);
    }

    public function eliminar(): void
    {
        require_login();
        $this->render('admin/productos', [
            'moduleAction' => 'eliminar',
        ]);
    }
}
