<?php

declare(strict_types=1);

final class AuthController extends AdminBaseController
{
    public function index(): void
    {
        if (admin_is_logged_in()) {
            $this->redirect('admin/dashboard');

            return;
        }

        $this->redirect('admin/login');
    }

    public function login(): void
    {
        if (admin_is_logged_in()) {
            $this->redirect('admin/dashboard');

            return;
        }

        $errors = [];
        $email = trim((string) ($_POST['email'] ?? ''));

        if ($this->isPost()) {
            $token = $_POST['csrf_token'] ?? '';

            if (!$this->validateCsrfToken($token)) {
                $this->redirect('admin/login');

                return;
            }

            $password = (string) ($_POST['password'] ?? '');

            if ($email === '' || !is_valid_email($email)) {
                $errors[] = 'Ingrese un correo electrónico válido.';
            }

            if ($password === '') {
                $errors[] = 'La contraseña es obligatoria.';
            }

            if ($errors === []) {
                $model = new AdminUsuarioModel();
                $usuario = $model->buscarPorEmail($email);

                if ($usuario === null || !(bool) ($usuario['activo'] ?? false)) {
                    $errors[] = 'Credenciales inválidas o usuario inactivo.';
                } elseif (!password_verify($password, (string) ($usuario['pass_hash'] ?? ''))) {
                    $errors[] = 'Credenciales inválidas.';
                } else {
                    admin_login_user($usuario);
                    $model->registrarAcceso((int) $usuario['id']);
                    admin_set_flash('success', 'Bienvenido de nuevo, ' . e($usuario['nombre'] ?? 'Administrador') . '.');
                    $this->redirect('admin/dashboard');

                    return;
                }
            }
        }

        $this->renderAuth('auth/login', [
            'title' => 'Acceso administradores',
            'errors' => $errors,
            'email' => $email,
        ]);
    }

    public function logout(): void
    {
        if ($this->isPost()) {
            $token = $_POST['csrf_token'] ?? '';
            if (!$this->validateCsrfToken($token)) {
                $this->redirect('admin/dashboard');

                return;
            }

            admin_logout();
            admin_set_flash('success', 'Sesión finalizada correctamente.');
            $this->redirect('admin/login');

            return;
        }

        if (admin_is_logged_in()) {
            $this->redirect('admin/dashboard');

            return;
        }

        $this->redirect('admin/login');
    }
}
