<?php

declare(strict_types=1);

define('ROOT_PATH', __DIR__);
require ROOT_PATH . '/application/config/constants.php';

$config = require CONFIG_PATH . '/config.php';
$routes = require CONFIG_PATH . '/routes.php';
$GLOBALS['config'] = $config;

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

$routeFromRequest = static function (): string {
    $requested = $_GET['route'] ?? '';

    if ($requested !== '') {
        return trim((string) $requested, '/');
    }

    $uri = '';

    if (!empty($_SERVER['REQUEST_URI'])) {
        $uri = (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $scriptDir = str_replace('\\', '/', dirname($scriptName));
    $scriptDir = ($scriptDir === '/' || $scriptDir === '.') ? '' : trim($scriptDir, '/');

    if ($scriptDir !== '' && strpos($uri, '/' . $scriptDir) === 0) {
        $uri = substr($uri, strlen($scriptDir) + 1);
    }

    $uri = trim($uri, '/');

    if (strpos($uri, 'index.php/') === 0) {
        $uri = substr($uri, strlen('index.php/'));
    }

    if ($uri === 'index.php') {
        $uri = '';
    }

    return $uri;
};

$requestedUri = $routeFromRequest();
$requestedUri = trim(preg_replace('#\\.php$#', '', $requestedUri) ?? '', '/');
$requestedUri = rawurldecode($requestedUri);
$requestedUri = preg_replace('#/{2,}#', '/', $requestedUri ?? '') ?? '';

$controllerName = $config['default_controller'] ?? 'HomeController';
$method = $config['default_method'] ?? 'index';
$parameters = [];

$matchRoute = static function (string $pattern, string $uri): ?array {
    $quoted = preg_quote($pattern, '#');
    $replacements = [
        '\\(:any\\)' => '([^/]+)',
        '\\(:num\\)' => '(\\d+)',
        '\\(:segment\\)' => '([a-zA-Z0-9\-_]+)',
    ];
    $regex = str_replace(array_keys($replacements), array_values($replacements), $quoted);

    $regex = preg_replace_callback('#\\\{([a-zA-Z_][a-zA-Z0-9_\-]*)\\\}#', static function (): string {
        return '([^/]+)';
    }, $regex);

    if ($regex === null) {
        return null;
    }

    $regex = '#^' . $regex . '$#u';

    if (preg_match($regex, $uri, $matches) === 1) {
        array_shift($matches);

        return $matches;
    }

    return null;
};

$matched = false;

if ($requestedUri !== '') {
    foreach ($routes as $pattern => $handler) {
        if (!is_array($handler) || count($handler) < 2) {
            continue;
        }

        $routeParams = $matchRoute($pattern, $requestedUri);

        if ($routeParams === null) {
            continue;
        }

        [$controllerName, $method] = $handler;
        $parameters = array_map('sanitize_uri_segment', $routeParams);
        $matched = true;
        break;
    }
}

if (!$matched && $requestedUri !== '') {
    $segments = array_values(array_filter(explode('/', $requestedUri), static function ($segment): bool {
        return $segment !== '';
    }));

    $segments = array_map('sanitize_uri_segment', $segments);

    if ($segments !== []) {
        $controllerSegment = array_shift($segments);
        $studly = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $controllerSegment)));
        $controllerName = $studly . 'Controller';

        if ($segments !== []) {
            $candidate = str_replace('-', '_', array_shift($segments));
            if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $candidate) === 1) {
                $method = $candidate;
            } else {
                array_unshift($segments, $candidate);
                $method = $config['default_method'] ?? 'index';
            }
        } else {
            $method = $config['default_method'] ?? 'index';
        }

        $parameters = array_map('sanitize_uri_segment', $segments);
    }
}

if (!class_exists($controllerName)) {
    http_response_code(404);
    echo 'Controlador no encontrado.';
    exit;
}

$controller = new $controllerName();

if (!method_exists($controller, $method) && !$matched && $parameters !== []) {
    foreach (['show', 'detalle', 'ver'] as $fallbackMethod) {
        if (method_exists($controller, $fallbackMethod)) {
            $method = $fallbackMethod;
            break;
        }
    }
}

if (!method_exists($controller, $method)) {
    http_response_code(404);
    echo 'Método no encontrado.';
    exit;
}

call_user_func_array([$controller, $method], $parameters);
