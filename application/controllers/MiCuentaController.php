<?php

declare(strict_types=1);

require_once APP_PATH . '/controllers/BaseController.php';

class MiCuentaController extends BaseController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $orden = $_SESSION['ultima_orden'] ?? null;

        $this->render('mi_cuenta', compact('orden'));
    }

    public function eliminar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['ultima_orden']);

        header('Location: ' . base_url('mi_cuenta'));
        exit;
    }
}
