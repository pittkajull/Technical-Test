<?php

namespace App\Models;

use CodeIgniter\Model;

class FuelLogModel extends Model
{
    protected $table = 'fuel_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'vehicle_id', 'fuel_date', 'fuel_type', 'liters', 'total_cost',
        'mileage_at_fuel', 'station', 'notes', 'created_by'
    ];

    // Timestamps handled by SQLite DEFAULT
    protected $useTimestamps = false;

    /**
     * Get fuel logs with vehicle info
     */
    public function getFuelLogsWithDetails()
    {
        return $this->select('fuel_logs.*, vehicles.plate_number, vehicles.brand, vehicles.model')
                    ->join('vehicles', 'vehicles.id = fuel_logs.vehicle_id', 'left')
                    ->orderBy('fuel_logs.fuel_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get fuel logs by vehicle
     */
    public function getFuelLogsByVehicle($vehicleId)
    {
        return $this->where('vehicle_id', $vehicleId)
                    ->orderBy('fuel_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get total fuel cost by month
     */
    public function getTotalFuelCostByMonth($year = null)
    {
        $year = $year ?? date('Y');
        
        return $this->select("strftime('%m', fuel_date) as month, SUM(total_cost) as total_cost, SUM(liters) as total_liters")
                    ->where("strftime('%Y', fuel_date)", $year)
                    ->groupBy("strftime('%m', fuel_date)")
                    ->findAll();
    }

    /**
     * Get total fuel cost by vehicle
     */
    public function getTotalFuelCostByVehicle()
    {
        return $this->select('vehicles.plate_number, SUM(fuel_logs.total_cost) as total_cost, SUM(fuel_logs.liters) as total_liters')
                    ->join('vehicles', 'vehicles.id = fuel_logs.vehicle_id')
                    ->groupBy('vehicles.id')
                    ->findAll();
    }
}
