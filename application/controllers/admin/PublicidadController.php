<?php

declare(strict_types=1);

require_once APP_PATH . '/controllers/admin/AdminBaseController.php';
require_once APP_PATH . '/models/PublicidadModel.php';

final class PublicidadController extends AdminBaseController
{
    private const IMAGEN_MIMES = [
        'image/jpeg' => 'jpg',
        'image/jpg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    private PublicidadModel $publicidadModel;
    private string $directorioPublicidad;

    public function __construct()
    {
        $this->publicidadModel = new PublicidadModel();
        $this->directorioPublicidad = ROOT_PATH . '/public/assets/uploads/publicidad';
    }

    public function index(): void
    {
        $this->requireLogin();

        $publicidad = $this->publicidadModel->obtener();

        $this->render('publicidad_form', [
            'title' => 'Publicidad',
            'publicidad' => $publicidad,
            'errores' => [],
        ]);
    }

    public function guardar(): void
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect('admin/publicidad');

            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            $this->redirect('admin/publicidad');

            return;
        }

        $datos = [
            'titulo' => trim((string) ($_POST['titulo'] ?? '')),
            'subtitulo' => trim((string) ($_POST['subtitulo'] ?? '')),
            'texto' => trim((string) ($_POST['texto'] ?? '')),
        ];

        $archivoImagen = $_FILES['imagen'] ?? null;
        $publicidadActual = $this->publicidadModel->obtener();

        $errores = $this->validarDatos($datos, $archivoImagen);

        if ($errores !== []) {
            $this->render('publicidad_form', [
                'title' => 'Publicidad',
                'publicidad' => array_merge($publicidadActual ?? [], $datos),
                'errores' => $errores,
            ]);

            return;
        }

        $datos['imagen'] = (string) ($publicidadActual['imagen'] ?? '');

        if ($this->hayNuevaImagen($archivoImagen)) {
            $resultadoImagen = $this->manejarImagen($archivoImagen ?? [], $publicidadActual['imagen'] ?? null);

            if (isset($resultadoImagen['error'])) {
                $this->render('publicidad_form', [
                    'title' => 'Publicidad',
                    'publicidad' => array_merge($publicidadActual ?? [], $datos),
                    'errores' => ['imagen' => $resultadoImagen['error']],
                ]);

                return;
            }

            $datos['imagen'] = $resultadoImagen['ruta'] ?? $datos['imagen'];
        }

        $resultado = $this->publicidadModel->actualizar($datos);

        if ($resultado) {
            admin_set_flash('success', 'Publicidad actualizada correctamente.');
        } else {
            admin_set_flash('danger', 'No se pudo actualizar la publicidad.');
        }

        $this->redirect('admin/publicidad');
    }

    private function validarDatos(array $datos, ?array $archivoImagen): array
    {
        $errores = [];

        if ($datos['titulo'] === '') {
            $errores['titulo'] = 'El título es obligatorio.';
        }

        if ($datos['subtitulo'] === '') {
            $errores['subtitulo'] = 'El subtítulo es obligatorio.';
        }

        if ($datos['texto'] === '') {
            $errores['texto'] = 'El texto es obligatorio.';
        }

        if ($this->hayNuevaImagen($archivoImagen)) {
            $errorImagen = $this->validarImagen($archivoImagen ?? []);
            if ($errorImagen !== null) {
                $errores['imagen'] = $errorImagen;
            }
        }

        return $errores;
    }

    private function hayNuevaImagen(?array $archivo): bool
    {
        if (!is_array($archivo)) {
            return false;
        }

        return isset($archivo['tmp_name']) && is_uploaded_file((string) $archivo['tmp_name']);
    }

    private function validarImagen(array $archivo): ?string
    {
        $error = $archivo['error'] ?? UPLOAD_ERR_NO_FILE;

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

    private function manejarImagen(array $archivoImagen, ?string $imagenActual): array
    {
        $errorImagen = $this->validarImagen($archivoImagen);
        if ($errorImagen !== null) {
            return ['error' => $errorImagen];
        }

        try {
            $directorio = $this->asegurarDirectorioPublicidad();
        } catch (\RuntimeException $exception) {
            return ['error' => $exception->getMessage()];
        }

        $timestamp = time();
        $nombreArchivo = 'publicidad-' . $timestamp . '.webp';
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

        return ['ruta' => 'public/assets/uploads/publicidad/' . $nombreArchivo];
    }

    private function asegurarDirectorioPublicidad(): string
    {
        $ruta = rtrim($this->directorioPublicidad, '/');

        if (!is_dir($ruta)) {
            if (!mkdir($ruta, 0755, true) && !is_dir($ruta)) {
                throw new \RuntimeException('No se pudo crear el directorio para la publicidad.');
            }
            @chmod($ruta, 0755);
        }

        if (!is_writable($ruta)) {
            @chmod($ruta, 0755);
        }

        if (!is_writable($ruta)) {
            throw new \RuntimeException('El directorio de publicidad no es escribible.');
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
        $rutaCompleta = ROOT_PATH . '/' . $rutaLimpia;

        if (is_file($rutaCompleta)) {
            @unlink($rutaCompleta);
        }
    }
}
