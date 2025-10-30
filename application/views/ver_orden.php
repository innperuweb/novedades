<?php
$orden = $orden ?? [];
$items = $items ?? [];
?>

<div class="breadcrumb-area bg--white-6 breadcrumb-bg-1 pt--60 pb--70 pt-lg--40 pb-lg--50 pt-md--30 pb-md--40">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="page-title">Mi cuenta</h1>
            </div>
        </div>
    </div>
</div>



<div id="content" class="main-content-wrapper">
    <div class="page-content-inner">
        <div class="container">

            <div class="row pt--80 pt-md--60 pt-sm--40 pb--80 pb-md--60 pb-sm--40">
                <div class="col-12">

                    <div class="row g-4 account">
                        <div class="col-md-2">
                            <nav class="nav nav-pills flex-md-column user-tabs" id="acct-tabs" role="tablist" aria-orientation="vertical">
                                <a class="nav-link active" id="dashboard-tab" href="mi_cuenta.php" role="tab" aria-controls="dashboard" aria-selected="true">Órdenes</a>
                                <a class="nav-link" id="datosp-tab" data-bs-toggle="pill" href="#datosp" role="tab" aria-controls="datosp" aria-selected="false">Datos personales</a>
                                <a class="nav-link" href="#">Cerrar sesión</a>
                            </nav>
                        </div>

                        <div class="col-md-10">
                            <div class="tab-content" id="acct-tabContent">
                                <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">

                                    <div class="row" style="text-align: center;">
                                        <div class="col-lg-6 mb-md--30">
                                            <div class="row mb--40 mb-md--30">
                                                <div class="col-12 estado_orden">
                                                    <h4 class="font-bold">Datos de la orden</h4>
                                                    <!-- Encabezado de la orden -->
                                                    <div class="orden-header">
                                                      <p><strong>N° de orden:</strong> <?= htmlspecialchars($orden['numero'] ?? '') ?></p>
                                                      <p><strong>Fecha:</strong> <?= htmlspecialchars($orden['fecha'] ?? '') ?></p>
                                                      <p><strong>Método de pago:</strong> <?= htmlspecialchars($orden['metodo_pago'] ?? '') ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row mb--40 mb-md--30">
                                                <div class="col-12 estado_orden">
                                                    <h4 class="font-bold">Estado de pago</h4>
                                                    <h2 class="heading-secondary text-uppercase font-bold">
                                                        Pendiente
                                                    </h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row centrar_orden">
                                        <div class="col-lg-4 col-md-4 mb-sm--30">
                                            <div class="about-text">
                                                <h3>Fecha </h3>
                                                <p class="ver_orden mb--25 mb-md--20"><?= htmlspecialchars($orden['fecha'] ?? '') ?></p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 mb-sm--30">
                                            <div class="about-text">
                                                <h3>Total</h3>
                                                <p class="ver_orden mb--25 mb-md--20">S/ <?= number_format((float) ($orden['totales']['total'] ?? 0), 2) ?></p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 mb-sm--30">
                                            <div class="about-text">
                                                <h3>Método de pago </h3>
                                                <p class="ver_orden mb--25 mb-md--20"><?= htmlspecialchars($orden['metodo_pago'] ?? '') ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <br>

                                    <div class="row centrar_orden">

                                        <div class="col-lg-6 col-md-6 mb-sm--30">
                                            <div class="about-text">
                                                <h3>Dirección de envío</h3>
                                                <?php if (!empty($orden['cliente']['distrito_nombre'])): ?>
                                                  <p class="ver_orden mb--25 mb-md--20"><strong>Distrito:</strong> <?= htmlspecialchars($orden['cliente']['distrito_nombre']) ?></p>
                                                <?php endif; ?>
                                                <?php if (!empty($orden['cliente']['direccion'])): ?>
                                                  <p class="ver_orden mb--25 mb-md--20"><strong>Dirección:</strong> <?= htmlspecialchars($orden['cliente']['direccion']) ?></p>
                                                <?php endif; ?>
                                                <?php if (!empty($orden['cliente']['referencia'])): ?>
                                                  <p class="ver_orden mb--25 mb-md--20"><strong>Referencia:</strong> <?= htmlspecialchars($orden['cliente']['referencia']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 mb-sm--30">
                                            <div class="about-text">
                                                <h3>Nº de whatsapp</h3>
                                                <p class="ver_orden mb--25 mb-md--20"><?= htmlspecialchars($orden['cliente']['telefono'] ?? '') ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <br>

                                    <div class="col-lg-12 mt-md--40">
                                        <div class="order-details">
                                            <div class="cliente">
                                              <p><strong>Cliente:</strong> <?= htmlspecialchars($orden['cliente']['nombre'] ?? '') ?> <?= htmlspecialchars($orden['cliente']['apellidos'] ?? '') ?></p>
                                              <p><strong>DNI:</strong> <?= htmlspecialchars($orden['cliente']['dni'] ?? '') ?></p>
                                              <p><strong>Teléfono:</strong> <?= htmlspecialchars($orden['cliente']['telefono'] ?? '') ?></p>
                                              <p><strong>Email:</strong> <?= htmlspecialchars($orden['cliente']['email'] ?? '') ?></p>
                                            </div>
                                            <div class="orden-info">
                                              <p><strong>Método de Envío:</strong> <?= htmlspecialchars($orden['metodo_envio'] ?? '') ?></p>
                                              <p><strong>Método de Pago:</strong> <?= htmlspecialchars($orden['metodo_pago'] ?? '') ?></p>
                                            </div>

                                            <div class="direccion-envio">
                                              <h4>Dirección de Entrega</h4>
                                              <?php if (!empty($orden['cliente']['distrito_nombre'])): ?>
                                                <p><strong>Distrito:</strong> <?= htmlspecialchars($orden['cliente']['distrito_nombre']) ?></p>
                                              <?php endif; ?>
                                              <?php if (!empty($orden['cliente']['direccion'])): ?>
                                                <p><strong>Dirección:</strong> <?= htmlspecialchars($orden['cliente']['direccion']) ?></p>
                                              <?php endif; ?>
                                              <?php if (!empty($orden['cliente']['referencia'])): ?>
                                                <p><strong>Referencia:</strong> <?= htmlspecialchars($orden['cliente']['referencia']) ?></p>
                                              <?php endif; ?>
                                            </div>
                                            <p><strong>Total:</strong> S/ <?= number_format((float) ($orden['totales']['total'] ?? 0), 2) ?></p>
                                            <div class="table-content table-responsive mb--30">
                                                <table class="table order-table order-table-2">
                                                    <thead>
                                                        <tr>
                                                            <th class="negrito_orden">Nombre del producto</th>
                                                            <th class="texto_centrado negrito_orden">Cantidad</th>
                                                            <th class="texto_centrado negrito_orden">Precio</th>
                                                            <th class="texto_centrado negrito_orden">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($items as $p): ?>
                                                          <tr class="orden-item">
                                                            <td class="nombre">
                                                              <?= htmlspecialchars($p['nombre'] ?? '') ?>
                                                              <?php if (!empty($p['color']) || !empty($p['talla'])): ?>
                                                                <br><small>
                                                                  <?php if (!empty($p['color'])): ?>Color: <?= htmlspecialchars($p['color']) ?><?php endif; ?>
                                                                  <?php if (!empty($p['color']) && !empty($p['talla'])): ?> — <?php endif; ?>
                                                                  <?php if (!empty($p['talla'])): ?>Talla: <?= htmlspecialchars($p['talla']) ?><?php endif; ?>
                                                                </small>
                                                              <?php endif; ?>
                                                            </td>
                                                            <td class="cantidad texto_centrado">x<?= (int) ($p['cantidad'] ?? 0) ?></td>
                                                            <td class="precio texto_centrado">S/ <?= number_format((float) ($p['precio'] ?? 0), 2) ?></td>
                                                            <td class="subtotal texto_centrado">S/ <?= number_format((float) ($p['subtotal'] ?? 0), 2) ?></td>
                                                          </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="orden-total">
                                                          <td colspan="3" class="text-end"><strong>Total</strong></td>
                                                          <td><strong>S/ <?= number_format((float) ($orden['totales']['total'] ?? 0), 2) ?></strong></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>

                                        </div>
                                    </div>







                                </div>
                                <div class="tab-pane fade" id="datosp" role="tabpanel" aria-labelledby="datosp-tab">
                                    <div class="comment-respond">
                                        <form action="#" class="form comment-form">
                                            <div class="form__group mb--30 mb-sm--20">
                                                <div class="row">
                                                    <div class="col-md-4 mb-sm--20">
                                                        <label class="form__label form__label--3" for="comment_email">Email<span
                                                                class="required">*</span></label>
                                                        <input type="email" name="comment_email" id="comment_email" placeholder="jconde@innperuweb.com" class="form__input">
                                                    </div>
                                                    <div class="col-md-4 mb-sm--20">
                                                        <label class="form__label form__label--3" for="comment_name">Nombre<span
                                                                class="required">*</span></label>
                                                        <input type="text" name="comment_name" id="comment_name" placeholder="Jared" class="form__input">
                                                    </div>
                                                    <div class="col-md-4 mb-sm--20">
                                                        <label class="form__label form__label--3" for="comment_name">Apellidos<span
                                                                class="required">*</span></label>
                                                        <input type="text" name="comment_name" id="comment_name" placeholder="Conde Tantalean" class="form__input">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form__label form__label--3" for="comment_website">DNI</label>
                                                        <input type="url" name="comment_website" id="comment_website" placeholder="54785698" class="form__input">
                                                    </div>
                                                    <div class="col-md-4 mb-sm--20">
                                                        <label class="form__label form__label--3" for="comment_name">N° de whatsapp<span
                                                                class="required">*</span></label>
                                                        <input type="text" name="comment_name" id="comment_name" placeholder="997199995" class="form__input">
                                                    </div>
                                                    <div class="col-md-4 mb-sm--20">
                                                        <label class="form__label form__label--3" for="comment_name">Dirección de envío<span
                                                                class="required">*</span></label>
                                                        <input type="text" name="comment_name" id="comment_name" placeholder="Av. Tomás Valle 1250 -  Los Olivos" class="form__input">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form__group">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <input type="submit" value="Guardar" class="btn btn-style-1 btn-submit">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

