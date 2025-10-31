<?php
$error = $error ?? null;
?>

<div class="breadcrumb-area bg--white-6 breadcrumb-bg-1 pt--60 pb--70 pt-lg--40 pb-lg--50 pt-md--30 pb-md--40">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="page-title">Registro de cliente</h1>
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
                        <h2 class="mb--20">Crea tu cuenta</h2>
                        <?php if ($error !== null) : ?>
                            <div class="alert alert-danger" role="alert">
                                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="<?= base_url('registro'); ?>" class="lezada-form">
                            <div class="form-group mb--20">
                                <label for="nombre">Nombre</label>
                                <input type="text" id="nombre" name="nombre" class="form__input form__input--2" placeholder="Nombre" required>
                            </div>
                            <div class="form-group mb--20">
                                <label for="apellidos">Apellidos</label>
                                <input type="text" id="apellidos" name="apellidos" class="form__input form__input--2" placeholder="Apellidos" required>
                            </div>
                            <div class="form-group mb--20">
                                <label for="email">Correo electrónico</label>
                                <input type="email" id="email" name="email" class="form__input form__input--2" placeholder="Correo electrónico" required>
                            </div>
                            <div class="form-group mb--20">
                                <label for="password">Contraseña</label>
                                <input type="password" id="password" name="password" class="form__input form__input--2" placeholder="Contraseña" required>
                            </div>
                            <div class="form-group mb--20">
                                <label for="telefono">Teléfono</label>
                                <input type="text" id="telefono" name="telefono" class="form__input form__input--2" placeholder="Teléfono">
                            </div>
                            <div class="form-group mb--20">
                                <label for="direccion">Dirección</label>
                                <input type="text" id="direccion" name="direccion" class="form__input form__input--2" placeholder="Dirección">
                            </div>
                            <div class="form-group mb--20">
                                <label for="distrito">Distrito</label>
                                <select id="distrito" name="distrito" class="form__input form__input--2 nice-select">
                                    <option value="">Seleccionar distrito</option>
                                    <option value="AN">Ancón</option>
                                    <option value="AT">Ate</option>
                                    <option value="BR">Barranco</option>
                                    <option value="BE">Breña</option>
                                    <option value="CA">Carabayllo</option>
                                    <option value="CC">Chaclacayo</option>
                                    <option value="CH">Chorrillos</option>
                                    <option value="CI">Cieneguilla</option>
                                    <option value="CM">Comas</option>
                                    <option value="EA">El Agustino</option>
                                    <option value="IN">Independencia</option>
                                    <option value="JM">Jesús María</option>
                                    <option value="LM">La Molina</option>
                                    <option value="LV">La Victoria</option>
                                    <option value="LI">Lince</option>
                                    <option value="LO">Los Olivos</option>
                                    <option value="LU">Lurigancho</option>
                                    <option value="LR">Lurín</option>
                                    <option value="MM">Magdalena del Mar</option>
                                    <option value="MO">Miraflores</option>
                                    <option value="PC">Pachacamac</option>
                                    <option value="PU">Pucusana</option>
                                    <option value="PL">Pueblo Libre</option>
                                    <option value="PP">Puente Piedra</option>
                                    <option value="PH">Punta Hermosa</option>
                                    <option value="PN">Punta Negra</option>
                                    <option value="RI">Rímac</option>
                                    <option value="SB">San Bartolo</option>
                                    <option value="SBJ">San Borja</option>
                                    <option value="SI">San Isidro</option>
                                    <option value="SL">San Juan de Lurigancho</option>
                                    <option value="SM">San Juan de Miraflores</option>
                                    <option value="SC">San Luis</option>
                                    <option value="SP">San Martín de Porres</option>
                                    <option value="SG">San Miguel</option>
                                    <option value="SA">Santa Anita</option>
                                    <option value="SMR">Santa María del Mar</option>
                                    <option value="SR">Santa Rosa</option>
                                    <option value="SS">Santiago de Surco</option>
                                    <option value="SU">Surquillo</option>
                                    <option value="VS">Villa El Salvador</option>
                                    <option value="VT">Villa María del Triunfo</option>
                                </select>
                            </div>
                            <div class="form-group mb--20">
                                <label for="referencia">Referencia</label>
                                <input type="text" id="referencia" name="referencia" class="form__input form__input--2" placeholder="Referencia">
                            </div>
                            <div class="form-group mb--20">
                                <button type="submit" class="lezada-button lezada-button--medium">Registrar</button>
                            </div>
                        </form>
                        <p class="mt--20">¿Ya tienes una cuenta? <a href="<?= base_url('login'); ?>">Inicia sesión</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
