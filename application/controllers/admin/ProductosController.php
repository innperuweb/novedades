<?php

declare(strict_types=1);

final class ProductosController extends AdminBaseController
{
    private const MIMES_PERMITIDOS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    private const MAX_IMAGENES = 10;
    private const MAX_IMAGEN_BYTES = 5242880; // 5 MB

    private AdminProductoModel $productoModel;
    private AdminCategoriaModel $categoriaModel;
    private string $directorioUploads;
    private string $directorioUploadsLegacy;
    private string $directorioImagenes;
    private string $rutaPublicaUploads;
    private string $directorioTablaTallas;
    private bool $debugUploads;
    private bool $registroLocalUploads; // FIX: upload imagenes
    private ?string $rutaLogDiagnostico; // FIX: upload imagenes
    private bool $diagnosticoRegistrado = false; // FIX: upload imagenes

    public function __construct()
    {
        $this->productoModel = new AdminProductoModel();
        $this->categoriaModel = new AdminCategoriaModel();
        $this->directorioUploads = ROOT_PATH . '/public/assets/uploads/productos';
        $this->directorioUploadsLegacy = ROOT_PATH . '/public/uploads/productos';
        $this->directorioImagenes = $this->directorioUploads;
        $this->rutaPublicaUploads = 'uploads/productos/';
        $this->directorioTablaTallas = ROOT_PATH . '/public/assets/uploads/tabla_tallas';
        $this->debugUploads = filter_var(getenv('DEBUG_UPLOADS') ?: '0', FILTER_VALIDATE_BOOL);
        $entorno = strtolower((string) (getenv('APP_ENV') ?: 'production')); // FIX: upload imagenes
        $this->registroLocalUploads = in_array($entorno, ['local', 'development'], true); // FIX: upload imagenes
        $this->rutaLogDiagnostico = $this->registroLocalUploads ? ROOT_PATH . '/storage/logs/upload_debug.log' : null; // FIX: upload imagenes
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

        $totalImagenesSubidas = 0; // FIX: upload imagenes
        if ($imagenesPreparadas['imagenes'] !== []) {
            $resultadoImagenes = $this->procesarImagenesSubidas(
                $nuevoId,
                $imagenesPreparadas['imagenes'],
                $indicePrincipalNuevo,
                false
            );

            if (!empty($resultadoImagenes['errores'])) {
                admin_set_flash('warning', implode(' ', $resultadoImagenes['errores']));
            } else {
                $totalImagenesSubidas = (int) ($resultadoImagenes['total'] ?? 0);
            }
        }

        $mensajeExito = 'Producto creado correctamente.'; // FIX: upload imagenes
        if ($totalImagenesSubidas > 0) {
            $mensajeExito .= ' Se subieron ' . $totalImagenesSubidas . ' imágenes y se registraron en la galería.';
        }
        admin_set_flash('success', $mensajeExito);
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

        $totalImagenesSubidas = 0; // FIX: upload imagenes
        if ($imagenesPreparadas['imagenes'] !== []) {
            $resultadoImagenes = $this->procesarImagenesSubidas(
                $productoId,
                $imagenesPreparadas['imagenes'],
                $indicePrincipalNuevo,
                $mantenerPrincipal
            );

            if (!empty($resultadoImagenes['errores'])) {
                admin_set_flash('warning', implode(' ', $resultadoImagenes['errores']));
            } else {
                $totalImagenesSubidas = (int) ($resultadoImagenes['total'] ?? 0);
            }
        }

        $mensajeExito = 'Producto actualizado correctamente.'; // FIX: upload imagenes
        if ($totalImagenesSubidas > 0) {
            $mensajeExito .= ' Se subieron ' . $totalImagenesSubidas . ' imágenes y se registraron en la galería.';
        }
        admin_set_flash('success', $mensajeExito);
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

    private function prepararImagenesDesdeRequest(): array
    {
        if (!$this->diagnosticoRegistrado) { // FIX: upload imagenes
            $this->registrarDiagnosticoUploads([ // FIX: upload imagenes
                'post_keys' => array_keys($_POST),
                'files_keys' => array_keys($_FILES),
                'ini_values' => [
                    'file_uploads' => ini_get('file_uploads'),
                    'upload_max_filesize' => ini_get('upload_max_filesize'),
                    'post_max_size' => ini_get('post_max_size'),
                    'max_file_uploads' => ini_get('max_file_uploads'),
                ],
                'imagenes' => $this->normalizarArchivosParaDiagnostico($_FILES['imagenes'] ?? null),
            ]);
            $this->diagnosticoRegistrado = true;
        }

        if (!isset($_FILES['imagenes']) || !is_array($_FILES['imagenes'])) {
            return ['imagenes' => [], 'errores' => []];
        }

        $imagenes = $_FILES['imagenes'];
        $componentes = [
            'name' => $imagenes['name'] ?? [],
            'tmp_name' => $imagenes['tmp_name'] ?? [],
            'error' => $imagenes['error'] ?? [],
            'size' => $imagenes['size'] ?? [],
        ];

        foreach ($componentes as $clave => $valor) {
            if (!is_array($valor)) {
                $componentes[$clave] = [$valor];
            }
        }

        $detalles = [];
        $nombres = array_values($componentes['name']);
        $temporal = array_values($componentes['tmp_name']);
        $errores = array_values($componentes['error']);
        $tamanos = array_values($componentes['size']);
        $total = count($nombres);

        for ($i = 0; $i < $total; $i++) {
            $detalles[] = [
                'name' => (string) ($nombres[$i] ?? ''),
                'tmp_name' => (string) ($temporal[$i] ?? ''),
                'error' => (int) ($errores[$i] ?? UPLOAD_ERR_NO_FILE),
                'size' => (int) ($tamanos[$i] ?? 0),
            ];
        }

        $detallesValidos = array_filter($detalles, static function ($detalle): bool {
            return (int) ($detalle['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
        });

        if (count($detallesValidos) > self::MAX_IMAGENES) {
            return ['imagenes' => [], 'errores' => ['Solo se permiten ' . self::MAX_IMAGENES . ' imágenes por guardado.']];
        }

        $archivosPreparados = [];
        $erroresEncontrados = [];
        $finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : null;

        foreach ($detalles as $detalle) {
            $codigoError = $detalle['error'];
            if ($codigoError === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($codigoError !== UPLOAD_ERR_OK) {
                $erroresEncontrados[] = 'Ocurrió un error al subir una de las imágenes.';
                continue;
            }

            $tmp = $detalle['tmp_name'];
            if ($tmp === '' || !is_uploaded_file($tmp)) {
                $erroresEncontrados[] = 'No se pudo procesar una de las imágenes seleccionadas.';
                continue;
            }

            $tamano = (int) ($detalle['size'] ?? 0);
            if ($tamano > self::MAX_IMAGEN_BYTES) {
                $erroresEncontrados[] = 'Una de las imágenes supera el tamaño máximo permitido (5 MB).';
                continue;
            }

            $info = @getimagesize($tmp);
            if ($info === false) {
                $erroresEncontrados[] = 'Una de las imágenes seleccionadas no es válida.';
                continue;
            }

            $mime = is_array($info) ? (string) ($info['mime'] ?? '') : '';
            $mimeReal = '';
            if (is_resource($finfo) || $finfo instanceof \finfo) {
                $mimeReal = (string) @finfo_file($finfo, $tmp);
            }

            if ($mimeReal !== '') {
                $mime = $mimeReal;
            }

            if ($mime === '' || !array_key_exists($mime, self::MIMES_PERMITIDOS)) {
                $erroresEncontrados[] = 'Una de las imágenes tiene un formato no permitido. Usa JPG, PNG o WEBP.';
                continue;
            }

            $nombreOriginal = $detalle['name'] !== '' ? $detalle['name'] : 'imagen-producto';
            $extension = $this->obtenerExtensionDesdeMime($mime, $nombreOriginal);
            $nombreArchivo = $this->generarNombreArchivo($nombreOriginal, $extension);
            $archivosPreparados[] = [
                'tmp_name' => $tmp,
                'nombre' => $nombreArchivo,
                'mime' => $mime,
            ];
        }

        if (is_resource($finfo) || $finfo instanceof \finfo) {
            finfo_close($finfo);
        }

        if ($erroresEncontrados !== []) {
            return ['imagenes' => [], 'errores' => $erroresEncontrados];
        }

        return ['imagenes' => $archivosPreparados, 'errores' => []];
    }

    private function procesarImagenesSubidas(int $productoId, array $imagenesPreparadas, ?int $indicePrincipal, bool $mantenerPrincipal): array
    {
        if ($imagenesPreparadas === []) {
            return ['errores' => [], 'total' => 0]; // FIX: upload imagenes
        }

        try {
            $directorio = $this->asegurarDirectorioImagenesProducto($productoId);
        } catch (\RuntimeException $exception) {
            $this->registrarDebugUploads('Directorio no disponible para subir imágenes.', [
                'producto' => $productoId,
                'mensaje' => $exception->getMessage(),
            ]);

            return ['errores' => [$exception->getMessage()], 'total' => 0]; // FIX: upload imagenes
        }
        $rutasGuardadas = [];
        $errores = [];

        foreach ($imagenesPreparadas as $imagen) {
            $tmp = $imagen['tmp_name'] ?? '';
            $nombre = $imagen['nombre'] ?? '';

            if ($tmp === '' || $nombre === '') {
                $errores[] = 'No se pudo procesar una de las imágenes seleccionadas.';
                break;
            }

            $destino = rtrim($directorio, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $nombre;

            $this->registrarDebugUploads('Intentando mover imagen subida.', [
                'tmp' => $tmp,
                'destino' => $destino,
            ]);

            $resultadoMovimiento = @move_uploaded_file($tmp, $destino);

            $this->registrarDebugUploads('Resultado de move_uploaded_file.', [
                'tmp' => $tmp,
                'destino' => $destino,
                'exito' => $resultadoMovimiento,
                'existe_tmp' => is_file($tmp),
                'directorio_existe' => is_dir(dirname($destino)),
                'directorio_writable' => is_writable(dirname($destino)),
            ]);

            if (!$resultadoMovimiento) {
                $ultimoError = error_get_last();
                $mensajeError = 'No se pudo guardar una de las imágenes en el servidor.';
                if ($ultimoError !== null) {
                    $mensajeError .= ' Detalle: ' . ($ultimoError['message'] ?? 'desconocido');
                }
                $errores[] = $mensajeError;
                break;
            }

            $rutasGuardadas[] = [
                'nombre' => $nombre,
                'ruta' => $this->rutaPublicaUploads . $nombre,
            ];
        }

        if ($errores !== []) {
            foreach ($rutasGuardadas as $ruta) {
                $this->eliminarArchivoFisico((string) ($ruta['ruta'] ?? ''));
            }

            return ['errores' => $errores, 'total' => 0]; // FIX: upload imagenes
        }

        $this->registrarDebugUploads('Rutas de imágenes guardadas antes de persistir.', [
            'producto' => $productoId,
            'imagenes' => $rutasGuardadas,
            'indicePrincipal' => $indicePrincipal,
            'mantenerPrincipal' => $mantenerPrincipal,
        ]);

        $this->productoModel->guardarImagenes($productoId, $rutasGuardadas, $indicePrincipal, $mantenerPrincipal);

        $this->registrarDebugUploads('Persistencia de imágenes completada.', [
            'producto' => $productoId,
            'total' => count($rutasGuardadas),
        ]);

        return ['errores' => [], 'total' => count($rutasGuardadas)]; // FIX: upload imagenes
    }

    private function registrarDebugUploads(string $mensaje, array $contexto = []): void
    {
        if (!$this->debugUploads) {
            return;
        }

        $payload = $contexto === [] ? '' : ' ' . json_encode($contexto, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        error_log('[ProductosController] ' . $mensaje . $payload);
    }

    private function registrarDiagnosticoUploads(array $contexto): void
    {
        if (!$this->registroLocalUploads || $this->rutaLogDiagnostico === null) { // FIX: upload imagenes
            return;
        }

        $directorio = dirname($this->rutaLogDiagnostico);
        if (!is_dir($directorio)) {
            if (!mkdir($directorio, 0755, true) && !is_dir($directorio)) { // FIX: upload imagenes
                return;
            }
        }

        $entrada = sprintf(
            "[%s] %s",
            date('Y-m-d H:i:s'),
            $contexto === [] ? 'Diagnóstico de subida' : 'Diagnóstico de subida ' . json_encode($contexto, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );

        @file_put_contents($this->rutaLogDiagnostico, $entrada . PHP_EOL, FILE_APPEND);
    }

    private function normalizarArchivosParaDiagnostico($entrada): array
    {
        if (!is_array($entrada)) {
            return [];
        }

        $componentes = [
            'name' => $entrada['name'] ?? [],
            'tmp_name' => $entrada['tmp_name'] ?? [],
            'error' => $entrada['error'] ?? [],
            'size' => $entrada['size'] ?? [],
        ];

        foreach ($componentes as $clave => $valor) {
            if (!is_array($valor)) {
                $componentes[$clave] = [$valor];
            }
        }

        $nombres = array_values($componentes['name']);
        $temporales = array_values($componentes['tmp_name']);
        $errores = array_values($componentes['error']);
        $tamanos = array_values($componentes['size']);
        $total = max(count($nombres), count($temporales), count($errores), count($tamanos));

        $resultado = [];
        for ($i = 0; $i < $total; $i++) {
            $resultado[] = [
                'name' => (string) ($nombres[$i] ?? ''),
                'tmp_name' => (string) ($temporales[$i] ?? ''),
                'error' => (int) ($errores[$i] ?? UPLOAD_ERR_NO_FILE),
                'size' => (int) ($tamanos[$i] ?? 0),
            ];
        }

        return $resultado;
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
        $ruta = $this->directorioUploads;

        if (!is_dir($ruta)) {
            if (!mkdir($ruta, 0775, true) && !is_dir($ruta)) { // FIX: upload imagenes
                throw new \RuntimeException('No se pudo crear el directorio base de imágenes de productos.');
            }
            @chmod($ruta, 0775);
        }

        if (!is_writable($ruta)) {
            @chmod($ruta, 0775);
        }

        if (!is_writable($ruta)) {
            throw new \RuntimeException('El directorio base de imágenes de productos no es escribible.');
        }

        return $ruta;
    }

    private function asegurarDirectorioTablaTallas(): string
    {
        $ruta = $this->directorioTablaTallas;

        if (!is_dir($ruta)) {
            if (!mkdir($ruta, 0755, true) && !is_dir($ruta)) { // FIX: upload imagenes
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

        return $ruta;
    }

    private function asegurarDirectorioImagenesProducto(int $productoId): string
    {
        $ruta = $this->asegurarDirectorioUploads();

        if (!is_writable($ruta)) {
            @chmod($ruta, 0775);
        }

        if (!is_writable($ruta)) {
            throw new \RuntimeException('El directorio para las imágenes del producto no es escribible.');
        }

        return rtrim($ruta, '/');
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

        if (strpos($rutaNormalizada, 'public/uploads/productos/') === 0) {
            $rutaNormalizada = substr($rutaNormalizada, strlen('public/uploads/productos/')) ?: '';
            $base = $this->directorioUploadsLegacy;
        } elseif (strpos($rutaNormalizada, 'uploads/productos/') === 0) {
            $rutaNormalizada = substr($rutaNormalizada, strlen('uploads/productos/')) ?: '';
            $base = $this->directorioUploads;
        } elseif (strpos($rutaNormalizada, 'uploads/products/') === 0) {
            $rutaNormalizada = substr($rutaNormalizada, strlen('uploads/products/')) ?: '';
            $base = $this->directorioImagenes;
        } elseif (strpos($rutaNormalizada, 'products/') === 0) {
            $rutaNormalizada = substr($rutaNormalizada, strlen('products/')) ?: '';
            $base = $this->directorioImagenes;
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

        if (!is_file($archivo)) {
            if ($base === $this->directorioUploadsLegacy) {
                $alterno = rtrim($this->directorioUploads, '/') . '/' . $rutaNormalizada;
                if (is_file($alterno)) {
                    $archivo = $alterno;
                }
            } elseif ($base === $this->directorioUploads) {
                $alterno = rtrim($this->directorioUploadsLegacy, '/') . '/' . $rutaNormalizada;
                if (is_file($alterno)) {
                    $archivo = $alterno;
                }
            }
        }

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
