<?php

declare(strict_types=1);

require_once APP_PATH . '/controllers/BaseController.php';
require_once APP_PATH . '/helpers/session_helper.php';

class CheckoutController extends BaseController
{
    public function index(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $carrito = get_cart_session();
        $total = $this->calcularTotal($carrito);

        $_SESSION['total'] = number_format($total, 2, '.', '');

        $datosGuardados = $_SESSION['datos_cliente'] ?? [];

        $this->render('checkout', compact('carrito', 'total', 'datosGuardados'));
    }

    public function procesar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $nombre = trim($_POST['nombre'] ?? '');
            $apellidos = trim($_POST['apellidos'] ?? '');
            $dni = trim($_POST['dni'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $referencia = trim($_POST['referencia'] ?? '');
            $distrito_codigo = trim($_POST['distrito'] ?? '');
            $distrito_nombre = trim($_POST['distrito_nombre'] ?? '');
            $guardarDatos = isset($_POST['shipdifferetads']);

            $metodo_envio = trim($_POST['metodo_envio'] ?? '');
            $metodo_pago = trim($_POST['metodo_pago'] ?? '');
            $total = $_SESSION['total'] ?? '0.00';

            if (
                empty($email) ||
                empty($nombre) ||
                empty($apellidos) ||
                empty($dni) ||
                empty($telefono) ||
                empty($metodo_envio) ||
                empty($metodo_pago)
            ) {
                echo "<script>alert('Por favor complete todos los campos obligatorios.'); window.history.back();</script>";
                exit;
            }

            $pago_titulos = [
                'transferencia' => 'TRANSFERENCIA BANCARIA',
                'yape_plin' => 'PAGO CON YAPE / PLIN',
                'tarjeta' => 'PAGO CON TARJETA CRÉDITO / DÉBITO',
            ];
            $metodo_pago_titulo = $pago_titulos[$metodo_pago] ?? strtoupper($metodo_pago);

            $envio_titulos = [
                'bank' => 'Envío en Lima Metropolitana (S/ 10.00)',
                'cheque' => 'Envío a Provincias (S/ 12.00)',
                'cash' => 'Aéreo / Express (S/ 18.00)',
            ];
            $metodo_envio_titulo = $envio_titulos[$metodo_envio] ?? strtoupper($metodo_envio);

            $checkout = [
                'email' => $email,
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'dni' => $dni,
                'telefono' => $telefono,
                'direccion' => $direccion,
                'referencia' => $referencia,
                'distrito_codigo' => $distrito_codigo,
                'distrito_nombre' => $distrito_nombre,
                'metodo_envio' => $metodo_envio,
                'metodo_envio_titulo' => $metodo_envio_titulo,
                'metodo_pago' => $metodo_pago,
                'metodo_pago_titulo' => $metodo_pago_titulo,
                'total' => $total,
            ];

            $optionalFields = [
                'departamento' => trim($_POST['departamento'] ?? ''),
                'provincia' => trim($_POST['provincia'] ?? ''),
                'distrito_provincia' => trim($_POST['distrito_provincia'] ?? ''),
                'direccion_provincia' => trim($_POST['direccion_provincia'] ?? ''),
                'notas' => trim($_POST['notas'] ?? ''),
            ];

            foreach ($optionalFields as $key => $value) {
                $checkout[$key] = $value;
            }

            if ($guardarDatos) {
                $_SESSION['datos_cliente'] = [
                    'email' => $email,
                    'nombre' => $nombre,
                    'apellidos' => $apellidos,
                    'dni' => $dni,
                    'telefono' => $telefono,
                    'direccion' => $direccion,
                    'referencia' => $referencia,
                    'distrito' => $distrito_codigo,
                    'distrito_nombre' => $distrito_nombre,
                ];

                foreach ($optionalFields as $campo => $valor) {
                    $_SESSION['datos_cliente'][$campo] = $valor;
                }
            } elseif (isset($_SESSION['datos_cliente'])) {
                unset($_SESSION['datos_cliente']);
            }

            $_SESSION['checkout'] = $checkout;

            header('Location: ' . base_url('ver_orden'));
            exit;
        }

        header('Location: ' . base_url('checkout'));
        exit;
    }

    public function ver_orden(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $checkout = $_SESSION['checkout'] ?? null;
        $carrito = $_SESSION['carrito'] ?? [];

        if ($checkout === null || $carrito === []) {
            header('Location: ' . base_url('checkout'));
            exit;
        }

        if (empty($_SESSION['orden_numero'])) {
            try {
                $_SESSION['orden_numero'] = 'NOV-' . strtoupper(substr(bin2hex(random_bytes(6)), 0, 8));
            } catch (\Throwable $exception) {
                $_SESSION['orden_numero'] = 'NOV-' . strtoupper(substr(md5((string) microtime(true)), 0, 8));
            }
        }

        $orden_numero = (string) $_SESSION['orden_numero'];

        date_default_timezone_set('America/Lima');
        $fecha = date('d/m/Y');

        $items = [];
        $total = 0.0;

        foreach ($carrito as $item) {
            $cantidad = max(1, (int) ($item['cantidad'] ?? 1));
            $precio = (float) ($item['precio'] ?? 0);
            $subtotal = $cantidad * $precio;
            $total += $subtotal;

            $items[] = [
                'nombre' => $item['nombre'] ?? 'Producto',
                'cantidad' => $cantidad,
                'precio' => $precio,
                'subtotal' => $subtotal,
                'color' => trim((string) ($item['color'] ?? '')),
                'talla' => trim((string) ($item['talla'] ?? '')),
                'imagen' => $item['imagen'] ?? '',
            ];
        }

        $orden = [
            'numero' => $orden_numero,
            'fecha' => $fecha,
            'metodo_envio' => $checkout['metodo_envio_titulo'] ?? '',
            'metodo_pago' => $checkout['metodo_pago_titulo'] ?? '',
            'cliente' => [
                'nombre' => $checkout['nombre'] ?? '',
                'apellidos' => $checkout['apellidos'] ?? '',
                'dni' => $checkout['dni'] ?? '',
                'telefono' => $checkout['telefono'] ?? '',
                'email' => $checkout['email'] ?? '',
                'distrito_codigo' => $checkout['distrito_codigo'] ?? '',
                'distrito_nombre' => $checkout['distrito_nombre'] ?? '',
                'direccion' => $checkout['direccion'] ?? '',
                'referencia' => $checkout['referencia'] ?? '',
            ],
            'totales' => [
                'total' => $total,
            ],
        ];

        $this->render('ver_orden', compact('orden', 'items'));
    }

    public function carrito(): void
    {
        $this->render('carrito_compras');
    }

    public function obtener_datos_cliente(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        $email = trim($_GET['email'] ?? '');

        if ($email === '') {
            echo json_encode(['success' => false], JSON_UNESCAPED_UNICODE);
            return;
        }

        $datos = $_SESSION['datos_cliente'] ?? [];

        if ($datos !== [] && isset($datos['email']) && $datos['email'] === $email) {
            echo json_encode([
                'success' => true,
                'data' => $datos,
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        echo json_encode(['success' => false], JSON_UNESCAPED_UNICODE);
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
