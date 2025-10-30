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

        $this->render('checkout', compact('carrito', 'total'));
    }

    public function procesar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . base_url('checkout'));
            exit;
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $distrito = trim($_POST['distrito'] ?? '');
        $referencia = trim($_POST['referencia'] ?? '');
        $notas = trim($_POST['notas'] ?? '');

        $carrito = get_cart_session();
        $total = $this->calcularTotal($carrito);

        require VIEW_PATH . 'partials/head.php';
        require VIEW_PATH . 'partials/header.php';

        $nombreSeguro = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
        $totalFormateado = number_format($total, 2);

        echo "<main class='container py-5 text-center'>
                <h2>✅ Pedido recibido</h2>
                <p>Gracias, <strong>{$nombreSeguro}</strong>. Hemos recibido tus datos.</p>
                <p>Total del pedido: <strong>S/ {$totalFormateado}</strong></p>
                <a href='" . base_url('index.php') . "' class='btn btn-primary mt-3'>Volver al inicio</a>
              </main>";

        require VIEW_PATH . 'partials/footer.php';
        require VIEW_PATH . 'partials/scripts.php';
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
