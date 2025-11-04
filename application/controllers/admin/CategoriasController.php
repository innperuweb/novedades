<?php

declare(strict_types=1);

final class CategoriasController extends AdminBaseController
{
    private AdminCategoriaModel $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new AdminCategoriaModel();
    }

    public function index(): void
    {
        $this->requireLogin();

        $categoriaId = isset($_GET['categoria']) ? sanitize_int($_GET['categoria']) : null;
        $subcategoriaId = isset($_GET['subcategoria']) ? sanitize_int($_GET['subcategoria']) : null;

        $categorias = $this->categoriaModel->listarCategorias();
        $subcategorias = $this->categoriaModel->listarSubcategorias();
        $categoriaEditar = $categoriaId !== null ? $this->categoriaModel->obtenerCategoria($categoriaId) : null;
        $subcategoriaEditar = $subcategoriaId !== null ? $this->categoriaModel->obtenerSubcategoria($subcategoriaId) : null;

        $this->render('categorias/index', [
            'title' => 'Categorías y subcategorías',
            'categorias' => $categorias,
            'subcategorias' => $subcategorias,
            'categoriaEditar' => $categoriaEditar,
            'subcategoriaEditar' => $subcategoriaEditar,
        ]);
    }

    public function guardarCategoria(): void
    {
        $this->requireLogin();
        $this->asegurarPeticionPost();

        $datos = [
            'id' => isset($_POST['id']) ? sanitize_int($_POST['id']) : null,
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'descripcion' => trim((string) ($_POST['descripcion'] ?? '')),
            'activo' => isset($_POST['activo']) ? 1 : 0,
        ];

        if ($datos['nombre'] === '') {
            admin_set_flash('danger', 'El nombre de la categoría es obligatorio.');
            $this->redirect('admin/categorias');

            return;
        }

        $this->categoriaModel->guardarCategoria($datos);
        admin_set_flash('success', 'Categoría guardada correctamente.');
        $this->redirect('admin/categorias');
    }

    public function eliminarCategoria(): void
    {
        $this->requireLogin();
        $this->asegurarPeticionPost();

        $id = isset($_POST['id']) ? sanitize_int($_POST['id']) : null;
        if ($id === null) {
            admin_set_flash('danger', 'Categoría inválida.');
            $this->redirect('admin/categorias');

            return;
        }

        $this->categoriaModel->eliminarCategoria($id);
        admin_set_flash('success', 'Categoría eliminada.');
        $this->redirect('admin/categorias');
    }

    public function guardarSubcategoria(): void
    {
        $this->requireLogin();
        $this->asegurarPeticionPost();

        $datos = [
            'id' => isset($_POST['id']) ? sanitize_int($_POST['id']) : null,
            'categoria_id' => isset($_POST['categoria_id']) ? sanitize_int($_POST['categoria_id']) : null,
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'descripcion' => trim((string) ($_POST['descripcion'] ?? '')),
            'activo' => isset($_POST['activo']) ? 1 : 0,
        ];

        if ($datos['categoria_id'] === null || $datos['nombre'] === '') {
            admin_set_flash('danger', 'Debe seleccionar una categoría y asignar un nombre.');
            $this->redirect('admin/categorias');

            return;
        }

        $this->categoriaModel->guardarSubcategoria($datos);
        admin_set_flash('success', 'Subcategoría guardada correctamente.');
        $this->redirect('admin/categorias');
    }

    public function eliminarSubcategoria(): void
    {
        $this->requireLogin();
        $this->asegurarPeticionPost();

        $id = isset($_POST['id']) ? sanitize_int($_POST['id']) : null;
        if ($id === null) {
            admin_set_flash('danger', 'Subcategoría inválida.');
            $this->redirect('admin/categorias');

            return;
        }

        $this->categoriaModel->eliminarSubcategoria($id);
        admin_set_flash('success', 'Subcategoría eliminada.');
        $this->redirect('admin/categorias');
    }

    private function asegurarPeticionPost(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/categorias');
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            $this->redirect('admin/categorias');
        }
    }
}
