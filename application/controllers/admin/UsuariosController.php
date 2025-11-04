<?php

declare(strict_types=1);

final class UsuariosController extends AdminBaseController
{
    private AdminUsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new AdminUsuarioModel();
    }

    public function index(): void
    {
        $this->requireLogin();

        $usuarios = $this->usuarioModel->listar();

        $this->render('usuarios/index', [
            'title' => 'Usuarios administrativos',
            'usuarios' => $usuarios,
        ]);
    }

    public function crear(): void
    {
        $this->requireLogin();

        $this->render('usuarios/form', [
            'title' => 'Nuevo usuario',
            'usuario' => [
                'nombre' => '',
                'email' => '',
                'rol' => 'admin',
                'activo' => 1,
            ],
            'roles' => admin_available_roles(),
            'esEdicion' => false,
            'errores' => [],
        ]);
    }

    public function guardar(): void
    {
        $this->requireLogin();
        $this->asegurarPeticionPost();

        $datos = $this->obtenerDatosUsuarioDesdeRequest();
        $errores = $this->validarUsuario($datos, false);

        if ($errores !== []) {
            $this->render('usuarios/form', [
                'title' => 'Nuevo usuario',
                'usuario' => $datos,
                'roles' => admin_available_roles(),
                'esEdicion' => false,
                'errores' => $errores,
            ]);

            return;
        }

        $datos['pass_hash'] = password_hash($datos['password'], PASSWORD_DEFAULT);
        unset($datos['password'], $datos['password_confirm']);

        $this->usuarioModel->crear($datos);
        admin_set_flash('success', 'Usuario creado correctamente.');
        $this->redirect('admin/usuarios');
    }

    public function editar(string $id): void
    {
        $this->requireLogin();

        $usuarioId = sanitize_int($id);
        if ($usuarioId === null) {
            admin_set_flash('danger', 'Usuario inválido.');
            $this->redirect('admin/usuarios');

            return;
        }

        $usuario = $this->usuarioModel->obtenerPorId($usuarioId);
        if ($usuario === null) {
            admin_set_flash('warning', 'El usuario no existe.');
            $this->redirect('admin/usuarios');

            return;
        }

        $this->render('usuarios/form', [
            'title' => 'Editar usuario',
            'usuario' => $usuario,
            'roles' => admin_available_roles(),
            'esEdicion' => true,
            'errores' => [],
        ]);
    }

    public function actualizar(string $id): void
    {
        $this->requireLogin();
        $this->asegurarPeticionPost();

        $usuarioId = sanitize_int($id);
        if ($usuarioId === null) {
            admin_set_flash('danger', 'Usuario inválido.');
            $this->redirect('admin/usuarios');

            return;
        }

        $datos = $this->obtenerDatosUsuarioDesdeRequest();
        $datos['id'] = $usuarioId;
        $errores = $this->validarUsuario($datos, true);

        if ($errores !== []) {
            $usuario = $this->usuarioModel->obtenerPorId($usuarioId) ?? [];
            $usuario = array_merge($usuario, $datos);

            $this->render('usuarios/form', [
                'title' => 'Editar usuario',
                'usuario' => $usuario,
                'roles' => admin_available_roles(),
                'esEdicion' => true,
                'errores' => $errores,
            ]);

            return;
        }

        $payload = [
            'nombre' => $datos['nombre'],
            'email' => $datos['email'],
            'rol' => $datos['rol'],
            'activo' => $datos['activo'],
        ];

        if ($datos['password'] !== '') {
            $payload['pass_hash'] = password_hash($datos['password'], PASSWORD_DEFAULT);
        }

        $this->usuarioModel->actualizar($usuarioId, $payload);
        admin_set_flash('success', 'Usuario actualizado correctamente.');
        $this->redirect('admin/usuarios');
    }

    public function eliminar(string $id): void
    {
        $this->requireLogin();
        $this->asegurarPeticionPost();

        $usuarioId = sanitize_int($id);
        if ($usuarioId === null) {
            admin_set_flash('danger', 'Usuario inválido.');
            $this->redirect('admin/usuarios');

            return;
        }

        $usuarioActual = admin_current_user();
        if ($usuarioActual !== null && (int) $usuarioActual['id'] === $usuarioId) {
            admin_set_flash('danger', 'No puedes eliminar tu propia cuenta.');
            $this->redirect('admin/usuarios');

            return;
        }

        $this->usuarioModel->eliminar($usuarioId);
        admin_set_flash('success', 'Usuario eliminado correctamente.');
        $this->redirect('admin/usuarios');
    }

    private function obtenerDatosUsuarioDesdeRequest(): array
    {
        return [
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'email' => strtolower(trim((string) ($_POST['email'] ?? ''))),
            'rol' => $_POST['rol'] ?? 'admin',
            'activo' => isset($_POST['activo']) ? 1 : 0,
            'password' => (string) ($_POST['password'] ?? ''),
            'password_confirm' => (string) ($_POST['password_confirm'] ?? ''),
        ];
    }

    private function validarUsuario(array $datos, bool $esEdicion): array
    {
        $errores = [];

        if ($datos['nombre'] === '') {
            $errores['nombre'] = 'El nombre es obligatorio.';
        }

        if ($datos['email'] === '' || !is_valid_email($datos['email'])) {
            $errores['email'] = 'Ingrese un correo válido.';
        } else {
            $existente = $this->usuarioModel->buscarPorEmail($datos['email']);
            if ($existente !== null && (!$esEdicion || (int) $existente['id'] !== (int) ($datos['id'] ?? 0))) {
                $errores['email'] = 'El correo ya está registrado.';
            }
        }

        $rolesDisponibles = admin_available_roles();
        if (!array_key_exists($datos['rol'], $rolesDisponibles)) {
            $errores['rol'] = 'Seleccione un rol válido.';
        }

        if ($esEdicion) {
            if ($datos['password'] !== '' && $datos['password'] !== $datos['password_confirm']) {
                $errores['password'] = 'Las contraseñas no coinciden.';
            }
        } else {
            if ($datos['password'] === '') {
                $errores['password'] = 'La contraseña es obligatoria.';
            } elseif ($datos['password'] !== $datos['password_confirm']) {
                $errores['password'] = 'Las contraseñas no coinciden.';
            }
        }

        return $errores;
    }

    private function asegurarPeticionPost(): void
    {
        if (!$this->isPost()) {
            $this->redirect('admin/usuarios');
        }

        $token = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($token)) {
            $this->redirect('admin/usuarios');
        }
    }
}
