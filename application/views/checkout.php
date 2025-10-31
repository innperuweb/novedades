<?php
$carritoItems = isset($carrito) && is_array($carrito) ? $carrito : ($_SESSION['carrito'] ?? []);
$totalPedido = isset($total) ? (float) $total : 0.0;
$datosCliente = isset($datosGuardados) && is_array($datosGuardados) ? $datosGuardados : [];
$cliente = isset($cliente) && is_array($cliente) ? $cliente : [];

$emailGuardado = htmlspecialchars($datosCliente['email'] ?? '', ENT_QUOTES, 'UTF-8');
$nombreGuardado = htmlspecialchars($datosCliente['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
$apellidosGuardados = htmlspecialchars($datosCliente['apellidos'] ?? '', ENT_QUOTES, 'UTF-8');
$dniGuardado = htmlspecialchars($datosCliente['dni'] ?? '', ENT_QUOTES, 'UTF-8');
$telefonoGuardado = htmlspecialchars($datosCliente['telefono'] ?? '', ENT_QUOTES, 'UTF-8');
$direccionGuardada = htmlspecialchars($datosCliente['direccion'] ?? '', ENT_QUOTES, 'UTF-8');
$referenciaGuardada = htmlspecialchars($datosCliente['referencia'] ?? '', ENT_QUOTES, 'UTF-8');
$notasGuardadas = htmlspecialchars($datosCliente['notas'] ?? '', ENT_QUOTES, 'UTF-8');
$distritoGuardado = htmlspecialchars($datosCliente['distrito'] ?? '', ENT_QUOTES, 'UTF-8');
$distritoNombreGuardado = htmlspecialchars($datosCliente['distrito_nombre'] ?? '', ENT_QUOTES, 'UTF-8');
$departamentoGuardado = htmlspecialchars($datosCliente['departamento'] ?? '', ENT_QUOTES, 'UTF-8');
$provinciaGuardada = htmlspecialchars($datosCliente['provincia'] ?? '', ENT_QUOTES, 'UTF-8');
$distritoProvinciaGuardado = htmlspecialchars($datosCliente['distrito_provincia'] ?? '', ENT_QUOTES, 'UTF-8');
$direccionProvinciaGuardada = htmlspecialchars($datosCliente['direccion_provincia'] ?? '', ENT_QUOTES, 'UTF-8');

$nombreSesion = htmlspecialchars($_SESSION['nombre_cliente'] ?? '', ENT_QUOTES, 'UTF-8');
$emailSesion = htmlspecialchars($_SESSION['email_cliente'] ?? '', ENT_QUOTES, 'UTF-8');
$telefonoCliente = htmlspecialchars($cliente['telefono'] ?? '', ENT_QUOTES, 'UTF-8');
$direccionCliente = htmlspecialchars($cliente['direccion'] ?? '', ENT_QUOTES, 'UTF-8');
$apellidosCliente = htmlspecialchars($cliente['apellidos'] ?? '', ENT_QUOTES, 'UTF-8');
$referenciaCliente = htmlspecialchars($cliente['referencia'] ?? '', ENT_QUOTES, 'UTF-8');

$nombreValor = $nombreSesion !== '' ? $nombreSesion : $nombreGuardado;
$emailValor = $emailSesion !== '' ? $emailSesion : $emailGuardado;
$apellidosValor = $apellidosGuardados !== '' ? $apellidosGuardados : $apellidosCliente;
$telefonoValor = $telefonoCliente !== '' ? $telefonoCliente : $telefonoGuardado;
$direccionValor = $direccionCliente !== '' ? $direccionCliente : $direccionGuardada;
$referenciaValor = $referenciaCliente !== '' ? $referenciaCliente : $referenciaGuardada;
?>

<div class="breadcrumb-area bg--white-6 breadcrumb-bg-1 pt--60 pb--70 pt-lg--40 pb-lg--50 pt-md--30 pb-md--40">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="page-title">Checkout</h1>
            </div>
        </div>
    </div>
</div>

<div id="content" class="main-content-wrapper ptb--80">
    <div class="page-content-inner">
        <div class="container">
            <form method="POST" action="<?= base_url('checkout/procesar') ?>" class="checkout-form">
                <div class="row pb--80 pb-md--60 pb-sm--40">
                    <div class="col-lg-6">
                        <div class="checkout-title mt--10">
                            <h2>Ingresa tu correo electrónico para continuar</h2>
                        </div>
                        <div class="checkout-form">
                            <div class="row mb--30">
                                <div class="form__group col-12">
                                    <label for="shipping_email" class="form__label form__label--2">Correo electrónico <span class="required">*</span></label>
                                    <input type="email" name="email" id="shipping_email" class="form__input form__input--2" value="<?= $emailValor; ?>" required>
                                </div>
                            </div>

                            <div class="checkout-title mt--10">
                                <h2></h2>
                            </div>

                            <div class="row mb--30">
                                <div class="form__group col-md-6 mb-sm--30">
                                    <label for="billing_fname" class="form__label form__label--2">Nombre
                                        <span class="required">*</span></label>
                                    <input type="text" name="nombre" id="billing_fname" class="form__input form__input--2" value="<?= $nombreValor; ?>" required>
                                </div>
                                <div class="form__group col-md-6">
                                    <label for="billing_lname" class="form__label form__label--2">Apellidos
                                        <span class="required">*</span></label>
                                    <input type="text" name="apellidos" id="billing_lname" class="form__input form__input--2" value="<?= $apellidosValor; ?>">
                                </div>
                            </div>
                            <div class="row mb--30">
                                <div class="form__group col-md-6 mb-sm--30">
                                    <label for="billing_dni" class="form__label form__label--2">DNI
                                        <span class="required">*</span></label>
                                    <input type="text" name="dni" id="billing_dni" class="form__input form__input--2" value="<?= $dniGuardado; ?>">
                                </div>
                                <div class="form__group col-md-6">
                                    <label for="billing_phone" class="form__label form__label--2">N° de Whatsapp
                                        <span class="required">*</span></label>
                                    <input type="text" name="telefono" id="billing_phone" class="form__input form__input--2" value="<?= $telefonoValor; ?>" required>
                                </div>
                            </div>

                            <h3 class="metodo">Método de Envío</h3>
                            <div class="checkout-payment">
                                <div class="payment-group mb--10">
                                    <div class="payment-radio">
                                        <input type="radio" value="bank" name="metodo_envio" id="bank">
                                        <label class="payment-label" for="bank">Envío en Lima Metropolitana (S/ 10.00)</label>
                                    </div>
                                    <div class="payment-info" data-method="bank">
                                        <div class="row mb--30">
                                            <div class="form__group col-12">
                                                <label for="billing_country" class="form__label form__label--2">Distrito
                                                    <span class="required">*</span></label>
                                                <select id="billing_country" name="distrito" class="form__input form__input--2 nice-select" required data-valor-guardado="<?= $distritoGuardado; ?>">
                                                    <option value="">Seleccionar</option>
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
                                                <input type="hidden" name="distrito_nombre" id="distrito_nombre" value="<?= $distritoNombreGuardado; ?>">
                                            </div>
                                        </div>
                                        <div class="row mb--30">
                                            <div class="form__group col-12">
                                                <label for="billing_company" class="form__label form__label--2">Escriba la dirección de entrega completa</label>
                                                <input type="text" name="direccion" id="billing_company" class="form__input form__input--2" value="<?= $direccionValor; ?>" required>
                                            </div>
                                        </div>
                                        <div class="row mb--30">
                                            <div class="form__group col-12">
                                                <label for="billing_reference" class="form__label form__label--2">Referencia</label>
                                                <input type="text" name="referencia" id="billing_reference" class="form__input form__input--2" value="<?= $referenciaValor; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="payment-group mb--10">
                                    <div class="payment-radio">
                                        <input type="radio" value="cheque" name="metodo_envio" id="cheque">
                                        <label class="payment-label" for="cheque">
                                            ENVÍO A PROVINCIAS (S/ 12.00)
                                        </label>
                                    </div>
                                    <div class="payment-info cheque hide-in-default" data-method="cheque">
                                        <div class="row mb--30">
                                            <div class="form__group col-12">
                                                <label for="billing_country_provincia" class="form__label form__label--2">Departamento
                                                    <span class="required">*</span></label>
                                                <select id="billing_country_provincia" name="departamento" class="form__input form__input--2 nice-select" data-valor-guardado="<?= $departamentoGuardado; ?>">
                                                    <option value="">Seleccionar</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb--30">
                                            <div class="form__group col-12">
                                                <label for="billing_provincia" class="form__label form__label--2">Provincia
                                                    <span class="required">*</span></label>
                                                <select id="billing_provincia" name="provincia" class="form__input form__input--2 nice-select" data-valor-guardado="<?= $provinciaGuardada; ?>">
                                                    <option value="">Seleccionar</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb--30">
                                            <div class="form__group col-12">
                                                <label for="billing_distrito_provincia" class="form__label form__label--2">Distrito
                                                    <span class="required">*</span></label>
                                                <select id="billing_distrito_provincia" name="distrito_provincia" class="form__input form__input--2 nice-select" data-valor-guardado="<?= $distritoProvinciaGuardado; ?>">
                                                    <option value="">Seleccionar</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb--30">
                                            <div class="form__group col-12">
                                                <label for="billing_company" class="form__label form__label--2">Escriba la dirección de entrega completa</label>
                                                <input type="text" name="direccion_provincia" id="billing_company_provincia" class="form__input form__input--2" value="<?= $direccionProvinciaGuardada; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="payment-group mb--10">
                                    <div class="payment-radio">
                                        <input type="radio" value="cash" name="metodo_envio" id="cash">
                                        <label class="payment-label" for="cash">
                                            AÉREO/EXPRESS (S/ 18.00)
                                        </label>
                                    </div>
                                    <div class="payment-info cash hide-in-default" data-method="cash">
                                        <p> <a class="fono" href="tel:+51901110822"> <i class="fa-brands fa-whatsapp" style="font-size: 20px; padding-top: 10px;"></i> &nbsp; Click para consultar disponibilidad</a> </p>
                                    </div>
                                </div>

                                <br>

                                <div class="row">
                                    <div class="form__group col-12">
                                        <div class="custom-checkbox mb--20">
                                            <input type="checkbox" id="guardar_datos" name="shipdifferetads" class="form__checkbox">
                                            <label for="guardar_datos" class="form__label form__label--2 shipping-label">Guardar mis datos para próximas compras</label>
                                        </div>
                                    </div>
                                    <div class="form__group col-12">
                                        <label for="orderNotes" class="form__label form__label--2">¿Algún comentario adicional?</label>
                                        <textarea class="form__input form__input--2 form__input--textarea" id="orderNotes" name="notas"><?= $notasGuardadas; ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-5 offset-xl-1 col-lg-6 mt-md--40">
                        <div class="order-details">
                            <div class="checkout-title mt--10">
                                <h2>Mi pedido</h2>
                            </div>
                            <div class="table-content table-responsive mb--30">
                                <table class="table order-table order-table-2">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($carritoItems)): ?>
                                            <?php foreach ($carritoItems as $item): ?>
                                                <?php
                                                $nombreProducto = htmlspecialchars($item['nombre'] ?? '', ENT_QUOTES, 'UTF-8');
                                                $cantidadProducto = (int) ($item['cantidad'] ?? 0);
                                                $cantidadProducto = $cantidadProducto > 0 ? $cantidadProducto : 1;
                                                $precioProducto = isset($item['precio']) ? (float) $item['precio'] : 0.0;
                                                $subtotalProducto = $precioProducto * $cantidadProducto;
                                                $colorProducto = trim((string) ($item['color'] ?? ''));
                                                $tallaProducto = trim((string) ($item['talla'] ?? ''));
                                                $colorProducto = $colorProducto !== '' ? htmlspecialchars($colorProducto, ENT_QUOTES, 'UTF-8') : '';
                                                $tallaProducto = $tallaProducto !== '' ? htmlspecialchars($tallaProducto, ENT_QUOTES, 'UTF-8') : '';
                                                ?>
                                                <tr>
                                                    <th><?= $nombreProducto; ?>
                                                        <?php if ($colorProducto !== '' || $tallaProducto !== ''): ?>
                                                            <br><small>
                                                                <?php if ($colorProducto !== ''): ?>Color: <?= $colorProducto; ?><?php endif; ?>
                                                                <?php if ($colorProducto !== '' && $tallaProducto !== ''): ?> — <?php endif; ?>
                                                                <?php if ($tallaProducto !== ''): ?>Talla: <?= $tallaProducto; ?><?php endif; ?>
                                                            </small>
                                                        <?php endif; ?>
                                                        <strong><span>&#10005;</span><?= $cantidadProducto; ?></strong>
                                                    </th>
                                                    <td class="text-end">S/ <?= number_format($subtotalProducto, 2); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="2">No hay productos en el carrito.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                    <?php if (!empty($carritoItems)): ?>
                                        <tfoot>
                                            <tr class="cart-subtotal">
                                                <th>Subtotal</th>
                                                <td class="text-end">S/ <?= number_format($totalPedido, 2); ?></td>
                                            </tr>
                                            <tr class="order-total">
                                                <th>Total</th>
                                                <td class="text-end"><span class="order-total-ammount total_pedido">S/ <?= number_format($totalPedido, 2); ?></span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    <?php endif; ?>
                                </table>
                            </div>

                            <h3 class="metodo">Método de Pago</h3>
                            <div class="checkout-payment">
                                <div class="payment-group mb--10">
                                    <div class="payment-radio">
                                        <input type="radio" value="transferencia" name="metodo_pago" id="transferencia">
                                        <label class="payment-label" for="transferencia">TRANSFERENCIA BANCARIA</label>
                                    </div>
                                    <div class="payment-info" data-method="transferencia">
                                        <div class="row">
                                            <p style="padding: 10px 35px;">Realiza tu pago directamente a nuestra cuenta bancaria. Por favor, usa el número de pedido como referencia de pago. Tú pedido no se procesará hasta que se haya recibido el importe en nuestra cuenta bancaria</p>
                                            <div class="banner-box banner-type-5 banner-hover-3">
                                                <div class="banner-inner">
                                                    <div class="banner-image">
                                                        <img src="<?= asset_url('img/pago/bcp.jpg'); ?>" alt="Banner">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                    </div>
                                </div>
                                <div class="payment-group mb--10">
                                    <div class="payment-radio">
                                        <input type="radio" value="yape_plin" name="metodo_pago" id="yape_plin">
                                        <label class="payment-label" for="yape_plin">
                                            PAGO CON YAPE / PLIN
                                        </label>
                                    </div>
                                    <div class="payment-info yape hide-in-default" data-method="yape_plin">
                                    <div class="row">
                                        <p style="padding: 10px 35px;">Realice su pago escaneando el código <b>QR</b> ó al número <b> <a href="tel:+51901110822"> 901 110 822 </a> </b> y envíe su pago al correo <b><a href="mailto:ys@novedades.pe">ys@novedades.pe</a></b> ó al <b> <a style="color: #19c44d;" href="https://api.whatsapp.com/send?phone=+51901110822&text=Envío%20mi%20pago%20">whatsapp</a> </b> </p>
                                        <div class="col-md-6">
                                            <div class="banner-box banner-type-5 banner-hover-3">
                                                <div class="banner-inner">
                                                    <div class="banner-image">
                                                        <img src="<?= asset_url('img/pago/yape.png'); ?>" alt="Banner">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="banner-box banner-type-5 banner-hover-3">
                                                <div class="banner-inner">
                                                    <div class="banner-image">
                                                        <img src="<?= asset_url('img/pago/plin.png'); ?>" alt="Banner">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="a_nombre"> <b>A nombre de: NOVEDADES SAC</b> </p>
                                    </div>
                                    </div>
                                </div>

                                <div class="payment-group mb--10">
                                    <div class="payment-radio">
                                        <input type="radio" value="tarjeta" name="metodo_pago" id="tarjeta">
                                        <label class="payment-label" for="tarjeta">
                                            PAGO CON TARJETA CRÉDITO / DÉBITO
                                        </label>
                                    </div>
                                </div>

                                <br>

                                <button type="submit" class="btn btn-fullwidth btn-style-1">
                                    Realizar pedido
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const distritoSelect = document.getElementById('billing_country');
                        const distritoHidden = document.getElementById('distrito_nombre');

                        if (distritoSelect && distritoHidden) {
                            distritoSelect.addEventListener('change', function() {
                                const selectedText = distritoSelect.options[distritoSelect.selectedIndex].text;
                                distritoHidden.value = selectedText;
                            });
                        }
                    });
                </script>
            </form>
        </div>
    </div>
</div>

