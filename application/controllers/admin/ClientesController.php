<?php

declare(strict_types=1);

final class ClientesController extends AdminBaseController
{
    public function index(): void
    {
        $this->requireLogin();

        $clientes = AdminClienteModel::listar();

        $this->render('clientes/index', [
            'title' => 'Clientes',
            'clientes' => $clientes,
        ]);
    }

    public function ver(string $id): void
    {
        $this->requireLogin();

        $clienteId = sanitize_int($id);
        if ($clienteId === null) {
            admin_set_flash('danger', 'Cliente inválido.');
            $this->redirect('admin/clientes');

            return;
        }

        $cliente = AdminClienteModel::obtenerDetalle($clienteId);
        if ($cliente === null) {
            admin_set_flash('warning', 'El cliente no existe.');
            $this->redirect('admin/clientes');

            return;
        }

        $this->render('clientes/ver', [
            'title' => 'Detalle de cliente',
            'cliente' => $cliente,
        ]);
    }
}
