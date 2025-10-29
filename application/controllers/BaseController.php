<?php

abstract class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        $viewFile = VIEW_PATH . $view . '.php';

        if (!is_file($viewFile)) {
            http_response_code(404);
            $fallbackView = VIEW_PATH . 'errors/404.php';

            if (!is_file($fallbackView)) {
                echo '404 - Vista "' . htmlspecialchars($view, ENT_QUOTES, 'UTF-8') . '" no encontrada.';
                return;
            }

            $viewFile = $fallbackView;
            $data['missingView'] = $view;
        }

        extract($data);
        require VIEW_PATH . 'partials/head.php';
        require VIEW_PATH . 'partials/header.php';
        require $viewFile;
        require VIEW_PATH . 'partials/footer.php';
        require VIEW_PATH . 'partials/scripts.php';
    }
}
