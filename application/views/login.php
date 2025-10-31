<?php
$error = $error ?? null;
?>

<div class="breadcrumb-area bg--white-6 breadcrumb-bg-1 pt--60 pb--70 pt-lg--40 pb-lg--50 pt-md--30 pb-md--40">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="page-title">Iniciar sesión</h1>
            </div>
        </div>
    </div>
</div>

<div id="content" class="main-content-wrapper ptb--80">
    <div class="page-content-inner">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="login-register-wrapper">
                        <h2 class="mb--20">Bienvenido de nuevo</h2>
                        <?php if ($error !== null) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="<?= base_url('login'); ?>" class="lezada-form">
                            <div class="form-group mb--20">
                                <label for="email">Correo electrónico</label>
                                <input type="email" id="email" name="email" class="form__input form__input--2" placeholder="Correo electrónico" required>
                            </div>
                            <div class="form-group mb--20">
                                <label for="password">Contraseña</label>
                                <input type="password" id="password" name="password" class="form__input form__input--2" placeholder="Contraseña" required>
                            </div>
                            <div class="form-group mb--20">
                                <button type="submit" class="lezada-button lezada-button--medium">Ingresar</button>
                            </div>
                        </form>
                        <p class="mt--20">¿Aún no tienes una cuenta? <a href="<?= base_url('registro'); ?>">Crear cuenta nueva</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
