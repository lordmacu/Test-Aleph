<?php

namespace App\Controllers;

use App\Libraries\AlephAPI;
use App\Models\CategoryModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controller for listing CMDB categories from Aleph API.
 */
class CategoriaController extends BaseController
{
    /**
     * Display all categories fetched from db.
     *
     * @return \CodeIgniter\HTTP\ResponseInterface|string
     */
    public function index()
    {
        $categoryModel = new CategoryModel();

        $categories = $categoryModel->findAll();

        return view('categories/index', [
            'categories' => $categories,
        ]);
    }

    /**
     * AJAX endpoint to import categories from the external API.
     *
     * @return ResponseInterface
     */
    public function importarCategoriasAjax(): ResponseInterface
    {
        try {
            $api = new AlephAPI();
            $categoryModel = new CategoryModel();

            // 1. Fetch categories from the external API
            $apiResponse = $api->getCategorias();
            if (!isset($apiResponse['categorias']) || empty($apiResponse['categorias'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'No se encontraron categorías en la API o hubo un error de conexión.'
                ])->setStatusCode(404);
            }

            $importedCount = 0;
            $skippedCount = 0;

            // 2. Loop and insert into the local database
            foreach ($apiResponse['categorias'] as $cat) {
                $data = ['nombre' => $cat['nombre']];

                // Use the model to insert only if it doesn't exist
                if ($categoryModel->insertIfNotExists($data)) {
                    $importedCount++;
                } else {
                    $skippedCount++; // Should be 0 if logic is correct, but good for tracking
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => "Importación completada. Se procesaron {$importedCount} categorías."
            ]);
        } catch (\Throwable $e) {
            // Catch any unexpected error
            log_message('error', '[CategoriasController] ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Ocurrió un error inesperado en el servidor.'
            ])->setStatusCode(500);
        }
    }
}
