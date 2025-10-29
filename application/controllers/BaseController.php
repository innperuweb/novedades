<?php

abstract class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        require VIEW_PATH . 'partials/head.php';
        require VIEW_PATH . 'partials/header.php';
        require VIEW_PATH . $view . '.php';
        require VIEW_PATH . 'partials/footer.php';
        require VIEW_PATH . 'partials/scripts.php';
    }
}
