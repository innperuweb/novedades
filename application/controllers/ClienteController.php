<?php

require_once APP_PATH . '/controllers/BaseController.php';

class ClienteController extends BaseController
{
    public function index(): void
    {
        $this->render('para_el_cliente');
    }

    public function libro(): void
    {
        $this->render('libro_de_reclamaciones');
    }
}
