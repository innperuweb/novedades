<?php

require_once APP_PATH . '/controllers/BaseController.php';
require_once APP_PATH . '/models/ProductoModel.php';
require_once APP_PATH . '/helpers/security_helper.php';

class BuscarController extends BaseController
{
    public function index(): void
    {
        // Captura y sanea el término de búsqueda
        $termino = isset($_GET['q']) ? clean_string((string) $_GET['q']) : '';
        $termino = trim($termino);

        $orden = isset($_GET['order']) ? sanitize_string((string) $_GET['order']) : '';
        $ordenesPermitidos = ['precio_asc', 'precio_desc', 'nombre_asc', 'nombre_desc'];
        if (!in_array($orden, $ordenesPermitidos, true)) {
            $orden = '';
        }

        error_log("🔍 BuscarController → término recibido: [$termino]");

        // Si no hay término, renderiza la vista vacía directamente
        if ($termino === '') {
            $this->render('buscar', [
                'query' => '',
                'resultados' => [],
                'orden' => $orden,
                'min_precio' => 0.0,
                'max_precio' => 10000.0,
            ]);
            return;
        }

        $productoModel = new ProductoModel();
        $resultados = [];
        $minPrecio = isset($_POST['min_precio']) ? (float) $_POST['min_precio'] : (isset($_GET['min_precio']) ? (float) $_GET['min_precio'] : 0.0);
        $maxPrecio = isset($_POST['max_precio']) ? (float) $_POST['max_precio'] : (isset($_GET['max_precio']) ? (float) $_GET['max_precio'] : 10000.0);

        if ($minPrecio > $maxPrecio) {
            [$minPrecio, $maxPrecio] = [$maxPrecio, $minPrecio];
        }

        try {
            // Búsqueda con coincidencias parciales (mayús/minús indiferente)
            $resultados = $productoModel->listarPorRangoPrecio($minPrecio, $maxPrecio, $orden, $termino);
            if (!is_array($resultados)) {
                $resultados = [];
            }
        } catch (\Throwable $e) {
            $resultados = [];
        }

        // Renderizar vista con datos
        $this->render('buscar', [
            'query' => $termino,
            'resultados' => $resultados,
            'orden' => $orden,
            'min_precio' => $minPrecio,
            'max_precio' => $maxPrecio,
        ]);
    }
}
