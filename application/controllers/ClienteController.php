<?php

require_once APP_PATH . '/controllers/BaseController.php';
require_once APP_PATH . '/models/ClienteModel.php';

class ClienteController extends BaseController
{
    public function index(): void
    {
        $this->render('para_el_cliente');
    }

    public function libro(): void
    {
        $this->render('libro_de_reclamaciones');
    }

    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            $cliente = ClienteModel::validarLogin(trim($email), (string) $password);

            if ($cliente !== null) {
                $_SESSION['id_cliente'] = (int) $cliente['id'];
                $_SESSION['email_cliente'] = $cliente['email'];
                $_SESSION['nombre_cliente'] = $cliente['nombre'];

                header('Location: ' . base_url('checkout'));
                exit;
            }

            $error = 'Correo o contraseña incorrectos';
        }

        $this->render('login', ['error' => $error]);
    }

    public function registro(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            if ($email === '') {
                $error = 'El correo electrónico es obligatorio.';
            } else {
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

                $distritoCodigo = trim($_POST['distrito'] ?? '');
                $distritoNombre = $distritos[$distritoCodigo] ?? $distritoCodigo;

                $data = [
                    'nombre' => trim($_POST['nombre'] ?? ''),
                    'apellidos' => trim($_POST['apellidos'] ?? ''),
                    'email' => $email,
                    'password' => password_hash((string) ($_POST['password'] ?? ''), PASSWORD_DEFAULT),
                    'telefono' => trim($_POST['telefono'] ?? ''),
                    'direccion' => trim($_POST['direccion'] ?? ''),
                    'distrito' => $distritoNombre,
                    'referencia' => trim($_POST['referencia'] ?? ''),
                ];

                $idCliente = ClienteModel::obtenerOcrear($data);

                $_SESSION['id_cliente'] = $idCliente;
                $_SESSION['email_cliente'] = $data['email'];
                $_SESSION['nombre_cliente'] = $data['nombre'];

                header('Location: ' . base_url('checkout'));
                exit;
            }
        }

        $this->render('registro', ['error' => $error]);
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();

        header('Location: ' . base_url('login'));
        exit;
    }
}
