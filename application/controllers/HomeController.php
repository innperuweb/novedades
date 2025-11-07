<?php

require_once APP_PATH . '/controllers/BaseController.php';
require_once APP_PATH . '/models/ProductoModel.php';

class HomeController extends BaseController
{
    public function index(): void
    {
        try {
            $productoModel = new ProductoModel();
            $productosAleatorios = $productoModel->obtenerProductosAleatorios();
        } catch (\Throwable $exception) {
            $productosAleatorios = [];
        }

        $this->render('index', [
            'productosAleatorios' => $productosAleatorios,
        ]);
    }
}
