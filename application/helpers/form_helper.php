<?php

// Archivo base para desarrollo futuro del módulo Form Helper
// No modificar vistas ni lógica visual existente
// Conectado al router principal desde index.php

function set_value($key)
{
    return isset($_POST[$key]) ? htmlspecialchars((string) $_POST[$key], ENT_QUOTES, 'UTF-8') : '';
}

function form_error($key, $errors)
{
    if (!isset($errors[$key])) {
        return '';
    }

    $message = htmlspecialchars((string) $errors[$key], ENT_QUOTES, 'UTF-8');

    return '<span class="form-error">' . $message . '</span>';
}

if (!function_exists('old')) {
    function old(string $key, $default = '')
    {
        if (!isset($_POST[$key])) {
            return $default;
        }

        $valor = $_POST[$key];

        if (is_array($valor)) {
            return $valor;
        }

        return (string) $valor;
    }
}
