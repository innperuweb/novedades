<section class="error-404">
    <div class="container text-center py-5">
        <h1 class="display-4">404</h1>
        <p class="lead">Lo sentimos, la vista solicitada no pudo ser encontrada.</p>
        <?php if (!empty($missingView)) : ?>
            <p class="text-muted">Vista: <strong><?php echo htmlspecialchars($missingView, ENT_QUOTES, 'UTF-8'); ?></strong></p>
        <?php endif; ?>
        <a class="btn btn-primary mt-3" href="/">Volver al inicio</a>
    </div>
</section>
