<?php

declare(strict_types=1);

require_once APP_PATH . '/controllers/BaseController.php';
require_once APP_PATH . '/models/OrdenModel.php';

class MiCuentaController extends BaseController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $emailCliente = $_SESSION['email_cliente'] ?? null;
        $ordenes = $emailCliente ? OrdenModel::obtenerPorEmail($emailCliente) : [];

        $this->render('mi_cuenta', compact('ordenes'));
    }

    public function eliminar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $numeroOrden = $_GET['nro'] ?? '';

        if ($numeroOrden !== '') {
            OrdenModel::eliminarPorNro($numeroOrden);
        }

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        header('Location: ' . base_url('mi_cuenta'));
        exit;
    }
}
