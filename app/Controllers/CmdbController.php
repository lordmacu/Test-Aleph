<?php

namespace App\Controllers;

use App\Libraries\AlephAPI;
use App\Services\CmdbService; // Import the service


/**
 * Controller to manage CMDB records by category.
 */
class CmdbController extends BaseController
{
    protected CmdbService $cmdbService;
    protected AlephAPI $api;

    public function __construct()
    {
        $this->cmdbService = new CmdbService();
        $this->api = new AlephAPI();
    }

    /**
     * Display CMDB records for a given category.
     */
    public function index(int $categoriaId)
    {
        $response = $this->api->getCmdbByCategoria($categoriaId);

        return view('cmdb/index', [
            'registros'    => $response['cmdb'] ?? [],
            'categoria_id' => $categoriaId,
        ]);
    }

    /**
     * Handle the export request.
     */
    public function exportar(int $categoria_id)
    {
        try {
            $exportData = $this->cmdbService->exportRecords($categoria_id);

            return view('cmdb/export_success', [
                'file_name'    => $exportData['file_name'],
                'file_url'     => $exportData['file_url'],
                'categoria_id' => $categoria_id,
            ]);
        } catch (\Throwable $e) {
            // Log the error: log_message('error', $e->getMessage());
            return redirect()->to('cmdb/' . $categoria_id)
                ->with('error', 'Ocurrió un error inesperado al generar el archivo.');
        }
    }

    /**
     * Display import form.
     */
    public function importarVista(int $categoria_id)
    {
        // This view can now be a simple modal in the index page
        return view('cmdb/import_form', [
            'categoria_id' => $categoria_id,
        ]);
    }

    /**
     * Handle the import request.
     */
    public function importar()
    {
        $categoria_id = $this->request->getPost('categoria_id');
        $file = $this->request->getFile('archivo');

        // Basic validation in the controller
        $rules = [
            'archivo' => 'uploaded[archivo]|max_size[archivo,5120]|ext_in[archivo,csv,xls,xlsx]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $result = $this->cmdbService->importRecords($file, (int)$categoria_id);

        return view('cmdb/import_result', [
            'insertados'   => $result['inserted'],
            'errores'      => $result['errors'],
            'categoria_id' => $categoria_id,
        ]);
    }
}
