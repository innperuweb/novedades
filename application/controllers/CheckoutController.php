<?php

require_once APP_PATH . '/controllers/BaseController.php';

class CheckoutController extends BaseController
{
    public function index(): void
    {
        $this->render('checkout');
    }

    public function carrito(): void
    {
        $this->render('carrito_compras');
    }
}
