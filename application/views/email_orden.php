<?php
require_once APP_PATH . '/helpers/security_helper.php';

$orden = $orden ?? [];
$items = $items ?? [];
$cliente = $orden['cliente'] ?? [];
$subtotal = isset($orden['subtotal']) ? (float) $orden['subtotal'] : (float) ($orden['totales']['subtotal'] ?? 0);
$costoEnvio = isset($orden['costo_envio']) ? (float) $orden['costo_envio'] : (float) ($orden['totales']['costo_envio'] ?? 0);
$total = isset($orden['total']) ? (float) $orden['total'] : (float) ($orden['totales']['total'] ?? 0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de orden <?= e($orden['numero'] ?? '') ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
        }
        h1 {
            font-size: 20px;
            margin-bottom: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        th,
        td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: left;
        }
        tfoot td {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Gracias por tu compra, <?= e(trim(($cliente['nombre'] ?? '') . ' ' . ($cliente['apellidos'] ?? ''))) ?></h1>

    <p><strong>N° de orden:</strong> <?= e($orden['numero'] ?? '') ?></p>
    <p><strong>Fecha:</strong> <?= e($orden['fecha'] ?? '') ?></p>
    <p><strong>Método de envío:</strong> <?= e($orden['metodo_envio_texto'] ?? '') ?></p>
    <p><strong>Método de pago:</strong> <?= e($orden['metodo_pago'] ?? '') ?></p>

    <h2>Resumen de tu compra</h2>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td>
                        <?= e($item['nombre'] ?? '') ?>
                        <?php if (!empty($item['color'])): ?>
                            <br><small>Color: <?= e($item['color']) ?></small>
                        <?php endif; ?>
                        <?php if (!empty($item['talla'])): ?>
                            <br><small>Talla: <?= e($item['talla']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= (int) ($item['cantidad'] ?? 0) ?></td>
                    <td>S/ <?= number_format((float) ($item['precio'] ?? 0), 2) ?></td>
                    <td>S/ <?= number_format((float) ($item['subtotal'] ?? 0), 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right;">Subtotal</td>
                <td style="text-align: right;">S/ <?= number_format($subtotal, 2) ?></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">Costo de envío</td>
                <td style="text-align: right;">S/ <?= number_format($costoEnvio, 2) ?></td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: right;">Total a pagar</td>
                <td style="text-align: right;">S/ <?= number_format($total, 2) ?></td>
            </tr>
        </tfoot>
    </table>

    <?php if (!empty($cliente['direccion'])): ?>
        <p><strong>Dirección de envío:</strong> <?= e($cliente['direccion']) ?></p>
    <?php endif; ?>
    <?php if (!empty($cliente['distrito_nombre'])): ?>
        <p><strong>Distrito:</strong> <?= e($cliente['distrito_nombre']) ?></p>
    <?php endif; ?>
    <?php if (!empty($cliente['referencia'])): ?>
        <p><strong>Referencia:</strong> <?= e($cliente['referencia']) ?></p>
    <?php endif; ?>

    <p>Pronto nos pondremos en contacto para coordinar la entrega.</p>
</body>
</html>
