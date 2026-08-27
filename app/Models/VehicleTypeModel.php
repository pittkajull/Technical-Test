<?php

namespace App\Models;

use CodeIgniter\Model;

class VehicleTypeModel extends Model
{
    protected $table = 'vehicle_types';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name', 'description'
    ];

    // Dates
    protected $useTimestamps = false;

    /**
     * Get all vehicle types
     */
    public function getAllTypes()
    {
        return $this->findAll();
    }
}
