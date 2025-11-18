<?php

declare(strict_types=1);

require_once APP_PATH . '/controllers/admin/AdminBaseController.php';
require_once APP_PATH . '/models/InformacionModel.php';

final class InformacionController extends AdminBaseController
{
    private const TIPOS = ['contacto', 'redes', 'header'];

    public function index(): void
    {
        $this->requireLogin();

        $informacion = $this->cargarInformacion();

        $this->render('informacion/index', [
            'title'       => 'Información',
            'informacion' => $informacion,
        ]);
    }

    public function editar(string $tipo): void
    {
        $this->requireLogin();

        $tipo = sanitize_uri_segment($tipo);

        if (!in_array($tipo, self::TIPOS, true)) {
            admin_set_flash('danger', 'Sección no encontrada.');
            $this->redirect('admin/informacion');
            return;
        }

        $informacion = $this->cargarInformacion();
        $errores = [];

        if ($this->isPost()) {
            $token = $_POST['csrf_token'] ?? '';
            if (!$this->validateCsrfToken($token)) {
                $this->redirect('admin/informacion/editar/' . $tipo);
                return;
            }

            [$payload, $errores] = $this->procesarFormulario($tipo, $informacion[$tipo] ?? []);

            if ($errores === []) {
                $actualizado = InformacionModel::actualizarPorTipo($tipo, $payload);

                if ($actualizado) {
                    admin_set_flash('success', 'Información actualizada correctamente.');
                } else {
                    admin_set_flash('danger', 'No se pudo guardar la información. Intente nuevamente.');
                }

                $this->redirect('admin/informacion/editar/' . $tipo);
                return;
            }

            $informacion[$tipo] = array_merge($informacion[$tipo] ?? [], $payload);
        }

        $this->render('informacion/editar', [
            'title'       => 'Información',
            'informacion' => $informacion,
            'activeTab'   => $tipo,
            'errores'     => $errores,
        ]);
    }

    private function cargarInformacion(): array
    {
        $datos = [];
        foreach (self::TIPOS as $tipo) {
            $datos[$tipo] = $this->obtenerInformacionPorTipo($tipo);
        }

        return $datos;
    }

    private function obtenerInformacionPorTipo(string $tipo): array
    {
        InformacionModel::crearSiNoExiste($tipo);

        $registro = InformacionModel::obtenerPorTipo($tipo) ?? [];
        $defaults = InformacionModel::obtenerCamposPorTipo($tipo);

        if ($tipo === 'contacto') {
            $defaults = array_merge([
                'telefono1' => '',
                'telefono2' => '',
                'email'     => '',
            ], $defaults);
        }

        if ($tipo === 'redes') {
            $defaults = array_merge([
                'facebook'  => '',
                'instagram' => '',
                'youtube'   => '',
                'tiktok'    => '',
            ], $defaults);
        }

        if ($tipo === 'header') {
            $defaults = array_merge(['mensaje_header' => ''], $defaults);
        }

        return array_merge($defaults, $registro);
    }

    private function procesarFormulario(string $tipo, array $actual): array
    {
        $errores = [];
        $payload = [];

        if ($tipo === 'contacto') {
            $payload['telefono1'] = sanitize_string($_POST['telefono1'] ?? '');
            $payload['telefono2'] = sanitize_string($_POST['telefono2'] ?? '');
            $payload['email'] = sanitize_string($_POST['email'] ?? '');

            if ($payload['email'] !== '' && !is_valid_email($payload['email'])) {
                $errores['email'] = 'Ingrese un correo electrónico válido.';
            }
        } elseif ($tipo === 'redes') {
            $urls = [
                'facebook'  => $_POST['facebook'] ?? '',
                'instagram' => $_POST['instagram'] ?? '',
                'youtube'   => $_POST['youtube'] ?? '',
                'tiktok'    => $_POST['tiktok'] ?? '',
            ];

            foreach ($urls as $campo => $valor) {
                $valorLimpio = sanitize_string($valor);
                if ($valorLimpio !== '' && !is_valid_url($valorLimpio)) {
                    $errores[$campo] = 'Ingrese una URL válida (incluya http o https).';
                }
                $payload[$campo] = $valorLimpio;
            }
        } elseif ($tipo === 'header') {
            $payload['mensaje_header'] = sanitize_string($_POST['mensaje_header'] ?? '');
        }

        return [$payload, $errores];
    }
}
