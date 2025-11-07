<?php

require_once APP_PATH . '/controllers/BaseController.php';
require_once APP_PATH . '/models/ProductoModel.php';
require_once APP_PATH . '/helpers/security_helper.php';

class BuscarController extends BaseController
{
    public function index(): void
    {
        $termino = isset($_GET['q']) ? clean_string((string) $_GET['q']) : '';
        $termino = trim($termino);

        $productoModel = new ProductoModel();
        $resultados = [];

        if ($termino !== '') {
            try {
                $resultados = $productoModel->buscarProductos($termino);
            } catch (\Throwable $exception) {
                $resultados = [];
            }
        }

        $this->render('buscar', [
            'query' => $termino,
            'resultados' => $resultados,
            'productoModel' => $productoModel,
        ]);
    }
}
