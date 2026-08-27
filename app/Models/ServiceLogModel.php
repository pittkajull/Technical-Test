<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiceLogModel extends Model
{
    protected $table = 'service_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'vehicle_id', 'service_type', 'service_date', 'next_service_date',
        'next_service_mileage', 'description', 'cost', 'workshop',
        'mileage_at_service', 'status', 'created_by'
    ];

    // Timestamps handled by SQLite DEFAULT
    protected $useTimestamps = false;

    /**
     * Get service logs with vehicle info
     */
    public function getServiceLogsWithDetails()
    {
        return $this->select('service_logs.*, vehicles.plate_number, vehicles.brand, vehicles.model')
                    ->join('vehicles', 'vehicles.id = service_logs.vehicle_id', 'left')
                    ->orderBy('service_logs.service_date', 'DESC')
                    ->findAll();
    }

    /**
     * Get upcoming services
     */
    public function getUpcomingServices()
    {
        return $this->select('service_logs.*, vehicles.plate_number, vehicles.brand, vehicles.model')
                    ->join('vehicles', 'vehicles.id = service_logs.vehicle_id', 'left')
                    ->where('service_logs.status', 'scheduled')
                    ->orderBy('service_logs.next_service_date', 'ASC')
                    ->findAll();
    }
}
