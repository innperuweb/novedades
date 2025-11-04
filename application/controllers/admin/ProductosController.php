<?php

declare(strict_types=1);

final class ProductosController extends AdminBaseController
{
    private AdminProductoModel $productoModel;
    private AdminCategoriaModel $categoriaModel;
    private string $directorioUploads;

    public function __construct()
    {
        $this->productoModel = new AdminProductoModel();
        $this->categoriaModel = new AdminCategoriaModel();
        $this->directorioUploads = ROOT_PATH . '/public/assets/uploads/productos';
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
            'marca' => '',
            'descripcion' => '',
            'precio' => 0,
            'stock' => 0,
            'sku' => '',
            'imagen' => '',
            'tabla_tallas' => '',
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

        $tablaTallas = $this->manejarTablaTallas(null);
        if (isset($tablaTallas['error'])) {
            admin_set_flash('danger', $tablaTallas['error']);
            $datos['subcategorias'] = $subcategorias;
            $this->mostrarFormularioProducto($datos, [], false);

            return;
        }

        if (!empty($tablaTallas['archivo'])) {
            $datos['tabla_tallas'] = $tablaTallas['archivo'];
        }

        $imagenesResultado = $this->manejarImagenes(null);
        if (!empty($imagenesResultado['errores'])) {
            admin_set_flash('danger', implode(' ', $imagenesResultado['errores']));
            $datos['subcategorias'] = $subcategorias;
            $this->mostrarFormularioProducto($datos, [], false);

            return;
        }

        $nuevoId = $this->productoModel->crear($datos, $subcategorias);
        if (!empty($imagenesResultado['archivos'])) {
            $this->productoModel->guardarImagenes($nuevoId, $imagenesResultado['archivos']);
        }
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

        $productoActual = $this->productoModel->obtenerProducto($productoId) ?? [];

        $datos = $this->obtenerDatosProductoDesdeRequest();
        $errores = $this->validarProducto($datos);
        $subcategorias = $this->obtenerSubcategoriasDesdeRequest();

        if ($errores !== []) {
            $datos['id'] = $productoId;
            $datos['subcategorias'] = $subcategorias;
            $this->mostrarFormularioProducto($datos, $errores, true);

            return;
        }

        $tablaTallas = $this->manejarTablaTallas($productoActual);
        if (isset($tablaTallas['error'])) {
            admin_set_flash('danger', $tablaTallas['error']);
            $datos['id'] = $productoId;
            $datos['subcategorias'] = $subcategorias;
            $this->mostrarFormularioProducto($datos, [], true);

            return;
        }

        if (!empty($tablaTallas['archivo'])) {
            $datos['tabla_tallas'] = $tablaTallas['archivo'];
        }

        $imagenesResultado = $this->manejarImagenes($productoActual);
        if (!empty($imagenesResultado['errores'])) {
            admin_set_flash('danger', implode(' ', $imagenesResultado['errores']));
            $datos['id'] = $productoId;
            $datos['subcategorias'] = $subcategorias;
            $this->mostrarFormularioProducto($datos, [], true);

            return;
        }

        $this->productoModel->actualizarProducto($productoId, $datos, $subcategorias);
        if (!empty($imagenesResultado['archivos'])) {
            $this->eliminarArchivosFisicos($productoActual['imagenes'] ?? []);
            $this->productoModel->reemplazarImagenes($productoId, $imagenesResultado['archivos']);
        }
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
            'marca' => trim((string) ($_POST['marca'] ?? '')),
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

    private function manejarTablaTallas(?array $productoActual): array
    {
        if (!isset($_FILES['tabla_tallas']) || !is_array($_FILES['tabla_tallas'])) {
            return ['archivo' => null];
        }

        $archivo = $_FILES['tabla_tallas'];
        $error = (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($error === UPLOAD_ERR_NO_FILE) {
            return ['archivo' => null];
        }

        if ($error !== UPLOAD_ERR_OK || empty($archivo['tmp_name']) || !is_uploaded_file($archivo['tmp_name'])) {
            return ['error' => 'No se pudo subir la tabla de tallas. Por favor, inténtalo nuevamente.'];
        }

        $info = @getimagesize($archivo['tmp_name']);
        if ($info === false) {
            return ['error' => 'El archivo de la tabla de tallas debe ser una imagen válida.'];
        }

        $nombreArchivo = $this->generarNombreArchivo((string) ($archivo['name'] ?? 'tabla-tallas'));
        $destino = $this->asegurarDirectorioUploads() . '/' . $nombreArchivo;

        if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
            return ['error' => 'No se pudo guardar la tabla de tallas en el servidor.'];
        }

        if (!empty($productoActual['tabla_tallas'])) {
            $this->eliminarArchivoFisico((string) $productoActual['tabla_tallas']);
        }

        return ['archivo' => $nombreArchivo];
    }

    private function manejarImagenes(?array $productoActual): array
    {
        if (!isset($_FILES['imagenes']) || !is_array($_FILES['imagenes'])) {
            return ['archivos' => [], 'errores' => []];
        }

        $imagenes = $_FILES['imagenes'];
        $nombres = $imagenes['name'] ?? [];
        $temporal = $imagenes['tmp_name'] ?? [];
        $errores = $imagenes['error'] ?? [];
        $cantidad = is_array($nombres) ? count($nombres) : 0;

        $resultados = [];
        $erroresEncontrados = [];

        for ($i = 0; $i < $cantidad; $i++) {
            $error = (int) ($errores[$i] ?? UPLOAD_ERR_NO_FILE);
            if ($error === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($error !== UPLOAD_ERR_OK) {
                $erroresEncontrados[] = 'Ocurrió un error al subir una de las imágenes.';
                continue;
            }

            $tmp = $temporal[$i] ?? '';
            if ($tmp === '' || !is_uploaded_file($tmp)) {
                $erroresEncontrados[] = 'No se pudo procesar una de las imágenes seleccionadas.';
                continue;
            }

            $info = @getimagesize($tmp);
            if ($info === false) {
                $erroresEncontrados[] = 'Una de las imágenes seleccionadas no es válida.';
                continue;
            }

            $nombreArchivo = $this->generarNombreArchivo((string) ($nombres[$i] ?? 'imagen-producto'));
            $destino = $this->asegurarDirectorioUploads() . '/' . $nombreArchivo;

            if (!move_uploaded_file($tmp, $destino)) {
                $erroresEncontrados[] = 'No se pudo guardar una de las imágenes en el servidor.';
                continue;
            }

            $resultados[] = $nombreArchivo;
        }

        if ($erroresEncontrados !== []) {
            foreach ($resultados as $archivoGuardado) {
                $this->eliminarArchivoFisico((string) $archivoGuardado);
            }

            return ['archivos' => [], 'errores' => $erroresEncontrados];
        }

        return ['archivos' => $resultados, 'errores' => []];
    }

    private function asegurarDirectorioUploads(): string
    {
        if (!is_dir($this->directorioUploads)) {
            mkdir($this->directorioUploads, 0755, true);
        }

        return $this->directorioUploads;
    }

    private function generarNombreArchivo(string $nombreOriginal): string
    {
        $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
        $extension = $extension !== '' ? '.' . strtolower($extension) : '';

        return uniqid('producto_', true) . $extension;
    }

    private function eliminarArchivosFisicos(array $imagenes): void
    {
        foreach ($imagenes as $imagen) {
            if (is_array($imagen)) {
                $ruta = (string) ($imagen['ruta'] ?? '');
            } else {
                $ruta = (string) $imagen;
            }

            $this->eliminarArchivoFisico($ruta);
        }
    }

    private function eliminarArchivoFisico(string $ruta): void
    {
        if ($ruta === '') {
            return;
        }

        $rutaNormalizada = ltrim($ruta, '/');
        if (strpos($rutaNormalizada, 'uploads/productos/') === 0) {
            $rutaNormalizada = substr($rutaNormalizada, strlen('uploads/productos/')) ?: '';
        }

        $archivo = $this->directorioUploads . '/' . $rutaNormalizada;

        if (strpos(realpath(dirname($archivo)) ?: '', realpath($this->directorioUploads)) !== 0) {
            return;
        }

        if (is_file($archivo)) {
            @unlink($archivo);
        }
    }
}
