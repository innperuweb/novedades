<?php

declare(strict_types=1);

require_once APP_PATH . '/controllers/BaseController.php';
require_once APP_PATH . '/helpers/session_helper.php';

class CheckoutController extends BaseController
{
    public function index(): void
    {
        $carrito = get_cart_session();
        $total = $this->calcularTotal($carrito);

        $_SESSION['total'] = number_format($total, 2, '.', '');

        $this->render('checkout', compact('carrito', 'total'));
    }

    public function procesar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $correo = $_POST['correo'] ?? '';
            $telefono = $_POST['telefono'] ?? '';
            $direccion = $_POST['direccion'] ?? '';
            $total = $_SESSION['total'] ?? '0.00';

            $nombreSeguro = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
            $totalSeguro = htmlspecialchars((string) $total, ENT_QUOTES, 'UTF-8');
            $baseUrl = base_url('index');

            echo "
                <div style='text-align:center; margin-top:80px; font-family:sans-serif'>
                    <h2>✅ Pedido recibido</h2>
                    <p>Gracias, <strong>{$nombreSeguro}</strong>.</p>
                    <p>Tu pedido ha sido registrado correctamente (modo demostración).</p>
                    <p>Total del pedido: <strong>S/ {$totalSeguro}</strong></p>
                    <a href='{$baseUrl}' style='display:inline-block;margin-top:20px;padding:10px 20px;background:#000;color:#fff;text-decoration:none;border-radius:4px;'>Volver a la tienda</a>
                </div>
            ";
        } else {
            header('Location: ' . base_url('checkout'));
            exit;
        }
    }

    public function carrito(): void
    {
        $this->render('carrito_compras');
    }

    private function calcularTotal(array $carrito): float
    {
        $total = 0.0;

        foreach ($carrito as $item) {
            $precio = isset($item['precio']) ? (float) $item['precio'] : 0.0;
            $cantidad = isset($item['cantidad']) ? (int) $item['cantidad'] : 0;
            $total += $precio * $cantidad;
        }

        return $total;
    }
}
