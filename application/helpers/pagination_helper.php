<?php

// Archivo base para desarrollo futuro del módulo Pagination Helper
// No modificar vistas ni lógica visual existente
// Conectado al router principal desde index.php

function paginate($total, $perPage, $currentPage, $url)
{
    $totalPages = (int) ceil($total / max($perPage, 1));

    if ($totalPages <= 1) {
        return '';
    }

    $urlEscaped = htmlspecialchars((string) $url, ENT_QUOTES, 'UTF-8');
    $output = '<ul class="pagination">';

    for ($i = 1; $i <= $totalPages; $i++) {
        $active = ($i === (int) $currentPage) ? ' class="active"' : '';
        $output .= '<li' . $active . '><a href="' . $urlEscaped . '?page=' . $i . '">' . $i . '</a></li>';
    }

    $output .= '</ul>';

    return $output;
}
