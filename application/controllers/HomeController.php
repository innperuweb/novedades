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
            $novedades = $productoModel->obtenerProductosPorSeccion('novedades', 10);
            $ofertas = $productoModel->obtenerProductosPorSeccion('ofertas', 10);
            $populares = $productoModel->obtenerProductosPorSeccion('populares', 10);
        } catch (\Throwable $exception) {
            $productosAleatorios = [];
            $novedades = [];
            $ofertas = [];
            $populares = [];
        }

        $this->render('index', [
            'productosAleatorios' => $productosAleatorios,
            'novedades' => $novedades,
            'ofertas' => $ofertas,
            'populares' => $populares,
        ]);
    }
}
