<?php

declare(strict_types=1);

require_once APP_PATH . '/controllers/BaseController.php';
require_once APP_PATH . '/helpers/security_helper.php';
require_once APP_PATH . '/models/CategoriaModel.php';

final class CategoriaController extends BaseController
{
    private CategoriaModel $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
    }

    public function index(): void
    {
        $categoriasListado = $this->categoriaModel->obtenerCategoriasConSubcategorias();

        $this->render('productos', [
            'categoria' => null,
            'categoriasListado' => $categoriasListado,
        ]);
    }

    public function ver(string $slug = ''): void
    {
        $slug = sanitize_uri_segment($slug);
        $categoria = $this->categoriaModel->obtenerCategoriaPorSlug($slug);

        if ($categoria === null) {
            http_response_code(404);
        }

        $this->render('productos', [
            'categoria' => $categoria,
        ]);
    }
}
