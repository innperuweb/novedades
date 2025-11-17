<?php

declare(strict_types=1);

require_once APP_PATH . '/controllers/admin/AdminBaseController.php';
require_once APP_PATH . '/models/InfoClienteModel.php';

final class InfoClienteController extends AdminBaseController
{
    private array $titulos = [
        'faq' => 'Preguntas frecuentes',
        'envios' => 'Envíos a nivel nacional',
        'por_mayor' => 'Pedidos por mayor',
        'garantias' => 'Garantías',
        'terminos' => 'Términos y condiciones',
        'privacidad' => 'Políticas de privacidad',
        'cambios' => 'Cambios y devoluciones',
    ];

    public function editar(string $slug): void
    {
        $this->requireLogin();

        $slug = sanitize_uri_segment($slug);

        if (!isset($this->titulos[$slug])) {
            admin_set_flash('danger', 'Sección inválida.');
            $this->redirect('admin/dashboard');

            return;
        }

        $titulo = $this->titulos[$slug];

        if ($this->isPost()) {
            $token = $_POST['csrf_token'] ?? '';
            if (!$this->validateCsrfToken($token)) {
                $this->redirect('admin/info-cliente/editar/' . $slug);

                return;
            }

            $contenido = $_POST['contenido'] ?? '';

            if (InfoClienteModel::actualizarContenido($slug, $contenido)) {
                admin_set_flash('success', 'Contenido guardado correctamente.');
            } else {
                admin_set_flash('danger', 'No se pudo guardar el contenido.');
            }

            $this->redirect('admin/info-cliente/editar/' . $slug);

            return;
        }

        $info = InfoClienteModel::obtenerPorSlug($slug);
        $contenido = $info['contenido'] ?? '';

        $this->render('info_cliente_editar', [
            'title' => $titulo,
            'titulo' => $titulo,
            'slug' => $slug,
            'contenido' => $contenido,
        ]);
    }
}
