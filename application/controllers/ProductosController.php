<?php

require_once APP_PATH . '/controllers/BaseController.php';

class ProductosController extends BaseController
{
    public function index(): void
    {
        $this->render('productos');
    }

    public function detalle(): void
    {
        $this->render('detalle_producto');
    }

    public function ofertas(): void
    {
        $this->render('ofertas');
    }
}
