<?php

declare(strict_types=1);

header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: no-referrer-when-downgrade');

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

require ROOT_PATH . '/application/config/constants.php';
require ROOT_PATH . '/application/config/security.php';
require ROOT_PATH . '/database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$config = require CONFIG_PATH . '/config.php';
$GLOBALS['config'] = $config;

require APP_PATH . '/helpers/url_helper.php';
require APP_PATH . '/helpers/security_helper.php';
require APP_PATH . '/helpers/session_helper.php';
require APP_PATH . '/helpers/form_helper.php';
require APP_PATH . '/helpers/pagination_helper.php';
require APP_PATH . '/helpers/admin_auth_helper.php';

define('ADMIN_PATH', __DIR__);
define('ADMIN_VIEW_PATH', VIEW_PATH . 'admin/');

autoload_admin_classes();

$routes = require ADMIN_PATH . '/routes.php';

$requestedUri = resolve_admin_route();
[$routeInfo, $parameters] = match_admin_route($routes, $requestedUri);

if ($routeInfo === null) {
    http_response_code(404);
    echo 'Ruta de administración no encontrada.';
    exit;
}

$allowedMethods = $routeInfo['methods'] ?? ['GET'];
$requestMethod = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

if (!in_array($requestMethod, $allowedMethods, true)) {
    http_response_code(405);
    header('Allow: ' . implode(', ', $allowedMethods));
    echo 'Método no permitido.';
    exit;
}

if (($routeInfo['auth'] ?? true) && !admin_is_logged_in()) {
    admin_redirect_login();
}

$controllerName = $routeInfo['controller'];
$method = $routeInfo['method'];

if (!class_exists($controllerName)) {
    http_response_code(500);
    echo 'Controlador de administración no disponible.';
    exit;
}

$controller = new $controllerName();

if (!method_exists($controller, $method)) {
    http_response_code(500);
    echo 'Acción no disponible.';
    exit;
}

call_user_func_array([$controller, $method], $parameters);

function autoload_admin_classes(): void
{
    spl_autoload_register(static function (string $class): void {
        $paths = [
            APP_PATH . '/controllers/admin/' . $class . '.php',
            APP_PATH . '/models/admin/' . $class . '.php',
            APP_PATH . '/controllers/' . $class . '.php',
            APP_PATH . '/models/' . $class . '.php',
        ];

        foreach ($paths as $path) {
            if (is_file($path)) {
                require_once $path;
                return;
            }
        }
    });
}

function resolve_admin_route(): string
{
    $requested = $_GET['route'] ?? '';
    if ($requested !== '') {
        return trim((string) $requested, '/');
    }

    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
    $path = (string) parse_url($uri, PHP_URL_PATH);

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $scriptDir = str_replace('\\', '/', dirname($scriptName));
    $scriptDir = ($scriptDir === '/' || $scriptDir === '.') ? '' : trim($scriptDir, '/');

    if ($scriptDir !== '' && strpos($path, '/' . $scriptDir) === 0) {
        $path = substr($path, strlen($scriptDir) + 1);
    }

    $path = trim($path, '/');

    if (strpos($path, 'index.php/') === 0) {
        $path = substr($path, strlen('index.php/'));
    }

    return trim($path, '/');
}

function match_admin_route(array $routes, string $requestedUri): array
{
    $sanitizedUri = rawurldecode($requestedUri);
    $sanitizedUri = preg_replace('#/{2,}#', '/', $sanitizedUri ?? '') ?? '';
    $sanitizedUri = trim($sanitizedUri, '/');

    $matchedRoute = null;
    $parameters = [];

    foreach ($routes as $pattern => $route) {
        $regex = preg_quote($pattern, '#');
        $regex = preg_replace_callback('#\\\\\{([a-zA-Z_][a-zA-Z0-9_-]*)\\\\\}#', static function ($matches): string {
            return '([a-zA-Z0-9_-]+)';
        }, $regex);

        if ($regex === null) {
            continue;
        }

        $regex = '#^' . $regex . '$#u';

        if (preg_match($regex, $sanitizedUri, $matches) === 1) {
            array_shift($matches);
            $parameters = array_map('sanitize_uri_segment', $matches);
            $matchedRoute = $route;
            break;
        }
    }

    if ($matchedRoute === null) {
        $segments = array_values(array_filter(explode('/', $sanitizedUri), static function ($segment): bool {
            return $segment !== '';
        }));

        if ($segments === []) {
            return [$routes[''] ?? null, []];
        }

        $controllerSegment = array_shift($segments);
        $controllerName = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $controllerSegment))) . 'Controller';
        $method = $segments !== [] ? str_replace('-', '_', array_shift($segments)) : 'index';

        $matchedRoute = [
            'controller' => $controllerName,
            'method' => $method,
            'auth' => true,
            'methods' => ['GET'],
        ];

        $parameters = array_map('sanitize_uri_segment', $segments);
    }

    return [$matchedRoute, $parameters];
}
