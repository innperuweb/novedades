<?php

declare(strict_types=1);

require_once APP_PATH . '/controllers/admin/AdminBaseController.php';
require_once APP_PATH . '/models/PublicidadModel.php';

final class PublicidadController extends AdminBaseController
{
    private const IMAGEN_MIMES = [
        'image/jpeg' => 'jpg',
        'image/jpg'  => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    private const POSICIONES = [1, 2, 3, 4];

    private PublicidadModel $publicidadModel;
    private string $directorioPublicidad;

    public function __construct()
    {
        $this->publicidadModel      = new PublicidadModel();
        $this->directorioPublicidad = ROOT_PATH . '/public/assets/uploads/publicidad';
    }

    public function index(): void
    {
        $this->requireLogin();

        $publicidades = $this->publicidadModel->obtenerTodas();

        $this->render('publicidad_form', [
            'title'        => 'Publicidad',
            'publicidades' => $publicidades,
            'errores'      => [],
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

        $publicidadesActuales  = $this->publicidadModel->obtenerTodas();
        $archivosImagen        = $_FILES['imagen'] ?? [];
        $errores               = [];
        $publicidadesProcesadas = [];

        foreach (self::POSICIONES as $posicion) {
            $datosBanner = [
                'titulo'    => trim($this->obtenerValorArreglo($_POST['titulo'] ?? [], $posicion)),
                'subtitulo' => trim($this->obtenerValorArreglo($_POST['subtitulo'] ?? [], $posicion)),
                'texto'     => trim($this->obtenerValorArreglo($_POST['texto'] ?? [], $posicion)),
                'imagen'    => (string) ($publicidadesActuales[$posicion]['imagen'] ?? ''),
            ];

            $archivoImagen = $this->extraerArchivoPorPosicion($archivosImagen, $posicion);
            $erroresBanner = $this->validarDatosBanner($datosBanner, $archivoImagen);

            if ($erroresBanner !== []) {
                $errores[$posicion] = $erroresBanner;
            }

            $publicidadesProcesadas[$posicion] = $datosBanner;

            if ($erroresBanner !== [] || !$this->hayNuevaImagen($archivoImagen)) {
                continue;
            }

            $resultadoImagen = $this->manejarImagen(
                $archivoImagen ?? [],
                $publicidadesActuales[$posicion]['imagen'] ?? null,
                $posicion
            );

            if (isset($resultadoImagen['error'])) {
                $errores[$posicion]['imagen'] = $resultadoImagen['error'];
                continue;
            }

            $publicidadesProcesadas[$posicion]['imagen'] = $resultadoImagen['ruta'] ?? $datosBanner['imagen'];
        }

        if ($errores !== []) {
            $this->render('publicidad_form', [
                'title'        => 'Publicidad',
                'publicidades' => $publicidadesProcesadas,
                'errores'      => $errores,
            ]);
            return;
        }

        foreach (self::POSICIONES as $posicion) {
            $this->publicidadModel->actualizarPorPosicion($posicion, $publicidadesProcesadas[$posicion]);
        }

        admin_set_flash('success', 'Publicidad actualizada correctamente.');
        $this->redirect('admin/publicidad');
    }

    /* =============================
        MÉTODOS PRIVADOS NECESARIOS
       ============================= */

    private function obtenerValorArreglo(array $valores, int $posicion): string
    {
        return (string) ($valores[$posicion] ?? '');
    }

    private function extraerArchivoPorPosicion(array $archivos, int $posicion): ?array
    {
        if (!isset($archivos['name'][$posicion])) {
            return null;
        }

        return [
            'name'     => $archivos['name'][$posicion] ?? '',
            'type'     => $archivos['type'][$posicion] ?? '',
            'tmp_name' => $archivos['tmp_name'][$posicion] ?? '',
            'error'    => $archivos['error'][$posicion] ?? UPLOAD_ERR_NO_FILE,
            'size'     => $archivos['size'][$posicion] ?? 0,
        ];
    }

    private function validarDatosBanner(array $datos, ?array $archivoImagen): array
    {
        $errores = [];

        if ($datos['titulo'] === '')    $errores['titulo']    = 'El título es obligatorio.';
        if ($datos['subtitulo'] === '') $errores['subtitulo'] = 'El subtítulo es obligatorio.';
        if ($datos['texto'] === '')     $errores['texto']     = 'El texto es obligatorio.';

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
        return is_array($archivo)
            && isset($archivo['tmp_name'])
            && is_uploaded_file((string) $archivo['tmp_name']);
    }

    private function validarImagen(array $archivo): ?string
    {
        $error = $archivo['error'] ?? UPLOAD_ERR_NO_FILE;

        if ($error === UPLOAD_ERR_NO_FILE) return null;
        if ($error !== UPLOAD_ERR_OK) return 'No se pudo subir la imagen. Inténtelo nuevamente.';

        $info = @getimagesize((string)$archivo['tmp_name']);
        if ($info === false) return 'El archivo debe ser una imagen válida.';

        $mime = (string)($info['mime'] ?? '');
        if (!array_key_exists($mime, self::IMAGEN_MIMES)) {
            return 'Formato de imagen no permitido. Use JPG, JPEG, PNG o WEBP.';
        }

        return null;
    }

    private function manejarImagen(array $archivoImagen, ?string $imagenActual, int $posicion): array
    {
        $errorImagen = $this->validarImagen($archivoImagen);
        if ($errorImagen !== null) return ['error' => $errorImagen];

        try {
            $directorio = $this->asegurarDirectorioPublicidad();
        } catch (\RuntimeException $e) {
            return ['error' => $e->getMessage()];
        }

        $timestamp      = time();
        $nombreArchivo  = "publicidad-{$posicion}-{$timestamp}.webp";
        $destino        = $directorio . '/' . $nombreArchivo;

        if (!$this->convertirAWebp($archivoImagen['tmp_name'], $archivoImagen['type'], $destino)) {
            return ['error' => 'No se pudo procesar la imagen.'];
        }

        @chmod($destino, 0644);

        if ($imagenActual) $this->eliminarImagenFisica($imagenActual);

        return ['ruta' => 'public/assets/uploads/publicidad/' . $nombreArchivo];
    }

    private function asegurarDirectorioPublicidad(): string
    {
        $ruta = rtrim($this->directorioPublicidad, '/');

        if (!is_dir($ruta) && !mkdir($ruta, 0755, true)) {
            throw new \RuntimeException('No se pudo crear el directorio para la publicidad.');
        }

        if (!is_writable($ruta) && !chmod($ruta, 0755)) {
            throw new \RuntimeException('El directorio de publicidad no es escribible.');
        }

        return $ruta;
    }

    private function convertirAWebp(string $origen, string $mime, string $destino): bool
    {
        $mime = strtolower(trim($mime));

        if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
            $image = @imagecreatefromjpeg($origen);
        } elseif ($mime === 'image/png') {
            $image = @imagecreatefrompng($origen);
            if ($image !== false && function_exists('imagepalettetotruecolor')) {
                @imagepalettetotruecolor($image);
                @imagealphablending($image, true);
                @imagesavealpha($image, true);
            }
        } elseif ($mime === 'image/webp') {
            $image = @imagecreatefromwebp($origen);
        } else {
            return false;
        }

        if ($image === false || $image === null) return false;

        $result = @imagewebp($image, $destino, 90);
        @imagedestroy($image);

        return $result === true;
    }

    private function eliminarImagenFisica(string $rutaRelativa): void
    {
        $ruta = ROOT_PATH . '/' . ltrim($rutaRelativa, '/');

        if (is_file($ruta)) {
            @unlink($ruta);
        }
    }
}
