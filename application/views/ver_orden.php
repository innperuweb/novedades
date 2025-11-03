<?php
require_once APP_PATH . '/helpers/security_helper.php';
require_once APP_PATH . '/models/OrdenModel.php';

$nro = $_GET['nro'] ?? '';
$nro = is_string($nro) ? $nro : '';
$orden = $orden ?? ($nro !== '' ? OrdenModel::obtenerOrdenCompleta((string) $nro) : null);
$detalle = is_array($orden['detalle'] ?? null) ? $orden['detalle'] : [];

if ($orden === null): ?>
    <div class="breadcrumb-area bg--white-6 breadcrumb-bg-1 pt--60 pb--70 pt-lg--40 pb-lg--50 pt-md--30 pb-md--40">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 text-center">
                    <h1 class="page-title">Mi cuenta</h1>
                </div>
            </div>
        </div>
    </div>

    <div id="content" class="main-content-wrapper ptb--80">
        <div class="page-content-inner">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8 text-center">
                        <p>Orden no encontrada.</p>
                        <p><a href="<?= e(base_url('mi_cuenta')) ?>">Volver a mi cuenta</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php return; endif;

$fechaFormateada = date('d/m/Y', strtotime($orden['fecha'] ?? 'now'));
$metodoEnvio = $orden['metodo_envio_texto'] ?? $orden['metodo_envio'] ?? '';
$referencia = $orden['referencia'] ?? '';
$costoEnvio = number_format((float) ($orden['costo_envio'] ?? 0), 2);
$totalOrden = number_format((float) ($orden['total'] ?? 0), 2);
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
                                <a class="nav-link active" id="dashboard-tab" href="<?= e(base_url('mi_cuenta')) ?>" role="tab" aria-controls="dashboard" aria-selected="true">Órdenes</a>
                                <a class="nav-link" id="datosp-tab" data-bs-toggle="pill" href="#datosp" role="tab" aria-controls="datosp" aria-selected="false">Datos personales</a>
                                <a class="nav-link" href="<?= e(base_url('cliente/logout')) ?>">Cerrar sesión</a>
                            </nav>
                        </div>

                        <div class="col-md-10">
                            <div class="tab-content" id="acct-tabContent">
                                <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">

                                    <div class="row" style="text-align: center;">
                                        <div class="col-lg-6 mb-md--30">
                                            <div class="row mb--40 mb-md--30">
                                                <div class="col-12 estado_orden">
                                                    <h4 class="font-bold">Nº de orden</h4>
                                                    <h2 class="heading-secondary text-uppercase font-bold">
                                                        <?= e($orden['nro_orden']) ?>
                                                    </h2>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="row mb--40 mb-md--30">
                                                <div class="col-12 estado_orden">
                                                    <h4 class="font-bold">Estado</h4>
                                                    <h2 class="heading-secondary text-uppercase font-bold">
                                                        <?= e($orden['estado'] ?? 'Pendiente') ?>
                                                    </h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row centrar_orden">
                                        <div class="col-lg-4 col-md-4 mb-sm--30">
                                            <div class="about-text">
                                                <h3>Fecha</h3>
                                                <p class="ver_orden mb--25 mb-md--20"><?= e($fechaFormateada) ?></p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 mb-sm--30">
                                            <div class="about-text">
                                                <h3>Total</h3>
                                                <p class="ver_orden mb--25 mb-md--20">S/ <?= e($totalOrden) ?></p>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4 mb-sm--30">
                                            <div class="about-text">
                                                <h3>Método de pago</h3>
                                                <p class="ver_orden mb--25 mb-md--20"><?= e($orden['metodo_pago'] ?? '') ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <br>

                                    <div class="row centrar_orden">
                                        <div class="col-lg-6 col-md-6 mb-sm--30">
                                            <div class="about-text">
                                                <h3>Dirección de envío</h3>
                                                <p class="ver_orden mb--25 mb-md--20">
                                                    <?= e($orden['direccion'] ?? '') ?><?php if (!empty($orden['distrito'] ?? '')): ?>, <?= e($orden['distrito']) ?><?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 mb-sm--30">
                                            <div class="about-text">
                                                <h3>Nº de whatsapp</h3>
                                                <p class="ver_orden mb--25 mb-md--20"><?= e($orden['telefono'] ?? '') ?></p>
                                            </div>
                                        </div>
                                    </div>

                                    <br>

                                    <div class="col-lg-12 mt-md--40">
                                        <div class="order-details">
                                            <div class="cliente">
                                                <p><strong>Cliente:</strong> <?= e($orden['nombre'] ?? '') ?> <?= e($orden['apellidos'] ?? '') ?></p>
                                                <?php if (!empty($orden['dni'] ?? '')): ?>
                                                    <p><strong>DNI:</strong> <?= e($orden['dni']) ?></p>
                                                <?php endif; ?>
                                                <?php if (!empty($orden['telefono'] ?? '')): ?>
                                                    <p><strong>Teléfono:</strong> <?= e($orden['telefono']) ?></p>
                                                <?php endif; ?>
                                                <?php if (!empty($orden['email'] ?? '')): ?>
                                                    <p><strong>Email:</strong> <?= e($orden['email']) ?></p>
                                                <?php endif; ?>
                                                <p><strong>Dirección:</strong> <?= e($orden['direccion'] ?? '') ?><?php if (!empty($orden['distrito'] ?? '')): ?>, <?= e($orden['distrito']) ?><?php endif; ?></p>
                                                <?php if ($referencia !== ''): ?>
                                                    <p><strong>Referencia:</strong> <?= e($referencia) ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($metodoEnvio !== ''): ?>
                                                <p><strong>Método de envío:</strong> <?= e($metodoEnvio) ?></p>
                                            <?php endif; ?>
                                            <p><strong>Costo de envío:</strong> S/ <?= e($costoEnvio) ?></p>
                                            <p><strong>Total:</strong> S/ <?= e($totalOrden) ?></p>
                                            <div class="table-content table-responsive mb--30">
                                                <table class="table order-table order-table-2">
                                                    <thead>
                                                        <tr>
                                                            <th class="negrito_orden">Nombre del producto</th>
                                                            <th class="texto_centrado negrito_orden">Color</th>
                                                            <th class="texto_centrado negrito_orden">Talla</th>
                                                            <th class="texto_centrado negrito_orden">Cantidad</th>
                                                            <th class="texto_centrado negrito_orden">Precio</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if ($detalle === []): ?>
                                                            <tr>
                                                                <td colspan="5" class="texto_centrado">No hay productos en esta orden.</td>
                                                            </tr>
                                                        <?php else: ?>
                                                            <?php foreach ($detalle as $item): ?>
                                                                <tr class="orden-item">
                                                                    <th><?= e($item['nombre'] ?? '') ?></th>
                                                                    <td class="texto_centrado"><?= e($item['color'] ?? '-') ?></td>
                                                                    <td class="texto_centrado"><?= e($item['talla'] ?? '-') ?></td>
                                                                    <td class="texto_centrado">x<?= e((string) ($item['cantidad'] ?? 0)) ?></td>
                                                                    <td class="texto_centrado">S/ <?= e(number_format((float) ($item['precio_unitario'] ?? 0), 2)) ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="shipping">
                                                            <th class="negrito_orden">Costo de Envío</th>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td class="texto_centrado">S/ <?= e($costoEnvio) ?></td>
                                                        </tr>
                                                        <tr class="order-total">
                                                            <th class="negrito_orden">Total</th>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td class="texto_centrado"><span class="order-total-ammount total_pedido">S/ <?= e($totalOrden) ?></span></td>
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
                                                        <label class="form__label form__label--3" for="comment_email">Email<span class="required">*</span></label>
                                                        <input type="email" name="comment_email" id="comment_email" placeholder="jconde@innperuweb.com" class="form__input">
                                                    </div>
                                                    <div class="col-md-4 mb-sm--20">
                                                        <label class="form__label form__label--3" for="comment_name">Nombre<span class="required">*</span></label>
                                                        <input type="text" name="comment_name" id="comment_name" placeholder="Jared" class="form__input">
                                                    </div>
                                                    <div class="col-md-4 mb-sm--20">
                                                        <label class="form__label form__label--3" for="comment_lastname">Apellidos<span class="required">*</span></label>
                                                        <input type="text" name="comment_lastname" id="comment_lastname" placeholder="Conde Tantalean" class="form__input">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form__label form__label--3" for="comment_dni">DNI</label>
                                                        <input type="text" name="comment_dni" id="comment_dni" placeholder="54785698" class="form__input">
                                                    </div>
                                                    <div class="col-md-4 mb-sm--20">
                                                        <label class="form__label form__label--3" for="comment_phone">N° de whatsapp<span class="required">*</span></label>
                                                        <input type="text" name="comment_phone" id="comment_phone" placeholder="997199995" class="form__input">
                                                    </div>
                                                    <div class="col-md-4 mb-sm--20">
                                                        <label class="form__label form__label--3" for="comment_address">Dirección de envío<span class="required">*</span></label>
                                                        <input type="text" name="comment_address" id="comment_address" placeholder="Av. Tomás Valle 1250 -  Los Olivos" class="form__input">
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
