<?php

declare(strict_types=1);

final class OrdenesController extends AdminBaseController
{
    public function index(): void
    {
        $this->requireLogin();

        $estado = isset($_GET['estado']) ? sanitize_string($_GET['estado']) : '';
        $busqueda = isset($_GET['q']) ? sanitize_string($_GET['q']) : '';

        $ordenes = AdminOrdenModel::listar([
            'estado' => $estado,
            'busqueda' => $busqueda,
        ]);

        $this->render('ordenes/index', [
            'title' => 'Órdenes',
            'ordenes' => $ordenes,
            'estadoSeleccionado' => $estado,
            'busqueda' => $busqueda,
            'estados' => AdminOrdenModel::ESTADOS,
        ]);
    }

    public function ver(string $id): void
    {
        $this->requireLogin();

        $ordenId = sanitize_int($id);
        if ($ordenId === null) {
            admin_set_flash('danger', 'Orden inválida.');
            $this->redirect('admin/ordenes');

            return;
        }

        $orden = AdminOrdenModel::obtenerPorId($ordenId);

        if ($orden === null) {
            admin_set_flash('warning', 'La orden solicitada no existe.');
            $this->redirect('admin/ordenes');

            return;
        }

        $this->render('ordenes/ver', [
            'title' => 'Detalle de orden',
            'orden' => $orden,
            'estados' => AdminOrdenModel::ESTADOS,
        ]);
    }

    public function cambiarEstado(): void
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect('admin/ordenes');

            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            $this->redirect('admin/ordenes');

            return;
        }

        $ordenId = isset($_POST['orden_id']) ? sanitize_int($_POST['orden_id']) : null;
        $estado = isset($_POST['estado']) ? sanitize_string($_POST['estado']) : '';

        if ($ordenId === null || !in_array($estado, AdminOrdenModel::ESTADOS, true)) {
            admin_set_flash('danger', 'Datos inválidos para actualizar la orden.');
            $this->redirect('admin/ordenes');

            return;
        }

        $actualizado = AdminOrdenModel::actualizarEstado($ordenId, $estado);

        if ($actualizado) {
            admin_set_flash('success', 'Estado de la orden actualizado correctamente.');
        } else {
            admin_set_flash('danger', 'No se pudo actualizar el estado de la orden.');
        }

        $referer = $_POST['redirect_to'] ?? '';
        if ($referer !== '' && filter_var($referer, FILTER_VALIDATE_URL)) {
            header('Location: ' . $referer);
            exit;
        }

        $this->redirect('admin/ordenes');
    }
}
