<?php

declare(strict_types=1);

require_once APP_PATH . '/controllers/admin/AdminBaseController.php';
require_once APP_PATH . '/models/ExperienciaModel.php';

final class ExperienciasController extends AdminBaseController
{
    private const ALLOWED_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];
    private const MAX_FILE_SIZE = 2097152; // 2MB

    private ExperienciaModel $experienciaModel;
    private string $uploadDir;

    public function __construct()
    {
        $this->experienciaModel = new ExperienciaModel();
        $this->uploadDir = ROOT_PATH . '/public/assets/uploads/experiencias';
    }

    public function index(): void
    {
        $this->requireLogin();

        $experiencias = $this->experienciaModel->listar();

        $this->render('experiencias/index', [
            'title' => 'Experiencias',
            'experiencias' => $experiencias,
        ]);
    }

    public function crear(): void
    {
        $this->requireLogin();

        if ($this->isPost()) {
            $this->guardarNueva();

            return;
        }

        $this->render('experiencias/crear', [
            'title' => 'Crear experiencia',
            'experiencia' => [
                'nombre' => '',
                'texto' => '',
                'visible' => 1,
                'imagen' => '',
            ],
            'errores' => [],
        ]);
    }

    public function editar(string $id): void
    {
        $this->requireLogin();

        $experienciaId = sanitize_int($id);
        if ($experienciaId === null) {
            admin_set_flash('danger', 'Experiencia inválida.');
            $this->redirect('admin/experiencias');

            return;
        }

        $experiencia = $this->experienciaModel->obtenerPorId($experienciaId);
        if ($experiencia === null) {
            admin_set_flash('warning', 'Experiencia no encontrada.');
            $this->redirect('admin/experiencias');

            return;
        }

        if ($this->isPost()) {
            $this->actualizarExistente($experiencia);

            return;
        }

        $this->render('experiencias/editar', [
            'title' => 'Editar experiencia',
            'experiencia' => $experiencia,
            'errores' => [],
        ]);
    }

    public function eliminar(string $id): void
    {
        $this->requireLogin();

        if (!$this->isPost()) {
            $this->redirect('admin/experiencias');

            return;
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            $this->redirect('admin/experiencias');

            return;
        }

        $experienciaId = sanitize_int($id);
        if ($experienciaId === null) {
            admin_set_flash('danger', 'Experiencia inválida.');
            $this->redirect('admin/experiencias');

            return;
        }

        $experiencia = $this->experienciaModel->obtenerPorId($experienciaId);

        if ($experiencia !== null && $this->experienciaModel->eliminar($experienciaId)) {
            if (!empty($experiencia['imagen'])) {
                $this->eliminarImagen((string) $experiencia['imagen']);
            }

            admin_set_flash('success', 'Experiencia eliminada correctamente.');
        } else {
            admin_set_flash('danger', 'No se pudo eliminar la experiencia.');
        }

        $this->redirect('admin/experiencias');
    }

    private function guardarNueva(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            $this->redirect('admin/experiencias');

            return;
        }

        [$datos, $errores] = $this->obtenerDatosValidados(true);

        if ($errores !== []) {
            $this->render('experiencias/crear', [
                'title' => 'Crear experiencia',
                'experiencia' => $datos,
                'errores' => $errores,
            ]);

            return;
        }

        $datos['imagen'] = '';
        $experienciaId = $this->experienciaModel->crear($datos);

        if ($experienciaId <= 0) {
            admin_set_flash('danger', 'No se pudo crear la experiencia.');
            $this->redirect('admin/experiencias');

            return;
        }

        $archivoImagen = $_FILES['imagen'] ?? null;
        if ($this->hayNuevaImagen($archivoImagen)) {
            $resultado = $this->procesarImagen($archivoImagen);
            if (isset($resultado['error'])) {
                $this->experienciaModel->eliminar($experienciaId);
                admin_set_flash('danger', $resultado['error']);
                $this->render('experiencias/crear', [
                    'title' => 'Crear experiencia',
                    'experiencia' => $datos,
                    'errores' => [],
                ]);

                return;
            }

            $this->experienciaModel->actualizar($experienciaId, array_merge($datos, [
                'imagen' => $resultado['filename'] ?? '',
            ]));
        }

        admin_set_flash('success', 'Experiencia creada correctamente.');
        $this->redirect('admin/experiencias');
    }

    private function actualizarExistente(array $experienciaActual): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            $this->redirect('admin/experiencias');

            return;
        }

        [$datos, $errores] = $this->obtenerDatosValidados(false);
        $archivoImagen = $_FILES['imagen'] ?? null;

        if ($errores !== []) {
            $this->render('experiencias/editar', [
                'title' => 'Editar experiencia',
                'experiencia' => array_merge($experienciaActual, $datos),
                'errores' => $errores,
            ]);

            return;
        }

        $nombreImagen = (string) ($experienciaActual['imagen'] ?? '');

        if ($this->hayNuevaImagen($archivoImagen)) {
            $resultado = $this->procesarImagen($archivoImagen);

            if (isset($resultado['error'])) {
                $this->render('experiencias/editar', [
                    'title' => 'Editar experiencia',
                    'experiencia' => array_merge($experienciaActual, $datos),
                    'errores' => ['imagen' => $resultado['error']],
                ]);

                return;
            }

            if ($nombreImagen !== '') {
                $this->eliminarImagen($nombreImagen);
            }

            $nombreImagen = $resultado['filename'] ?? $nombreImagen;
        }

        $datos['imagen'] = $nombreImagen;
        $this->experienciaModel->actualizar((int) $experienciaActual['id'], $datos);

        admin_set_flash('success', 'Experiencia actualizada correctamente.');
        $this->redirect('admin/experiencias');
    }

    private function obtenerDatosValidados(bool $esCreacion): array
    {
        $datos = [
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'texto' => trim((string) ($_POST['texto'] ?? '')),
            'visible' => isset($_POST['visible']) ? 1 : 0,
            'orden' => 0,
        ];

        $errores = [];

        if ($datos['nombre'] === '') {
            $errores['nombre'] = 'El nombre es obligatorio.';
        }

        if ($datos['texto'] === '') {
            $errores['texto'] = 'El texto es obligatorio.';
        }

        $archivoImagen = $_FILES['imagen'] ?? null;
        if ($this->hayNuevaImagen($archivoImagen)) {
            $errorImagen = $this->validarImagen($archivoImagen);
            if ($errorImagen !== null) {
                $errores['imagen'] = $errorImagen;
            }
        } elseif ($esCreacion === true) {
            // Imagen opcional en creación según requisitos
        }

        return [$datos, $errores];
    }

    private function hayNuevaImagen(?array $archivo): bool
    {
        if ($archivo === null) {
            return false;
        }

        $error = (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE);

        return $error !== UPLOAD_ERR_NO_FILE;
    }

    private function validarImagen(array $archivo): ?string
    {
        $error = (int) ($archivo['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($error === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($error !== UPLOAD_ERR_OK || empty($archivo['tmp_name']) || !is_uploaded_file((string) $archivo['tmp_name'])) {
            return 'No se pudo subir la imagen. Intente nuevamente.';
        }

        if ((int) ($archivo['size'] ?? 0) > self::MAX_FILE_SIZE) {
            return 'La imagen debe pesar menos de 2MB.';
        }

        $info = @getimagesize((string) $archivo['tmp_name']);
        if ($info === false) {
            return 'El archivo debe ser una imagen válida.';
        }

        $mime = (string) ($info['mime'] ?? '');
        if ($mime === '' || !array_key_exists($mime, self::ALLOWED_MIMES)) {
            return 'Formato de imagen no permitido. Use JPG, JPEG, PNG o WEBP.';
        }

        return null;
    }

    private function procesarImagen(array $archivo): array
    {
        $error = $this->validarImagen($archivo);
        if ($error !== null) {
            return ['error' => $error];
        }

        $ext = self::ALLOWED_MIMES[(string) (@getimagesize((string) $archivo['tmp_name'])['mime'] ?? '')] ?? 'jpg';

        try {
            $directorio = $this->asegurarDirectorio();
        } catch (\RuntimeException $exception) {
            return ['error' => $exception->getMessage()];
        }

        $nombreArchivo = 'experiencia-' . uniqid('', true) . '.' . $ext;
        $destino = rtrim($directorio, '/') . '/' . $nombreArchivo;

        if (!move_uploaded_file((string) $archivo['tmp_name'], $destino)) {
            return ['error' => 'No se pudo guardar la imagen subida.'];
        }

        @chmod($destino, 0644);

        return ['filename' => $nombreArchivo];
    }

    private function asegurarDirectorio(): string
    {
        $ruta = rtrim($this->uploadDir, '/');

        if (!is_dir($ruta)) {
            if (!mkdir($ruta, 0755, true) && !is_dir($ruta)) {
                throw new \RuntimeException('No se pudo crear el directorio de experiencias.');
            }
            @chmod($ruta, 0755);
        }

        if (!is_writable($ruta)) {
            @chmod($ruta, 0755);
        }

        if (!is_writable($ruta)) {
            throw new \RuntimeException('El directorio de experiencias no es escribible.');
        }

        return $ruta;
    }

    private function eliminarImagen(string $nombreArchivo): void
    {
        $ruta = rtrim($this->uploadDir, '/') . '/' . ltrim($nombreArchivo, '/');

        if (is_file($ruta)) {
            @unlink($ruta);
        }
    }
}
