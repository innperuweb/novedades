<?php

// Archivo base para desarrollo futuro del módulo Admin Productos
// No modificar vistas ni lógica visual existente
// Conectado al router principal desde index.php

$action = isset($moduleAction) ? htmlspecialchars((string) $moduleAction, ENT_QUOTES, 'UTF-8') : 'productos';
?>
<main class="module-placeholder">
    <section class="container">
        <h1>Gestión de Productos</h1>
        <p>Vista base del panel de productos en modo: <strong><?php echo $action; ?></strong>.</p>
        <p>Aquí se administrarán altas, bajas y modificaciones del catálogo.</p>
    </section>
</main>
