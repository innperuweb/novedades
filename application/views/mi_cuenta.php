<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once APP_PATH . '/helpers/security_helper.php';
require_once APP_PATH . '/models/OrdenModel.php';

$email_cliente = $_SESSION['email_cliente'] ?? null;

$ordenes = $email_cliente ? OrdenModel::obtenerPorEmail($email_cliente) : [];
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
                                <a class="nav-link active" id="dashboard-tab" data-bs-toggle="pill" href="#dashboard" role="tab" aria-controls="dashboard" aria-selected="true">Órdenes</a>
                                <a class="nav-link" id="datosp-tab" data-bs-toggle="pill" href="#datosp" role="tab" aria-controls="datosp" aria-selected="false">Datos personales</a>
                                <a class="nav-link" href="#">Cerrar sesión</a>
                            </nav>
                        </div>

                        <div class="col-md-10">
                            <div class="tab-content" id="acct-tabContent">
                                <div class="tab-pane fade show active" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">

                                    <div class="table-content table-responsive">
                                        <div class="table-content table-responsive">
                                            <table class="table compare-table">
                                                <thead>
                                                    <tr>
                                                        <th>Nº de órden</th>
                                                        <th>Fecha</th>
                                                        <th>Total</th>
                                                        <th>Estado</th>
                                                        <th>Ver</th>
                                                        <th>Eliminar</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($ordenes)): ?>
                                                        <?php foreach ($ordenes as $orden): ?>
                                                            <tr>
                                                                <td><?= e($orden['nro_orden']) ?></td>
                                                                <td><?= date('d/m/Y', strtotime($orden['fecha'])) ?></td>
                                                                <td>S/ <?= number_format((float) $orden['total'], 2) ?></td>
                                                                <td><?= e($orden['estado']) ?></td>
                                                                <td><a href="<?= base_url('ver_orden?nro=' . urlencode($orden['nro_orden'])) ?>">Ver</a></td>
                                                                <td><a href="<?= base_url('mi_cuenta/eliminar?nro=' . urlencode($orden['nro_orden'])) ?>" class="text-danger" onclick="return confirm('¿Eliminar esta orden?');">✕</a></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr><td colspan="6">No tienes órdenes registradas.</td></tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
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

<!-- Main Content Wrapper Start -->
<div id="content" class="main-content-wrapper">
    <div class="page-content-inner">
        <div class="container">
            <div class="row ptb--80 ptb-md--60 ptb-sm--40">
                <div class="col-12" id="main-content">

                </div>
            </div>
        </div>
    </div>
</div>

