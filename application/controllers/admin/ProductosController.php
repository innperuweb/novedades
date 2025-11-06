<?php

declare(strict_types=1);

final class ProductosController extends AdminBaseController
{
    private const TABLA_TALLAS_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    private AdminProductoModel $productoModel;
    private AdminCategoriaModel $categoriaModel;
    private string $directorioTablaTallas;

    public function __construct()
    {
        $this->productoModel = new AdminProductoModel();
        $this->categoriaModel = new AdminCategoriaModel();
        $this->directorioTablaTallas = ROOT_PATH . '/public/assets/uploads/tabla_tallas';
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
            'tabla_tallas' => null,
            'colores' => [],
            'tallas' => [],
            'subcategorias' => [],
            'visible' => 1,
            'estado' => 1,
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

    public function eliminarTablaTallas(string $id): void
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->responderJson(['success' => false, 'message' => 'Método no permitido.'], 405);

            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            $this->responderJson(['success' => false, 'message' => 'Sesión inválida, recargue la página.'], 400);

            return;
        }

        $productoId = sanitize_int($id);
        if ($productoId === null) {
            $this->responderJson(['success' => false, 'message' => 'Producto inválido.'], 400);

            return;
        }

        $producto = $this->productoModel->obtenerProducto($productoId);
        if ($producto === null) {
            $this->responderJson(['success' => false, 'message' => 'Producto no encontrado.'], 404);

            return;
        }

        $rutaAnterior = (string) ($producto['tabla_tallas'] ?? '');
        $this->productoModel->limpiarTablaTallas($productoId);

        if ($rutaAnterior !== '') {
            $this->eliminarArchivoFisico($rutaAnterior);
        }

        $this->responderJson(['success' => true]);
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

        $visibleMarcado = isset($_POST['visible']);

        return [
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'marca' => trim((string) ($_POST['marca'] ?? '')),
            'descripcion' => trim((string) ($_POST['descripcion'] ?? '')),
            'precio' => (float) $precio,
            'stock' => max(0, (int) ($_POST['stock'] ?? 0)),
            'sku' => trim((string) ($_POST['sku'] ?? '')),
            'colores' => $this->obtenerOpcionesDesdePost('colores'),
            'tallas' => $this->obtenerOpcionesDesdePost('tallas'),
            'visible' => $visibleMarcado ? 1 : 0,
            'estado' => $visibleMarcado ? 1 : 0,
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

    private function obtenerOpcionesDesdePost(string $campo): array
    {
        if (!isset($_POST[$campo])) {
            return [];
        }

        $valor = $_POST[$campo];

        if (is_array($valor)) {
            $items = $valor;
        } else {
            $items = preg_split('/[;,]+/', (string) $valor) ?: [];
        }

        $items = array_map(static fn ($item): string => trim((string) $item), $items);
        $items = array_filter($items, static fn ($item): bool => $item !== '');

        return array_values(array_unique($items));
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

        $mime = is_array($info) ? (string) ($info['mime'] ?? '') : '';
        if ($mime === '' || !array_key_exists($mime, self::TABLA_TALLAS_MIMES)) {
            return ['error' => 'El archivo de la tabla de tallas debe ser JPG, PNG o WEBP.'];
        }

        $extension = $this->obtenerExtensionTablaTallas($mime, (string) ($archivo['name'] ?? 'tabla-tallas'));
        $nombreArchivo = $this->generarNombreTablaTallas((string) ($archivo['name'] ?? 'tabla-tallas'), $extension);

        try {
            $directorio = $this->asegurarDirectorioTablaTallas();
        } catch (\RuntimeException $exception) {
            return ['error' => $exception->getMessage()];
        }

        $destino = $directorio . '/' . $nombreArchivo;

        if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
            return ['error' => 'No se pudo guardar la tabla de tallas en el servidor.'];
        }

        if (!empty($productoActual['tabla_tallas'])) {
            $this->eliminarArchivoFisico((string) $productoActual['tabla_tallas']);
        }

        return ['archivo' => 'uploads/tabla_tallas/' . $nombreArchivo];
    }

    private function asegurarDirectorioTablaTallas(): string
    {
        $ruta = $this->directorioTablaTallas;

        if (!is_dir($ruta)) {
            if (!mkdir($ruta, 0755, true) && !is_dir($ruta)) {
                throw new \RuntimeException('No se pudo crear el directorio para las tablas de tallas.');
            }
            @chmod($ruta, 0755);
        }

        if (!is_writable($ruta)) {
            @chmod($ruta, 0755);
        }

        if (!is_writable($ruta)) {
            throw new \RuntimeException('El directorio de tablas de tallas no es escribible.');
        }

        return rtrim($ruta, '/');
    }

    private function generarNombreTablaTallas(string $nombreOriginal, string $extension): string
    {
        $extension = ltrim($extension, '.');
        if ($extension === '') {
            $extension = 'jpg';
        }

        $base = pathinfo($nombreOriginal, PATHINFO_FILENAME);
        $base = $base !== '' ? preg_replace('/[^a-z0-9_-]+/i', '-', $base) : 'tabla-tallas';
        $base = trim((string) $base, '-');
        if ($base === '') {
            $base = 'tabla-tallas';
        }

        return uniqid($base . '_', true) . '.' . $extension;
    }

    private function obtenerExtensionTablaTallas(string $mime, string $nombreOriginal): string
    {
        $mime = strtolower($mime);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        if ($extension === '' || !in_array($extension, self::TABLA_TALLAS_MIMES, true)) {
            $extension = self::TABLA_TALLAS_MIMES[$mime] ?? 'jpg';
        }

        return $extension;
    }

    private function eliminarArchivoFisico(string $ruta): void
    {
        if ($ruta === '') {
            return;
        }

        $rutaNormalizada = ltrim($ruta, '/');
        if ($rutaNormalizada === '') {
            return;
        }

        if (strpos($rutaNormalizada, 'public/assets/') === 0) {
            $rutaNormalizada = ltrim(substr($rutaNormalizada, strlen('public/assets/')) ?: '', '/');
        }

        $mapas = [
            'uploads/tabla_tallas/' => $this->directorioTablaTallas,
            'tabla_tallas/' => $this->directorioTablaTallas,
            'public/uploads/tabla_tallas/' => ROOT_PATH . '/public/uploads/tabla_tallas',
        ];

        $base = $this->directorioTablaTallas;
        foreach ($mapas as $prefijo => $directorio) {
            if (strpos($rutaNormalizada, $prefijo) === 0) {
                $rutaNormalizada = substr($rutaNormalizada, strlen($prefijo)) ?: '';
                $base = $directorio;
                break;
            }
        }

        if ($rutaNormalizada === '') {
            return;
        }

        $candidatos = array_unique(array_filter([
            $base,
            $this->directorioTablaTallas,
            ROOT_PATH . '/public/assets/uploads/tabla_tallas',
            ROOT_PATH . '/public/uploads/tabla_tallas',
        ]));

        $rutaNormalizada = ltrim($rutaNormalizada, '/');

        foreach ($candidatos as $directorioBase) {
            $directorioBase = rtrim((string) $directorioBase, '/');
            if ($directorioBase === '') {
                continue;
            }

            $archivo = $directorioBase . '/' . $rutaNormalizada;
            $baseReal = realpath($directorioBase);
            $directorioArchivo = realpath(dirname($archivo));

            if ($baseReal !== false && $directorioArchivo !== false && strpos($directorioArchivo, $baseReal) !== 0) {
                continue;
            }

            if (is_file($archivo)) {
                @unlink($archivo);

                return;
            }
        }
    }

    private function responderJson(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
