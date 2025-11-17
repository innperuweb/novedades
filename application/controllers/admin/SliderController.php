<?php

declare(strict_types=1);

require_once APP_PATH . '/controllers/admin/AdminBaseController.php';
require_once APP_PATH . '/models/SliderModel.php';

final class SliderController extends AdminBaseController
{
    private const IMAGEN_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    private SliderModel $sliderModel;
    private string $directorioSlider;

    public function __construct()
    {
        $this->sliderModel = new SliderModel();
        $this->directorioSlider = ROOT_PATH . '/public/assets/uploads/slider';
    }

    public function obtenerVisibles(): array
    {
        $sql = "SELECT * FROM slider_home WHERE visible = 1 ORDER BY orden ASC";
        return DB::fetchAll($sql);
    }

    public function index(): void
    {
        $this->requireLogin();

        $sliders = $this->sliderModel->listar();

        $this->render('slider_listar', [
            'title' => 'Sliders',
            'sliders' => $sliders,
        ]);
    }

    public function crear(): void
    {
        $this->requireLogin();

        $this->mostrarFormulario([
            'titulo' => '',
            'subtitulo' => '',
            'boton_url' => '',
            'visible' => 1,
            'imagen' => '',
        ], [], false);
    }

    public function editar(string $id): void
    {
        $this->requireLogin();

        $sliderId = sanitize_int($id);
        if ($sliderId === null) {
            admin_set_flash('danger', 'Slider inválido.');
            $this->redirect('admin/slider');

            return;
        }

        $slider = $this->sliderModel->obtenerPorId($sliderId);
        if ($slider === null) {
            admin_set_flash('warning', 'Slider no encontrado.');
            $this->redirect('admin/slider');

            return;
        }

        $this->mostrarFormulario($slider, [], true);
    }

    public function guardar(): void
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect('admin/slider');

            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            $this->redirect('admin/slider');

            return;
        }

        $datos = $this->obtenerDatosFormulario();
        $archivoImagen = $_FILES['imagen'] ?? null;
        $errores = $this->validarDatos($datos, true, $archivoImagen);

        if ($errores !== []) {
            $this->mostrarFormulario($datos, $errores, false);

            return;
        }

        $sliderId = $this->sliderModel->crear(array_merge($datos, ['imagen' => '']));

        if ($sliderId <= 0) {
            admin_set_flash('danger', 'No se pudo crear el slider.');
            $this->redirect('admin/slider');

            return;
        }

        $resultadoImagen = $this->manejarImagen($sliderId, $archivoImagen, null);

        if (isset($resultadoImagen['error'])) {
            $this->sliderModel->eliminar($sliderId);
            admin_set_flash('danger', $resultadoImagen['error']);
            $this->mostrarFormulario($datos, [], false);

            return;
        }

        $this->sliderModel->actualizar($sliderId, array_merge($datos, [
            'imagen' => $resultadoImagen['ruta'] ?? '',
        ]));

        admin_set_flash('success', 'Slider creado correctamente.');
        $this->redirect('admin/slider');
    }

    public function actualizar(string $id): void
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect('admin/slider');

            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            $this->redirect('admin/slider');

            return;
        }

        $sliderId = sanitize_int($id);
        if ($sliderId === null) {
            admin_set_flash('danger', 'Slider inválido.');
            $this->redirect('admin/slider');

            return;
        }

        $sliderActual = $this->sliderModel->obtenerPorId($sliderId);
        if ($sliderActual === null) {
            admin_set_flash('warning', 'Slider no encontrado.');
            $this->redirect('admin/slider');

            return;
        }

        $datos = $this->obtenerDatosFormulario();
        $archivoImagen = $_FILES['imagen'] ?? null;
        $errores = $this->validarDatos($datos, false, $archivoImagen);

        if ($errores !== []) {
            $datos['id'] = $sliderId;
            $datos['imagen'] = $sliderActual['imagen'] ?? '';
            $this->mostrarFormulario($datos, $errores, true);

            return;
        }

        if ($this->hayNuevaImagen($archivoImagen)) {
            $resultadoImagen = $this->manejarImagen($sliderId, $archivoImagen, $sliderActual['imagen'] ?? null);

            if (isset($resultadoImagen['error'])) {
                admin_set_flash('danger', $resultadoImagen['error']);
                $datos['id'] = $sliderId;
                $datos['imagen'] = $sliderActual['imagen'] ?? '';
                $this->mostrarFormulario($datos, [], true);

                return;
            }

            $datos['imagen'] = $resultadoImagen['ruta'] ?? ($sliderActual['imagen'] ?? '');
        } else {
            $datos['imagen'] = $sliderActual['imagen'] ?? '';
        }

        $this->sliderModel->actualizar($sliderId, $datos);

        admin_set_flash('success', 'Slider actualizado correctamente.');
        $this->redirect('admin/slider');
    }

    public function eliminar(string $id): void
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect('admin/slider');

            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            $this->redirect('admin/slider');

            return;
        }

        $sliderId = sanitize_int($id);
        if ($sliderId === null) {
            admin_set_flash('danger', 'Slider inválido.');
            $this->redirect('admin/slider');

            return;
        }

        $slider = $this->sliderModel->obtenerPorId($sliderId);

        if ($slider !== null && $this->sliderModel->eliminar($sliderId)) {
            if (!empty($slider['imagen'])) {
                $this->eliminarImagenFisica((string) $slider['imagen']);
            }

            admin_set_flash('success', 'Slider eliminado correctamente.');
        } else {
            admin_set_flash('danger', 'No se pudo eliminar el slider.');
        }

        $this->redirect('admin/slider');
    }

    private function mostrarFormulario(array $slider, array $errores, bool $esEdicion): void
    {
        $this->render('slider_form', [
            'title' => $esEdicion ? 'Editar slider' : 'Crear slider',
            'slider' => $slider,
            'errores' => $errores,
            'esEdicion' => $esEdicion,
        ]);
    }

    private function obtenerDatosFormulario(): array
    {
        return [
            'titulo' => (string) ($_POST['titulo'] ?? ''),
            'subtitulo' => (string) ($_POST['subtitulo'] ?? ''),
            'boton_url' => (string) ($_POST['boton_url'] ?? ''),
            'visible' => isset($_POST['visible']) ? 1 : 0,
        ];
    }

    private function validarDatos(array $datos, bool $requiereImagen, ?array $archivoImagen): array
    {
        $errores = [];

        if (trim($datos['titulo']) === '') {
            $errores['titulo'] = 'El título es obligatorio.';
        }

        if ($requiereImagen && !$this->hayNuevaImagen($archivoImagen)) {
            $errores['imagen'] = 'La imagen es obligatoria.';
        }

        if ($this->hayNuevaImagen($archivoImagen)) {
            $validacionImagen = $this->validarImagen($archivoImagen);
            if ($validacionImagen !== null) {
                $errores['imagen'] = $validacionImagen;
            }
        }

        return $errores;
    }

    private function hayNuevaImagen(?array $archivoImagen): bool
    {
        if ($archivoImagen === null) {
            return false;
        }

        $error = (int) ($archivoImagen['error'] ?? UPLOAD_ERR_NO_FILE);

        return $error !== UPLOAD_ERR_NO_FILE;
    }

    private function validarImagen(array $archivo): ?string
    {
        $error = (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($error === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($error !== UPLOAD_ERR_OK || empty($archivo['tmp_name']) || !is_uploaded_file((string) $archivo['tmp_name'])) {
            return 'No se pudo subir la imagen. Inténtelo nuevamente.';
        }

        $info = @getimagesize((string) $archivo['tmp_name']);
        if ($info === false) {
            return 'El archivo debe ser una imagen válida.';
        }

        $mime = is_array($info) ? (string) ($info['mime'] ?? '') : '';
        if ($mime === '' || !array_key_exists($mime, self::IMAGEN_MIMES)) {
            return 'Formato de imagen no permitido. Use JPG, JPEG, PNG o WEBP.';
        }

        return null;
    }

    private function manejarImagen(int $sliderId, ?array $archivoImagen, ?string $imagenActual): array
    {
        if (!$this->hayNuevaImagen($archivoImagen)) {
            return ['ruta' => $imagenActual];
        }

        $errorImagen = $this->validarImagen($archivoImagen ?? []);
        if ($errorImagen !== null) {
            return ['error' => $errorImagen];
        }

        try {
            $directorio = $this->asegurarDirectorioSlider();
        } catch (\RuntimeException $exception) {
            return ['error' => $exception->getMessage()];
        }

        $timestamp = time();
        $nombreArchivo = 'slider-' . $sliderId . '-' . $timestamp . '.webp';
        $destino = $directorio . '/' . $nombreArchivo;

        $mime = (string) ($archivoImagen['type'] ?? '');
        $tmpName = (string) ($archivoImagen['tmp_name'] ?? '');

        if (!$this->convertirAWebp($tmpName, $mime, $destino)) {
            return ['error' => 'No se pudo procesar la imagen.'];
        }

        @chmod($destino, 0644);

        if ($imagenActual) {
            $this->eliminarImagenFisica($imagenActual);
        }

        return ['ruta' => 'uploads/slider/' . $nombreArchivo];
    }

    private function asegurarDirectorioSlider(): string
    {
        $ruta = rtrim($this->directorioSlider, '/');

        if (!is_dir($ruta)) {
            if (!mkdir($ruta, 0755, true) && !is_dir($ruta)) {
                throw new \RuntimeException('No se pudo crear el directorio para los sliders.');
            }
            @chmod($ruta, 0755);
        }

        if (!is_writable($ruta)) {
            @chmod($ruta, 0755);
        }

        if (!is_writable($ruta)) {
            throw new \RuntimeException('El directorio de sliders no es escribible.');
        }

        return $ruta;
    }

    private function convertirAWebp(string $origen, string $mime, string $destino): bool
    {
        $mime = strtolower(trim($mime));
        $image = null;

        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            $image = @imagecreatefromjpeg($origen);
        } elseif ($mime === 'image/png') {
            $image = @imagecreatefrompng($origen);
            if ($image !== false) {
                if (function_exists('imagepalettetotruecolor')) {
                    @imagepalettetotruecolor($image);
                }
                @imagealphablending($image, true);
                @imagesavealpha($image, true);
            }
        } elseif ($mime === 'image/webp') {
            $image = @imagecreatefromwebp($origen);
        }

        if ($image === false || $image === null) {
            return false;
        }

        $resultado = @imagewebp($image, $destino, 90);
        @imagedestroy($image);

        return $resultado === true;
    }

    private function eliminarImagenFisica(string $rutaRelativa): void
    {
        $rutaLimpia = ltrim($rutaRelativa, '/');
        $rutaCompleta = ROOT_PATH . '/public/assets/' . $rutaLimpia;

        if (is_file($rutaCompleta)) {
            @unlink($rutaCompleta);
        }
    }
}
