<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categorias';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nombre'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Inserts a category if it does not already exist based on its name.
     * This prevents creating duplicate categories.
     *
     * @param array $data Data containing the 'nombre' of the category.
     * @return bool True if inserted or already exists, false on error.
     */
    public function insertIfNotExists(array $data): bool
    {
        $existing = $this->where('nombre', $data['nombre'])->first();

        if (!$existing) {
            return $this->insert($data, false);
        }

        return true;
    }
}
