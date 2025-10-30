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
        session_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Capturar datos del formulario
            $email = trim($_POST['email'] ?? '');
            $nombre = trim($_POST['nombre'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $dni = trim($_POST['dni'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $referencia = trim($_POST['referencia'] ?? '');
            $distrito = trim($_POST['distrito'] ?? '');
            $departamento = trim($_POST['departamento'] ?? '');
            $provincia = trim($_POST['provincia'] ?? '');
            $distrito_provincia = trim($_POST['distrito_provincia'] ?? '');
            $direccion_provincia = trim($_POST['direccion_provincia'] ?? '');
            $notas = trim($_POST['notas'] ?? '');
            $metodo_envio = trim($_POST['metodo_envio'] ?? '');
            $metodo_pago = trim($_POST['metodo_pago'] ?? '');
            $total = $_SESSION['total'] ?? '0.00';

            // Validar campos obligatorios
            if (empty($email) || empty($nombre) || empty($apellidos) || empty($dni) || empty($telefono)) {
                echo "<script>alert('Por favor complete todos los campos obligatorios.'); window.history.back();</script>";
                exit;
            }

            // Guardar los datos en sesión
            $_SESSION['checkout'] = [
                'email' => $email,
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'dni' => $dni,
                'telefono' => $telefono,
                'direccion' => $direccion,
                'referencia' => $referencia,
                'distrito' => $distrito,
                'departamento' => $departamento,
                'provincia' => $provincia,
                'distrito_provincia' => $distrito_provincia,
                'direccion_provincia' => $direccion_provincia,
                'notas' => $notas,
                'metodo_envio' => $metodo_envio,
                'metodo_pago' => $metodo_pago,
                'total' => $total
            ];

            // Redirigir a ver_orden.php
            header("Location: " . base_url('ver_orden'));
            exit;
        } else {
            header("Location: " . base_url('checkout'));
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
