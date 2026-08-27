<?php

namespace App\Models;

use CodeIgniter\Model;

class DriverModel extends Model
{
    protected $table = 'drivers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name', 'phone', 'license_number', 'license_type', 'is_active'
    ];

    // Timestamps handled by SQLite DEFAULT
    protected $useTimestamps = false;

    /**
     * Get all active drivers
     */
    public function getActiveDrivers()
    {
        return $this->where('is_active', 1)->findAll();
    }

    /**
     * Get driver by ID
     */
    public function getDriverById($id)
    {
        return $this->find($id);
    }

    /**
     * Count active drivers
     */
    public function countActive()
    {
        return $this->where('is_active', 1)->countAllResults();
    }
}
