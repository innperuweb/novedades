<?php

// Archivo base para desarrollo futuro del módulo Search
// No modificar vistas ni lógica visual existente
// Conectado al router principal desde index.php

$action = isset($moduleAction) ? htmlspecialchars((string) $moduleAction, ENT_QUOTES, 'UTF-8') : 'index';
?>
<main class="module-placeholder">
    <section class="container">
        <h1>Buscador</h1>
        <p>Vista base del buscador en modo: <strong><?php echo $action; ?></strong>.</p>
        <p>Este espacio está listo para integrar la lógica del módulo de búsqueda.</p>
    </section>
</main>
