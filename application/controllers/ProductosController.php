<?php

require_once APP_PATH . '/controllers/BaseController.php';
require_once APP_PATH . '/models/ProductoModel.php';
require_once APP_PATH . '/helpers/security_helper.php';

class ProductosController extends BaseController
{
    public function index(): void
    {
        $this->render('productos');
    }

    public function detalle(): void
    {
        $id = isset($_GET['id']) ? sanitize_int($_GET['id']) : null;
        $id = $id ?? 1;

        $model = new ProductoModel();
        $producto = $model->getById($id);

        if ($producto === null) {
            $producto = $model->getById(1);
            if ($producto === null) {
                $this->render('detalle_producto', ['producto' => null]);
                return;
            }
        }

        $this->render('detalle_producto', compact('producto'));
    }

    public function ofertas(): void
    {
        $this->render('ofertas');
    }
}
