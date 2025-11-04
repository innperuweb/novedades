<?php

require_once APP_PATH . '/controllers/BaseController.php';
require_once APP_PATH . '/models/ProductoModel.php';
require_once APP_PATH . '/helpers/security_helper.php';

class BuscarController extends BaseController
{
    public function resultado(): void
    {
        $termino = isset($_GET['q']) ? sanitize((string) $_GET['q']) : '';
        $resultados = $termino !== '' ? ProductoModel::buscar($termino) : [];

        $this->render('search/index', [
            'moduleAction' => 'resultado',
            'term' => $termino,
            'resultados' => $resultados,
        ]);
    }
}
