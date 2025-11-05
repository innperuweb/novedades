<?php

declare(strict_types=1);

final class ProductosController extends AdminBaseController
{
    private const MIMES_PERMITIDOS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    private AdminProductoModel $productoModel;
    private AdminCategoriaModel $categoriaModel;
    private string $directorioUploads;
    private string $directorioImagenes;
    private string $directorioTablaTallas;

    public function __construct()
    {
        $this->productoModel = new AdminProductoModel();
        $this->categoriaModel = new AdminCategoriaModel();
        $this->directorioUploads = ROOT_PATH . '/public/assets/uploads/productos';
        $this->directorioImagenes = ROOT_PATH . '/public/assets/uploads/products';
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
            'tabla_tallas' => '',
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
        $imagenesPreparadas = $this->prepararImagenesDesdeRequest();

        if ($errores !== []) {
            $datos['subcategorias'] = $subcategorias;
            $this->mostrarFormularioProducto($datos, $errores, false);

            return;
        }

        if ($imagenesPreparadas['errores'] !== []) {
            admin_set_flash('danger', implode(' ', $imagenesPreparadas['errores']));
            $datos['subcategorias'] = $subcategorias;
            $this->mostrarFormularioProducto($datos, [], false);

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

        $indicePrincipalNuevo = $this->obtenerIndicePrincipalNuevo(count($imagenesPreparadas['imagenes']));
        if ($indicePrincipalNuevo === null && $imagenesPreparadas['imagenes'] !== []) {
            $indicePrincipalNuevo = 0;
        }

        $nuevoId = $this->productoModel->crear($datos, $subcategorias);

        if ($imagenesPreparadas['imagenes'] !== []) {
            $resultadoImagenes = $this->procesarImagenesSubidas(
                $nuevoId,
                $imagenesPreparadas['imagenes'],
                $indicePrincipalNuevo,
                false
            );

            if (!empty($resultadoImagenes['errores'])) {
                admin_set_flash('warning', implode(' ', $resultadoImagenes['errores']));
            }
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
        $imagenesPreparadas = $this->prepararImagenesDesdeRequest();

        if ($errores !== []) {
            $datos['id'] = $productoId;
            $datos['subcategorias'] = $subcategorias;
            $this->mostrarFormularioProducto($datos, $errores, true);

            return;
        }

        if ($imagenesPreparadas['errores'] !== []) {
            admin_set_flash('danger', implode(' ', $imagenesPreparadas['errores']));
            $datos['id'] = $productoId;
            $datos['subcategorias'] = $subcategorias;
            $this->mostrarFormularioProducto($datos, [], true);

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

        $indicePrincipalNuevo = $this->obtenerIndicePrincipalNuevo(count($imagenesPreparadas['imagenes']));
        $tienePrincipalActual = $this->coleccionTienePrincipal($productoActual['imagenes'] ?? []);
        $mantenerPrincipal = $tienePrincipalActual && $indicePrincipalNuevo === null;

        if (!$tienePrincipalActual && $imagenesPreparadas['imagenes'] !== []) {
            if ($indicePrincipalNuevo === null) {
                $indicePrincipalNuevo = 0;
            }
            $mantenerPrincipal = false;
        }

        if ($indicePrincipalNuevo !== null) {
            $mantenerPrincipal = false;
        }

        $this->productoModel->actualizarProducto($productoId, $datos, $subcategorias);

        if ($imagenesPreparadas['imagenes'] !== []) {
            $resultadoImagenes = $this->procesarImagenesSubidas(
                $productoId,
                $imagenesPreparadas['imagenes'],
                $indicePrincipalNuevo,
                $mantenerPrincipal
            );

            if (!empty($resultadoImagenes['errores'])) {
                admin_set_flash('warning', implode(' ', $resultadoImagenes['errores']));
            }
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

    public function eliminarImagen(string $id, string $imagenId): void
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
        $imagenIdSanitizada = sanitize_int($imagenId);

        if ($productoId === null || $imagenIdSanitizada === null) {
            $this->responderJson(['success' => false, 'message' => 'Parámetros inválidos.'], 400);

            return;
        }

        $resultado = $this->productoModel->eliminarImagen($productoId, $imagenIdSanitizada);

        if ($resultado === null) {
            $this->responderJson(['success' => false, 'message' => 'La imagen indicada no existe.'], 404);

            return;
        }

        $this->eliminarArchivoFisico((string) ($resultado['ruta'] ?? ''));

        $nuevoPrincipalId = null;
        if (!empty($resultado['era_principal'])) {
            $nuevoPrincipalId = $this->productoModel->asignarPrincipalRestante($productoId);
        }

        $this->responderJson([
            'success' => true,
            'nuevoPrincipalId' => $nuevoPrincipalId,
        ]);
    }

    public function eliminarImagenAjax(string $imagenId): void
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

        $imagenIdSanitizada = sanitize_int($imagenId);
        if ($imagenIdSanitizada === null) {
            $this->responderJson(['success' => false, 'message' => 'Parámetros inválidos.'], 400);

            return;
        }

        $resultado = $this->productoModel->eliminarImagenPorId($imagenIdSanitizada);
        if ($resultado === null) {
            $this->responderJson(['success' => false, 'message' => 'La imagen indicada no existe.'], 404);

            return;
        }

        $this->eliminarArchivoFisico((string) ($resultado['ruta'] ?? ''));

        $nuevoPrincipalId = null;
        $productoId = (int) ($resultado['producto_id'] ?? 0);
        if (!empty($resultado['era_principal']) && $productoId > 0) {
            $nuevoPrincipalId = $this->productoModel->asignarPrincipalRestante($productoId);
        }

        $this->responderJson([
            'success' => true,
            'nuevoPrincipalId' => $nuevoPrincipalId,
        ]);
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

    private function obtenerIndicePrincipalNuevo(int $cantidadArchivos): ?int
    {
        if ($cantidadArchivos <= 0) {
            return null;
        }

        $valor = $_POST['imagen_principal_nueva'] ?? '';
        if ($valor === '' && $valor !== '0') {
            return null;
        }

        $indice = filter_var($valor, FILTER_VALIDATE_INT);
        if ($indice === false) {
            return null;
        }

        if ($indice < 0 || $indice >= $cantidadArchivos) {
            return null;
        }

        return $indice;
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
        if ($mime === '' || !array_key_exists($mime, self::MIMES_PERMITIDOS)) {
            return ['error' => 'El archivo de la tabla de tallas debe ser JPG, PNG o WEBP.'];
        }

        $extension = $this->obtenerExtensionDesdeMime($mime, (string) ($archivo['name'] ?? 'tabla-tallas'));
        $nombreArchivo = $this->generarNombreArchivo((string) ($archivo['name'] ?? 'tabla-tallas'), $extension);
        $destino = $this->asegurarDirectorioTablaTallas() . '/' . $nombreArchivo;

        if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
            return ['error' => 'No se pudo guardar la tabla de tallas en el servidor.'];
        }

        if (!empty($productoActual['tabla_tallas'])) {
            $this->eliminarArchivoFisico((string) $productoActual['tabla_tallas']);
        }

        return ['archivo' => 'uploads/tabla_tallas/' . $nombreArchivo];
    }

    private function prepararImagenesDesdeRequest(): array
    {
        if (!isset($_FILES['imagenes']) || !is_array($_FILES['imagenes'])) {
            return ['imagenes' => [], 'errores' => []];
        }

        $imagenes = $_FILES['imagenes'];
        $nombres = $imagenes['name'] ?? [];
        $temporal = $imagenes['tmp_name'] ?? [];
        $errores = $imagenes['error'] ?? [];
        $cantidad = is_array($nombres) ? count($nombres) : 0;

        $archivosPreparados = [];
        $erroresEncontrados = [];

        for ($i = 0; $i < $cantidad; $i++) {
            $codigoError = (int) ($errores[$i] ?? UPLOAD_ERR_NO_FILE);
            if ($codigoError === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($codigoError !== UPLOAD_ERR_OK) {
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

            $mime = is_array($info) ? (string) ($info['mime'] ?? '') : '';
            if ($mime === '' || !array_key_exists($mime, self::MIMES_PERMITIDOS)) {
                $erroresEncontrados[] = 'Una de las imágenes tiene un formato no permitido. Usa JPG, PNG o WEBP.';
                continue;
            }

            $extension = $this->obtenerExtensionDesdeMime($mime, (string) ($nombres[$i] ?? 'imagen-producto'));
            $nombreArchivo = $this->generarNombreArchivo((string) ($nombres[$i] ?? 'imagen-producto'), $extension);
            $archivosPreparados[] = [
                'tmp_name' => $tmp,
                'nombre' => $nombreArchivo,
            ];
        }

        if ($erroresEncontrados !== []) {
            return ['imagenes' => [], 'errores' => $erroresEncontrados];
        }

        return ['imagenes' => $archivosPreparados, 'errores' => []];
    }

    private function procesarImagenesSubidas(int $productoId, array $imagenesPreparadas, ?int $indicePrincipal, bool $mantenerPrincipal): array
    {
        if ($imagenesPreparadas === []) {
            return ['errores' => []];
        }

        $directorio = $this->asegurarDirectorioImagenesProducto($productoId);
        $rutasGuardadas = [];
        $errores = [];

        foreach ($imagenesPreparadas as $imagen) {
            $tmp = $imagen['tmp_name'] ?? '';
            $nombre = $imagen['nombre'] ?? '';

            if ($tmp === '' || $nombre === '') {
                $errores[] = 'No se pudo procesar una de las imágenes seleccionadas.';
                break;
            }

            $destino = $directorio . '/' . $nombre;

            if (!move_uploaded_file($tmp, $destino)) {
                $errores[] = 'No se pudo guardar una de las imágenes en el servidor.';
                break;
            }

            $rutasGuardadas[] = [
                'ruta' => 'products/' . $productoId . '/' . $nombre,
            ];
        }

        if ($errores !== []) {
            foreach ($rutasGuardadas as $ruta) {
                $this->eliminarArchivoFisico((string) ($ruta['ruta'] ?? ''));
            }

            return ['errores' => $errores];
        }

        $this->productoModel->guardarImagenes($productoId, $rutasGuardadas, $indicePrincipal, $mantenerPrincipal);

        return ['errores' => []];
    }

    private function coleccionTienePrincipal(array $imagenes): bool
    {
        foreach ($imagenes as $imagen) {
            if (is_array($imagen) && (int) ($imagen['es_principal'] ?? 0) === 1) {
                return true;
            }
        }

        return false;
    }

    private function asegurarDirectorioUploads(): string
    {
        if (!is_dir($this->directorioUploads)) {
            mkdir($this->directorioUploads, 0755, true);
        }

        return $this->directorioUploads;
    }

    private function asegurarDirectorioTablaTallas(): string
    {
        if (!is_dir($this->directorioTablaTallas)) {
            mkdir($this->directorioTablaTallas, 0755, true);
        }

        return $this->directorioTablaTallas;
    }

    private function asegurarDirectorioImagenesProducto(int $productoId): string
    {
        if (!is_dir($this->directorioImagenes)) {
            mkdir($this->directorioImagenes, 0755, true);
        }

        $productoId = max(1, $productoId);
        $ruta = rtrim($this->directorioImagenes, '/') . '/' . $productoId;

        if (!is_dir($ruta)) {
            mkdir($ruta, 0755, true);
        }

        return $ruta;
    }

    private function generarNombreArchivo(string $nombreOriginal, ?string $extensionForzada = null): string
    {
        $extension = $extensionForzada ?? '';

        if ($extension === '') {
            $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
            $extension = $extension !== '' ? '.' . strtolower($extension) : '';
        }

        return uniqid('producto_', true) . $extension;
    }

    private function obtenerExtensionDesdeMime(string $mime, string $nombreOriginal): string
    {
        $mime = strtolower($mime);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        $permitidos = array_values(self::MIMES_PERMITIDOS);
        if ($extension === '' || !in_array($extension, $permitidos, true)) {
            $extension = self::MIMES_PERMITIDOS[$mime] ?? 'jpg';
        }

        return '.' . $extension;
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
        $base = $this->directorioUploads;

        if (strpos($rutaNormalizada, 'uploads/products/') === 0) {
            $rutaNormalizada = substr($rutaNormalizada, strlen('uploads/products/')) ?: '';
            $base = $this->directorioImagenes;
        } elseif (strpos($rutaNormalizada, 'products/') === 0) {
            $rutaNormalizada = substr($rutaNormalizada, strlen('products/')) ?: '';
            $base = $this->directorioImagenes;
        } elseif (strpos($rutaNormalizada, 'uploads/productos/') === 0) {
            $rutaNormalizada = substr($rutaNormalizada, strlen('uploads/productos/')) ?: '';
            $base = $this->directorioUploads;
        } elseif (strpos($rutaNormalizada, 'uploads/tabla_tallas/') === 0) {
            $rutaNormalizada = substr($rutaNormalizada, strlen('uploads/tabla_tallas/')) ?: '';
            $base = $this->directorioTablaTallas;
        } elseif (strpos($rutaNormalizada, 'tabla_tallas/') === 0) {
            $rutaNormalizada = substr($rutaNormalizada, strlen('tabla_tallas/')) ?: '';
            $base = $this->directorioTablaTallas;
        }

        if ($rutaNormalizada === '') {
            return;
        }

        $archivo = rtrim($base, '/') . '/' . $rutaNormalizada;

        $baseReal = realpath($base);
        $directorioArchivo = realpath(dirname($archivo));

        if ($baseReal !== false && $directorioArchivo !== false && strpos($directorioArchivo, $baseReal) !== 0) {
            return;
        }

        if (is_file($archivo)) {
            @unlink($archivo);
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
