<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationModel extends Model
{
    protected $table = 'locations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name', 'type', 'address', 'latitude', 'longitude'
    ];

    // Timestamps handled by SQLite DEFAULT
    protected $useTimestamps = false;

    /**
     * Get all locations
     */
    public function getAllLocations()
    {
        return $this->findAll();
    }

    /**
     * Get locations by type
     */
    public function getLocationsByType($type)
    {
        return $this->where('type', $type)->findAll();
    }
}
