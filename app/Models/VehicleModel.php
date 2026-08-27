<?php

namespace App\Models;

use CodeIgniter\Model;

class VehicleModel extends Model
{
    protected $table = 'vehicles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'plate_number', 'vehicle_type_id', 'brand', 'model', 'year', 'color',
        'ownership', 'rental_company', 'fuel_type', 'status', 'current_mileage', 'location_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all vehicles with type and location info
     */
    public function getVehiclesWithDetails()
    {
        return $this->select('vehicles.*, vehicle_types.name as type_name, locations.name as location_name')
                    ->join('vehicle_types', 'vehicle_types.id = vehicles.vehicle_type_id', 'left')
                    ->join('locations', 'locations.id = vehicles.location_id', 'left')
                    ->findAll();
    }

    /**
     * Get available vehicles
     */
    public function getAvailableVehicles()
    {
        return $this->select('vehicles.*, vehicle_types.name as type_name, locations.name as location_name')
                    ->join('vehicle_types', 'vehicle_types.id = vehicles.vehicle_type_id', 'left')
                    ->join('locations', 'locations.id = vehicles.location_id', 'left')
                    ->where('vehicles.status', 'tersedia')
                    ->findAll();
    }

    /**
     * Get vehicle by ID with details
     */
    public function getVehicleById($id)
    {
        return $this->select('vehicles.*, vehicle_types.name as type_name, locations.name as location_name')
                    ->join('vehicle_types', 'vehicle_types.id = vehicles.vehicle_type_id', 'left')
                    ->join('locations', 'locations.id = vehicles.location_id', 'left')
                    ->where('vehicles.id', $id)
                    ->first();
    }

    /**
     * Count vehicles by status
     */
    public function countByStatus()
    {
        return $this->select('status, COUNT(*) as total')
                    ->groupBy('status')
                    ->findAll();
    }

    /**
     * Count vehicles by ownership
     */
    public function countByOwnership()
    {
        return $this->select('ownership, COUNT(*) as total')
                    ->groupBy('ownership')
                    ->findAll();
    }

    /**
     * Count vehicles by type
     */
    public function countByType()
    {
        return $this->select('vehicle_types.name as type_name, COUNT(*) as total')
                    ->join('vehicle_types', 'vehicle_types.id = vehicles.vehicle_type_id')
                    ->groupBy('vehicle_types.id')
                    ->findAll();
    }

    /**
     * Update vehicle status
     */
    public function updateStatus($vehicleId, $status)
    {
        return $this->update($vehicleId, ['status' => $status]);
    }

    /**
     * Update vehicle mileage
     */
    public function updateMileage($vehicleId, $mileage)
    {
        return $this->update($vehicleId, ['current_mileage' => $mileage]);
    }
}
