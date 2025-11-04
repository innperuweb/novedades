<?php

declare(strict_types=1);

final class ProductosController extends AdminBaseController
{
    private AdminProductoModel $productoModel;
    private AdminCategoriaModel $categoriaModel;

    public function __construct()
    {
        $this->productoModel = new AdminProductoModel();
        $this->categoriaModel = new AdminCategoriaModel();
    }

    public function index(): void
    {
        $this->requireLogin();

        $productos = $this->productoModel->obtenerTodos();

        $this->render('productos/index', [
            'title' => 'Productos',
            'productos' => $productos,
        ]);
    }

    public function crear(): void
    {
        $this->requireLogin();
        $this->mostrarFormularioProducto([
            'nombre' => '',
            'descripcion' => '',
            'precio' => 0,
            'stock' => 0,
            'sku' => '',
            'imagen' => '',
            'colores' => [],
            'tallas' => [],
            'subcategorias' => [],
            'activo' => 1,
        ], [], false);
    }

    public function editar(string $id): void
    {
        $this->requireLogin();

        $productoId = sanitize_int($id);
        if ($productoId === null) {
            admin_set_flash('danger', 'Producto inválido.');
            $this->redirect('admin/productos');

            return;
        }

        $producto = $this->productoModel->obtenerProducto($productoId);
        if ($producto === null) {
            admin_set_flash('warning', 'Producto no encontrado.');
            $this->redirect('admin/productos');

            return;
        }

        $this->mostrarFormularioProducto($producto, [], true);
    }

    public function guardar(): void
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect('admin/productos');

            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            $this->redirect('admin/productos');

            return;
        }

        $datos = $this->obtenerDatosProductoDesdeRequest();
        $errores = $this->validarProducto($datos);
        $subcategorias = $this->obtenerSubcategoriasDesdeRequest();

        if ($errores !== []) {
            $datos['subcategorias'] = $subcategorias;
            $this->mostrarFormularioProducto($datos, $errores, false);

            return;
        }

        $nuevoId = $this->productoModel->crear($datos, $subcategorias);
        admin_set_flash('success', 'Producto creado correctamente.');
        $this->redirect('admin/productos/editar/' . $nuevoId);
    }

    public function actualizar(string $id): void
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect('admin/productos');

            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            $this->redirect('admin/productos');

            return;
        }

        $productoId = sanitize_int($id);
        if ($productoId === null) {
            admin_set_flash('danger', 'Producto inválido.');
            $this->redirect('admin/productos');

            return;
        }

        $datos = $this->obtenerDatosProductoDesdeRequest();
        $errores = $this->validarProducto($datos);
        $subcategorias = $this->obtenerSubcategoriasDesdeRequest();

        if ($errores !== []) {
            $datos['id'] = $productoId;
            $datos['subcategorias'] = $subcategorias;
            $this->mostrarFormularioProducto($datos, $errores, true);

            return;
        }

        $this->productoModel->actualizarProducto($productoId, $datos, $subcategorias);
        admin_set_flash('success', 'Producto actualizado correctamente.');
        $this->redirect('admin/productos/editar/' . $productoId);
    }

    public function eliminar(string $id): void
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect('admin/productos');

            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            $this->redirect('admin/productos');

            return;
        }

        $productoId = sanitize_int($id);
        if ($productoId === null) {
            admin_set_flash('danger', 'Identificador de producto inválido.');
            $this->redirect('admin/productos');

            return;
        }

        $this->productoModel->eliminarProducto($productoId);
        admin_set_flash('success', 'Producto eliminado correctamente.');
        $this->redirect('admin/productos');
    }

    private function mostrarFormularioProducto(array $producto, array $errores, bool $esEdicion): void
    {
        $categorias = $this->categoriaModel->listarCategorias();
        $subcategorias = $this->categoriaModel->listarSubcategorias();

        $this->render('productos/form', [
            'title' => $esEdicion ? 'Editar producto' : 'Nuevo producto',
            'producto' => $producto,
            'categorias' => $categorias,
            'subcategorias' => $subcategorias,
            'errores' => $errores,
            'esEdicion' => $esEdicion,
        ]);
    }

    private function obtenerDatosProductoDesdeRequest(): array
    {
        $precio = (string) ($_POST['precio'] ?? '0');
        $precio = str_replace(',', '.', $precio);

        return [
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'descripcion' => trim((string) ($_POST['descripcion'] ?? '')),
            'precio' => (float) $precio,
            'stock' => isset($_POST['stock']) ? max(0, (int) $_POST['stock']) : 0,
            'sku' => trim((string) ($_POST['sku'] ?? '')),
            'imagen' => trim((string) ($_POST['imagen'] ?? '')),
            'colores' => $_POST['colores'] ?? '',
            'tallas' => $_POST['tallas'] ?? '',
            'activo' => isset($_POST['activo']) ? 1 : 0,
        ];
    }

    private function validarProducto(array $datos): array
    {
        $errores = [];

        if ($datos['nombre'] === '') {
            $errores['nombre'] = 'El nombre es obligatorio.';
        }

        if ($datos['precio'] <= 0) {
            $errores['precio'] = 'Ingrese un precio mayor a cero.';
        }

        if ($datos['stock'] < 0) {
            $errores['stock'] = 'El stock no puede ser negativo.';
        }

        return $errores;
    }

    private function obtenerSubcategoriasDesdeRequest(): array
    {
        if (!isset($_POST['subcategorias'])) {
            return [];
        }

        $valores = $_POST['subcategorias'];
        if (!is_array($valores)) {
            $valores = [$valores];
        }

        $valores = array_map(static function ($item): int {
            return (int) $item;
        }, $valores);

        $valores = array_filter($valores, static function ($item): bool {
            return $item > 0;
        });

        return array_values(array_unique($valores));
    }
}
