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

        error_log("🔍 BuscarController → término recibido: [$termino]");

        // Si no hay término, renderiza la vista vacía directamente
        if ($termino === '') {
            $this->render('buscar', [
                'query' => '',
                'resultados' => [],
            ]);
            return;
        }

        $productoModel = new ProductoModel();
        $resultados = [];

        try {
            // Búsqueda con coincidencias parciales (mayús/minús indiferente)
            $resultados = $productoModel->buscarProductos($termino);
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
        ]);
    }
}
