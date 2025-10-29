<?php

declare(strict_types=1);

define('ROOT_PATH', __DIR__);
require ROOT_PATH . '/application/config/constants.php';

$config = require CONFIG_PATH . '/config.php';
$routes = require CONFIG_PATH . '/routes.php';

require APP_PATH . '/helpers/url_helper.php';
require APP_PATH . '/helpers/security_helper.php';

spl_autoload_register(static function ($class): void {
    $paths = [
        APP_PATH . '/controllers/' . $class . '.php',
        APP_PATH . '/models/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

$requestedUri = $_GET['route'] ?? '';
if ($requestedUri === '') {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
    $scriptName = parse_url($_SERVER['SCRIPT_NAME'] ?? '', PHP_URL_PATH) ?: '';
    $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

    if ($basePath !== '' && strpos($uri, $basePath) === 0) {
        $uri = substr($uri, strlen($basePath));
    }

    $requestedUri = trim($uri, '/');
}

$requestedUri = trim($requestedUri, '/');
$requestedUri = preg_replace('/\\.php$/', '', $requestedUri ?? '') ?? '';

$controllerName = $config['default_controller'];
$method = $config['default_method'];

if ($requestedUri !== '' && isset($routes[$requestedUri])) {
    [$controllerName, $method] = $routes[$requestedUri];
} elseif ($requestedUri !== '') {
    $segments = explode('/', $requestedUri);
    $controllerSegment = array_shift($segments);
    $methodSegment = array_shift($segments) ?: $method;

    $controllerName = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $controllerSegment))) . 'Controller';
    $method = str_replace('-', '_', $methodSegment);
}

if (!class_exists($controllerName)) {
    http_response_code(404);
    echo 'Controlador no encontrado.';
    exit;
}

$controller = new $controllerName();

if (!method_exists($controller, $method)) {
    http_response_code(404);
    echo 'Método no encontrado.';
    exit;
}

$controller->$method();
