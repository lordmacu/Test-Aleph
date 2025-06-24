<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;

/**
 * AlephAPI handles communication with the Aleph Manager API using an API Key.
 */
class AlephAPI
{
    /**
     * Aleph API Key (static for now).
     *
     * @var string
     */
    protected string $api_key = 'zkUMrLN8xKEtrCr4Y7hYfLw8k!utbb';

    /**
     * Base URL for the Aleph API.
     *
     * @var string
     */
    protected string $base_url = 'https://qa.alephmanager.com/API/';

    /**
     * HTTP client to make API requests.
     *
     * @var CURLRequest
     */
    protected CURLRequest $client;

    /**
     * Initialize the CURL client.
     */
    public function __construct()
    {
        $this->client = \Config\Services::curlrequest();
    }

    /**
     * Send a POST request to a specific Aleph API endpoint with authentication.
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    protected function post(string $endpoint, array $data = []): array
    {
        $data['api_key'] = $this->api_key;

        try {
            $response = $this->client->post($this->base_url . $endpoint, [
                'form_params' => $data,
            ]);

            return json_decode($response->getBody(), true) ?? [];
        } catch (\Throwable $e) {
            // You could log this in production
            return ['error' => 'API connection failed: ' . $e->getMessage()];
        }
    }

    /**
     * Get all available categories from Aleph.
     *
     * @return array
     */
    public function getCategorias(): array
    {
        return $this->post('get_categorias');
    }

    /**
     * Get all CMDB records for a given category ID.
     *
     * @param int $categoria_id
     * @return array
     */
    public function getCmdbByCategoria(int $categoria_id): array
    {
        return $this->post('get_cmdb', ['categoria_id' => $categoria_id]);
    }

    /**
     * Insert or update a CMDB record.
     *
     * @param array $data
     * @return array
     */
    public function insertOrUpdateCmdb(array $data): array
    {
        return $this->post('insert_cmdb', $data);
    }

    /**
     * Delete a CMDB record by its unique identifier.
     *
     * @param string $identificador
     * @return array
     */
    public function deleteCmdb(string $identificador): array
    {
        return $this->post('delete_cmdb', ['identificador' => $identificador]);
    }

    /**
     * Activate or deactivate all CMDB records in a given category.
     *
     * @param int $categoria_id
     * @param int $activado 0 = inactive, 1 = active
     * @return array
     */
    public function updateCmdbActivado(int $categoria_id, int $activado): array
    {
        return $this->post('update_activado_cmdb', [
            'categoria_id' => $categoria_id,
            'activado' => $activado,
        ]);
    }
}
