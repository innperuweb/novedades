<?php

declare(strict_types=1);

require_once APP_PATH . '/controllers/BaseController.php';
require_once APP_PATH . '/helpers/session_helper.php';
require_once APP_PATH . '/models/ClienteModel.php';

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

        $cliente = null;
        if (!empty($_SESSION['id_cliente'])) {
            $cliente = ClienteModel::obtenerPorId((int) $_SESSION['id_cliente']);

            if ($cliente !== null) {
                $_SESSION['email_cliente'] = $cliente['email'] ?? ($_SESSION['email_cliente'] ?? '');
                $_SESSION['nombre_cliente'] = $cliente['nombre'] ?? ($_SESSION['nombre_cliente'] ?? '');
            }
        }

        $this->render('checkout', compact('carrito', 'total', 'datosGuardados', 'cliente'));
    }

    public function procesar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . base_url('checkout'));
            exit;
        }

        $carrito = get_cart_session();

        if ($carrito === []) {
            echo "<script>alert('Tu carrito está vacío.'); window.location.href='" . base_url('checkout') . "';</script>";
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $dni = trim($_POST['dni'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $referencia = trim($_POST['referencia'] ?? '');
        $distritoCodigo = trim($_POST['distrito'] ?? '');
        $distritoNombre = trim($_POST['distrito_nombre'] ?? '');
        $guardarDatos = isset($_POST['shipdifferetads']);

        $metodoEnvio = trim($_POST['metodo_envio'] ?? '');
        $metodoPago = trim($_POST['metodo_pago'] ?? '');

        // Convertir código del distrito a nombre completo
        $distritos = [
            'AN' => 'Ancón', 'AT' => 'Ate', 'BR' => 'Barranco', 'BE' => 'Breña',
            'CA' => 'Carabayllo', 'CC' => 'Chaclacayo', 'CH' => 'Chorrillos', 'CI' => 'Cieneguilla',
            'CM' => 'Comas', 'EA' => 'El Agustino', 'IN' => 'Independencia', 'JM' => 'Jesús María',
            'LM' => 'La Molina', 'LV' => 'La Victoria', 'LI' => 'Lince', 'LO' => 'Los Olivos',
            'LU' => 'Lurigancho', 'LR' => 'Lurín', 'MM' => 'Magdalena del Mar', 'MO' => 'Miraflores',
            'PC' => 'Pachacamac', 'PU' => 'Pucusana', 'PL' => 'Pueblo Libre', 'PP' => 'Puente Piedra',
            'PH' => 'Punta Hermosa', 'PN' => 'Punta Negra', 'RI' => 'Rímac', 'SB' => 'San Bartolo',
            'SBJ' => 'San Borja', 'SI' => 'San Isidro', 'SL' => 'San Juan de Lurigancho',
            'SM' => 'San Juan de Miraflores', 'SC' => 'San Luis', 'SP' => 'San Martín de Porres',
            'SG' => 'San Miguel', 'SA' => 'Santa Anita', 'SMR' => 'Santa María del Mar',
            'SR' => 'Santa Rosa', 'SS' => 'Santiago de Surco', 'SU' => 'Surquillo',
            'VS' => 'Villa El Salvador', 'VT' => 'Villa María del Triunfo'
        ];

        // Determinar nombre del distrito
        if ($distritoCodigo !== '') {
            $distritoNombre = $distritos[$distritoCodigo] ?? $distritoNombre;
        }

        if (
            $email === '' ||
            $nombre === '' ||
            $apellidos === '' ||
            $dni === '' ||
            $telefono === '' ||
            $metodoEnvio === '' ||
            $metodoPago === ''
        ) {
            echo "<script>alert('Por favor complete todos los campos obligatorios.'); window.history.back();</script>";
            exit;
        }

        $pagoTitulos = [
            'transferencia' => 'TRANSFERENCIA BANCARIA',
            'yape_plin' => 'PAGO CON YAPE / PLIN',
            'tarjeta' => 'PAGO CON TARJETA CRÉDITO / DÉBITO',
        ];
        $metodoPagoTitulo = $pagoTitulos[$metodoPago] ?? strtoupper($metodoPago);

        $metodoEnvioTexto = 'Sin especificar';
        $costoEnvio = 0.0;

        switch ($metodoEnvio) {
            case 'lima':
            case 'bank':
                $costoEnvio = 10.00;
                $metodoEnvioTexto = 'Envío en Lima Metropolitana (S/ 10.00)';
                break;
            case 'provincia':
            case 'cheque':
                $costoEnvio = 12.00;
                $metodoEnvioTexto = 'Envío a Provincias (S/ 12.00)';
                break;
            case 'aereo':
            case 'cash':
                $costoEnvio = 18.00;
                $metodoEnvioTexto = 'Aéreo/Express (S/ 18.00)';
                break;
            default:
                $metodoEnvioTexto = 'Sin especificar';
                break;
        }

        $optionalFields = [
            'departamento' => trim($_POST['departamento'] ?? ''),
            'provincia' => trim($_POST['provincia'] ?? ''),
            'distrito_provincia' => trim($_POST['distrito_provincia'] ?? ''),
            'direccion_provincia' => trim($_POST['direccion_provincia'] ?? ''),
            'notas' => trim($_POST['notas'] ?? ''),
        ];

        if ($guardarDatos) {
            $_SESSION['datos_cliente'] = [
                'email' => $email,
                'nombre' => $nombre,
                'apellidos' => $apellidos,
                'dni' => $dni,
                'telefono' => $telefono,
                'direccion' => $direccion,
                'referencia' => $referencia,
                'distrito' => $distritoCodigo,
                'distrito_nombre' => $distritoNombre !== '' ? $distritoNombre : $distritoCodigo,
            ];

            foreach ($optionalFields as $campo => $valor) {
                $_SESSION['datos_cliente'][$campo] = $valor;
            }
        } elseif (isset($_SESSION['datos_cliente'])) {
            unset($_SESSION['datos_cliente']);
        }

        $subtotal = 0.0;
        $detalleItems = [];

        foreach ($carrito as $item) {
            $cantidad = max(1, (int) ($item['cantidad'] ?? 0));
            $precio = (float) ($item['precio'] ?? 0);
            $subtotalProducto = $cantidad * $precio;
            $subtotal += $subtotalProducto;

            $detalleItems[] = [
                'id' => (int) ($item['id'] ?? 0),
                'nombre' => (string) ($item['nombre'] ?? ''),
                'color' => (string) ($item['color'] ?? ''),
                'talla' => (string) ($item['talla'] ?? ''),
                'cantidad' => $cantidad,
                'precio' => $precio,
            ];
        }

        $total = $subtotal + $costoEnvio;

        $numeroOrden = $this->generarNumeroOrden();

        $pdo = Database::connect();

        $clienteData = [
            'nombre' => $nombre,
            'apellidos' => $apellidos,
            'email' => $email,
            'telefono' => $telefono,
            'direccion' => $direccion,
            'distrito' => $distritoNombre !== '' ? $distritoNombre : $distritoCodigo,
            'referencia' => $referencia,
            'password' => null,
        ];

        $idCliente = ClienteModel::obtenerOcrear($clienteData);

        try {
            if (!$pdo->inTransaction()) {
                $pdo->beginTransaction();
            }

            $ordenId = OrdenModel::crear([
                ':id_cliente' => $idCliente,
                ':nro_orden' => $numeroOrden,
                ':nombre' => $nombre,
                ':apellidos' => $apellidos,
                ':email' => $email,
                ':telefono' => $telefono,
                ':dni' => $dni,
                ':direccion' => $direccion,
                ':distrito' => $distritoNombre !== '' ? $distritoNombre : $distritoCodigo,
                ':referencia' => $referencia,
                ':metodo_envio' => $metodoEnvio,
                ':metodo_envio_texto' => $metodoEnvioTexto,
                ':costo_envio' => round($costoEnvio, 2),
                ':metodo_pago' => $metodoPagoTitulo,
                ':subtotal' => round($subtotal, 2),
                ':total' => round($total, 2),
            ]);

            OrdenDetalleModel::crear($ordenId, $detalleItems);

            $pdo->commit();
        } catch (\Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            echo "<script>alert('Ocurrió un problema al guardar tu orden. Inténtalo nuevamente.'); window.history.back();</script>";
            exit;
        }

        // Guardar información del cliente en sesión
        $_SESSION['id_cliente'] = $idCliente;
        $_SESSION['email_cliente'] = $email;
        $_SESSION['nombre_cliente'] = $nombre;

        clear_cart_session();
        unset($_SESSION['checkout'], $_SESSION['orden_guardada'], $_SESSION['ultima_orden'], $_SESSION['orden_numero']);

        header('Location: ' . base_url('ver_orden?nro=' . urlencode($numeroOrden)));
        exit;
    }

    public function ver_orden(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $numeroOrden = isset($_GET['nro']) ? trim((string) $_GET['nro']) : '';

        if ($numeroOrden === '') {
            header('Location: ' . base_url('checkout'));
            exit;
        }

        $orden = OrdenModel::obtenerPorNumero($numeroOrden);

        if ($orden === null) {
            http_response_code(404);
            $this->render('ver_orden', [
                'orden' => null,
                'items' => [],
                'mensajeError' => 'Orden no encontrada.',
            ]);
            return;
        }

        $fecha = $orden['fecha'] ?? '';
        $fechaFormateada = '';

        if ($fecha !== '') {
            try {
                $fechaObjeto = new \DateTime($fecha);
                $fechaObjeto->setTimezone(new \DateTimeZone('America/Lima'));
                $fechaFormateada = $fechaObjeto->format('d/m/Y H:i');
            } catch (\Exception $exception) {
                $fechaFormateada = date('d/m/Y H:i', strtotime($fecha));
            }
        }

        $detalles = OrdenDetalleModel::obtenerPorOrden((int) ($orden['id'] ?? 0));

        $items = [];

        foreach ($detalles as $detalle) {
            $items[] = [
                'id' => (int) ($detalle['producto_id'] ?? 0),
                'nombre' => (string) ($detalle['nombre_producto'] ?? ''),
                'cantidad' => (int) ($detalle['cantidad'] ?? 0),
                'precio' => (float) ($detalle['precio_unitario'] ?? 0),
                'subtotal' => (float) ($detalle['subtotal'] ?? 0),
                'color' => (string) ($detalle['color'] ?? ''),
                'talla' => (string) ($detalle['talla'] ?? ''),
            ];
        }

        $ordenPreparada = [
            'numero' => (string) ($orden['nro_orden'] ?? ''),
            'fecha' => $fechaFormateada,
            'estado' => (string) ($orden['estado'] ?? 'Pendiente'),
            'metodo_envio' => (string) ($orden['metodo_envio'] ?? ''),
            'metodo_envio_texto' => (string) ($orden['metodo_envio_texto'] ?? ''),
            'metodo_pago' => (string) ($orden['metodo_pago'] ?? ''),
            'costo_envio' => (float) ($orden['costo_envio'] ?? 0),
            'subtotal' => (float) ($orden['subtotal'] ?? 0),
            'total' => (float) ($orden['total'] ?? 0),
            'direccion' => (string) ($orden['direccion'] ?? ''),
            'distrito' => (string) ($orden['distrito'] ?? ''),
            'referencia' => (string) ($orden['referencia'] ?? ''),
            'cliente' => [
                'nombre' => (string) ($orden['nombre'] ?? ''),
                'apellidos' => (string) ($orden['apellidos'] ?? ''),
                'dni' => (string) ($orden['dni'] ?? ''),
                'telefono' => (string) ($orden['telefono'] ?? ''),
                'email' => (string) ($orden['email'] ?? ''),
                'distrito' => (string) ($orden['distrito'] ?? ''),
                'distrito_nombre' => (string) ($orden['distrito'] ?? ''),
                'direccion' => (string) ($orden['direccion'] ?? ''),
                'referencia' => (string) ($orden['referencia'] ?? ''),
            ],
            'totales' => [
                'subtotal' => (float) ($orden['subtotal'] ?? 0),
                'costo_envio' => (float) ($orden['costo_envio'] ?? 0),
                'total' => (float) ($orden['total'] ?? 0),
            ],
        ];

        $this->render('ver_orden', [
            'orden' => $ordenPreparada,
            'items' => $items,
            'mensajeError' => null,
        ]);
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

    private function generarNumeroOrden(): string
    {
        try {
            return 'NOV-' . strtoupper(substr(bin2hex(random_bytes(6)), 0, 8));
        } catch (\Throwable $exception) {
            return 'NOV-' . strtoupper(substr(md5((string) microtime(true)), 0, 8));
        }
    }
}
