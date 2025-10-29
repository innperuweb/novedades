<?php

function set_session($key, $value) {
    $_SESSION[$key] = $value;
}

function get_session($key) {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
}

function remove_session($key) {
    if (isset($_SESSION[$key])) unset($_SESSION[$key]);
}

function destroy_session() {
    session_unset();
    session_destroy();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: " . base_url('mi-cuenta'));
        exit();
    }
}

function get_cart_session(): array {
    return isset($_SESSION['carrito']) && is_array($_SESSION['carrito'])
        ? $_SESSION['carrito']
        : [];
}

function set_cart_session(array $items): void {
    $_SESSION['carrito'] = $items;
}

function clear_cart_session(): void {
    if (isset($_SESSION['carrito'])) {
        unset($_SESSION['carrito']);
    }
}
