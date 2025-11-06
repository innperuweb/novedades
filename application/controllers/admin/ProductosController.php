<?php

declare(strict_types=1);

final class ProductosController extends AdminBaseController
{
    private const TABLA_TALLAS_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    private const LIMITE_IMAGENES_PRODUCTO = 10;

    private const IMAGENES_PRODUCTO_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    private AdminProductoModel $productoModel;
    private AdminCategoriaModel $categoriaModel;
    private string $directorioTablaTallas;
    private string $directorioImagenesProducto;
    private ?array $ultimaImagenGuardada = null;

    public function __construct()
    {
        $this->productoModel = new AdminProductoModel();
        $this->categoriaModel = new AdminCategoriaModel();
        $this->directorioTablaTallas = ROOT_PATH . '/public/assets/uploads/tabla_tallas';
        $this->directorioImagenesProducto = ROOT_PATH . '/public/assets/uploads/productos';
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

    public function subirImagen(string $id): void
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

        if (!isset($_FILES['imagen_producto']) || !is_array($_FILES['imagen_producto'])) {
            $this->responderJson(['success' => false, 'message' => 'No se recibió ninguna imagen.'], 400);

            return;
        }

        try {
            $this->guardarImagenProducto($productoId, $_FILES['imagen_producto']);
            $imagen = $this->ultimaImagenGuardada;

            if ($imagen === null) {
                throw new \RuntimeException('No se pudo registrar la imagen.');
            }

            $imagenesActuales = $this->productoModel->obtenerImagenesPorProducto($productoId);

            $this->responderJson([
                'success' => true,
                'imagen' => [
                    'id' => (int) ($imagen['id'] ?? 0),
                    'url' => $this->construirUrlImagen((string) ($imagen['ruta'] ?? '')),
                    'nombre' => (string) ($imagen['nombre'] ?? ''),
                    'es_principal' => (int) ($imagen['es_principal'] ?? 0),
                    'orden' => (int) ($imagen['orden'] ?? 0),
                ],
                'imagenes' => array_map(function ($item): array {
                    return [
                        'id' => (int) ($item['id'] ?? 0),
                        'url' => $this->construirUrlImagen((string) ($item['ruta'] ?? '')),
                        'nombre' => (string) ($item['nombre'] ?? ''),
                        'es_principal' => (int) ($item['es_principal'] ?? 0),
                        'orden' => (int) ($item['orden'] ?? 0),
                    ];
                }, $imagenesActuales),
            ]);
        } catch (\RuntimeException $exception) {
            $this->responderJson(['success' => false, 'message' => $exception->getMessage()], 400);
        } catch (\Throwable $exception) {
            $this->responderJson(['success' => false, 'message' => 'No se pudo subir la imagen.'], 500);
        }
    }

    public function eliminarImagen(string $id): void
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

        $imagenId = sanitize_int($id);
        if ($imagenId === null) {
            $this->responderJson(['success' => false, 'message' => 'Imagen inválida.'], 400);

            return;
        }

        $imagen = $this->productoModel->obtenerImagenPorId($imagenId);
        if ($imagen === null) {
            $this->responderJson(['success' => false, 'message' => 'Imagen no encontrada.'], 404);

            return;
        }

        $productoId = (int) ($imagen['producto_id'] ?? 0);
        $ruta = (string) ($imagen['ruta'] ?? '');

        if (!$this->productoModel->eliminarImagen($imagenId)) {
            $this->responderJson(['success' => false, 'message' => 'No se pudo eliminar la imagen.'], 500);

            return;
        }

        if ($ruta !== '') {
            $this->eliminarArchivoFisico($ruta);
        }

        $this->productoModel->reordenarImagenes($productoId);
        $this->productoModel->asegurarImagenPrincipal($productoId);

        $imagenesRestantes = $this->productoModel->obtenerImagenesPorProducto($productoId);

        $this->responderJson([
            'success' => true,
            'imagenes' => array_map(function ($item): array {
                return [
                    'id' => (int) ($item['id'] ?? 0),
                    'url' => $this->construirUrlImagen((string) ($item['ruta'] ?? '')),
                    'nombre' => (string) ($item['nombre'] ?? ''),
                    'es_principal' => (int) ($item['es_principal'] ?? 0),
                    'orden' => (int) ($item['orden'] ?? 0),
                ];
            }, $imagenesRestantes),
        ]);
    }

    private function mostrarFormularioProducto(array $producto, array $errores, bool $esEdicion): void
    {
        $categorias = $this->categoriaModel->listarCategorias();
        $subcategorias = $this->categoriaModel->listarSubcategorias();

        $imagenes = [];
        $productoId = (int) ($producto['id'] ?? 0);
        if ($productoId > 0) {
            try {
                $imagenes = $this->productoModel->obtenerImagenesPorProducto($productoId);
            } catch (\Throwable $exception) {
                $imagenes = [];
            }
        }

        $this->render('productos/form', [
            'title' => $esEdicion ? 'Editar producto' : 'Nuevo producto',
            'producto' => $producto,
            'categorias' => $categorias,
            'subcategorias' => $subcategorias,
            'errores' => $errores,
            'esEdicion' => $esEdicion,
            'imagenes' => $imagenes,
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
            'descripcion' => (string) ($_POST['descripcion'] ?? ''),
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

    private function asegurarDirectorioImagenesProducto(int $productoId): string
    {
        $base = rtrim($this->directorioImagenesProducto, '/');

        if (!is_dir($base)) {
            if (!mkdir($base, 0755, true) && !is_dir($base)) {
                throw new \RuntimeException('No se pudo crear el directorio base de imágenes de productos.');
            }
            @chmod($base, 0755);
        }

        $ruta = $base . '/' . $productoId;

        if (!is_dir($ruta)) {
            if (!mkdir($ruta, 0755, true) && !is_dir($ruta)) {
                throw new \RuntimeException('No se pudo crear el directorio de imágenes del producto.');
            }
            @chmod($ruta, 0755);
        }

        if (!is_writable($ruta)) {
            @chmod($ruta, 0755);
        }

        if (!is_writable($ruta)) {
            throw new \RuntimeException('El directorio de imágenes del producto no es escribible.');
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

    private function generarNombreImagenProducto(string $nombreOriginal, string $extension): string
    {
        $extension = ltrim($extension, '.');
        if ($extension === '') {
            $extension = 'jpg';
        }

        $base = pathinfo($nombreOriginal, PATHINFO_FILENAME);
        $base = $base !== '' ? preg_replace('/[^a-z0-9_-]+/i', '-', $base) : 'imagen';
        $base = trim((string) $base, '-');
        if ($base === '') {
            $base = 'imagen';
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

    private function obtenerExtensionImagenProducto(string $mime, string $nombreOriginal): string
    {
        $mime = strtolower($mime);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        if ($extension === '' || !in_array($extension, self::IMAGENES_PRODUCTO_MIMES, true)) {
            $extension = self::IMAGENES_PRODUCTO_MIMES[$mime] ?? 'jpg';
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
            'uploads/productos/' => $this->directorioImagenesProducto,
            'productos/' => $this->directorioImagenesProducto,
            'public/uploads/productos/' => ROOT_PATH . '/public/uploads/productos',
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
            $this->directorioImagenesProducto,
            ROOT_PATH . '/public/assets/uploads/productos',
            ROOT_PATH . '/public/uploads/productos',
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

    private function guardarImagenProducto(int $productoId, array $archivo): void
    {
        $this->ultimaImagenGuardada = null;

        $cantidadActual = $this->productoModel->contarImagenesPorProducto($productoId);
        if ($cantidadActual >= self::LIMITE_IMAGENES_PRODUCTO) {
            throw new \RuntimeException('Se alcanzó el límite de imágenes permitidas.');
        }

        $error = (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error === UPLOAD_ERR_NO_FILE) {
            throw new \RuntimeException('Seleccione una imagen para subir.');
        }

        if ($error !== UPLOAD_ERR_OK || empty($archivo['tmp_name']) || !is_uploaded_file((string) $archivo['tmp_name'])) {
            throw new \RuntimeException('No se pudo subir la imagen seleccionada.');
        }

        $peso = (int) ($archivo['size'] ?? 0);
        if ($peso <= 0 || $peso > 2 * 1024 * 1024) {
            throw new \RuntimeException('La imagen debe pesar hasta 2 MB.');
        }

        $info = @getimagesize((string) $archivo['tmp_name']);
        if ($info === false) {
            throw new \RuntimeException('El archivo debe ser una imagen válida.');
        }

        $mime = is_array($info) ? (string) ($info['mime'] ?? '') : '';
        if ($mime === '' || !array_key_exists($mime, self::IMAGENES_PRODUCTO_MIMES)) {
            throw new \RuntimeException('Formato de imagen no permitido. Solo JPG, PNG o WEBP.');
        }

        $extension = $this->obtenerExtensionImagenProducto($mime, (string) ($archivo['name'] ?? 'imagen-producto'));
        $nombreArchivo = $this->generarNombreImagenProducto((string) ($archivo['name'] ?? 'imagen-producto'), $extension);

        $directorio = $this->asegurarDirectorioImagenesProducto($productoId);
        $destino = $directorio . '/' . $nombreArchivo;

        if (!move_uploaded_file((string) $archivo['tmp_name'], $destino)) {
            throw new \RuntimeException('No se pudo guardar la imagen en el servidor.');
        }

        @chmod($destino, 0644);

        $rutaRelativa = 'uploads/productos/' . $productoId . '/' . $nombreArchivo;
        $orden = $this->productoModel->obtenerSiguienteOrdenImagen($productoId);
        $hayPrincipal = $this->productoModel->tieneImagenPrincipal($productoId);
        $esPrincipal = $hayPrincipal ? 0 : 1;

        $nombreOriginal = (string) ($archivo['name'] ?? $nombreArchivo);
        $nombreOriginal = trim($nombreOriginal);
        if ($nombreOriginal === '') {
            $nombreOriginal = $nombreArchivo;
        }

        $imagenId = $this->productoModel->insertarImagenProducto(
            $productoId,
            $nombreOriginal,
            $rutaRelativa,
            $esPrincipal === 1,
            $orden
        );

        if ($imagenId <= 0) {
            @unlink($destino);

            throw new \RuntimeException('No se pudo registrar la imagen en la base de datos.');
        }

        $this->ultimaImagenGuardada = [
            'id' => $imagenId,
            'ruta' => $rutaRelativa,
            'nombre' => $nombreOriginal,
            'es_principal' => $esPrincipal,
            'orden' => $orden,
        ];
    }

    private function construirUrlImagen(string $ruta): string
    {
        $ruta = trim($ruta);

        if ($ruta === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $ruta) === 1) {
            return $ruta;
        }

        $rutaLimpia = ltrim($ruta, '/');

        if (strpos($rutaLimpia, 'public/assets/') === 0) {
            return base_url($rutaLimpia);
        }

        if (strpos($rutaLimpia, 'assets/') === 0) {
            return base_url('public/' . $rutaLimpia);
        }

        if (strpos($rutaLimpia, 'uploads/') === 0) {
            return asset_url($rutaLimpia);
        }

        if (strpos($rutaLimpia, 'productos/') === 0) {
            return asset_url('uploads/' . $rutaLimpia);
        }

        return asset_url('uploads/productos/' . $rutaLimpia);
    }

    private function responderJson(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
