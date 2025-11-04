<?php

declare(strict_types=1);

final class DashboardController extends AdminBaseController
{
    public function index(): void
    {
        $this->requireLogin();

        $ventasTotales = AdminOrdenModel::totalVentas();
        $ordenesPendientes = AdminOrdenModel::contarPorEstado('Pendiente');
        $totalClientes = $this->contarClientes();
        $totalProductos = $this->contarProductos();
        $ultimasOrdenes = AdminOrdenModel::ultimasOrdenes(5);

        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'ventasTotales' => $ventasTotales,
            'ordenesPendientes' => $ordenesPendientes,
            'totalClientes' => $totalClientes,
            'totalProductos' => $totalProductos,
            'ultimasOrdenes' => $ultimasOrdenes,
        ]);
    }

    private function contarClientes(): int
    {
        $pdo = Database::connect();
        $stmt = $pdo->query('SELECT COUNT(*) FROM clientes');

        return (int) $stmt->fetchColumn();
    }

    private function contarProductos(): int
    {
        $pdo = Database::connect();
        $stmt = $pdo->query('SELECT COUNT(*) FROM productos');

        return (int) $stmt->fetchColumn();
    }
}
