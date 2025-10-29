<?php

declare(strict_types=1);

function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('clean_string')) {
    function clean_string(?string $value): string
    {
        $value = $value ?? '';
        $value = trim($value);
        $value = preg_replace('#[\x00-\x1F\x7F]+#u', '', $value);
        $value = preg_replace('#<\/?(script|style)[^>]*>#i', '', $value);

        return $value;
    }
}

if (!function_exists('sanitize_string')) {
    function sanitize_string(?string $value): string
    {
        $clean = clean_string($value);

        $clean = filter_var($clean, FILTER_UNSAFE_RAW, [
            'flags' => FILTER_FLAG_STRIP_LOW,
        ]);

        return $clean === false ? '' : (string) $clean;
    }
}

if (!function_exists('sanitize_int')) {
    function sanitize_int($value): ?int
    {
        return filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['default' => null],
        ]);
    }
}

if (!function_exists('sanitize_bool')) {
    function sanitize_bool($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;
    }
}

if (!function_exists('sanitize_array')) {
    function sanitize_array(array $values): array
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $values[$key] = sanitize_array($value);
                continue;
            }

            if (is_int($value) || is_float($value) || is_bool($value)) {
                $values[$key] = $value;
                continue;
            }

            $values[$key] = sanitize_string((string) $value);
        }

        return $values;
    }
}

if (!function_exists('is_valid_email')) {
    function is_valid_email(?string $email): bool
    {
        return $email !== null && filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('is_valid_url')) {
    function is_valid_url(?string $url): bool
    {
        return $url !== null && filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}

if (!function_exists('sanitize_uri_segment')) {
    function sanitize_uri_segment(?string $segment): string
    {
        $segment = rawurldecode($segment ?? '');
        $segment = preg_replace('/[^\pL\pN\-_.~]+/u', '', $segment);

        return $segment ?? '';
    }
}

if (!function_exists('escape_output')) {
    function escape_output($value): string
    {
        if (is_array($value) || is_object($value)) {
            $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $value = $encoded !== false ? $encoded : '';
        }

        return e((string) $value);
    }
}
