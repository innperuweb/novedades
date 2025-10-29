<?php

require_once APP_PATH . '/controllers/BaseController.php';

class CuentaController extends BaseController
{
    public function index(): void
    {
        $this->render('mi_cuenta');
    }

    public function verOrden(): void
    {
        $this->render('ver_orden');
    }
}
