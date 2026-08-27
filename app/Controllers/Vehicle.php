<?php

namespace App\Controllers;

use App\Models\VehicleModel;
use App\Models\VehicleTypeModel;
use App\Models\LocationModel;
use App\Models\FuelLogModel;
use App\Models\ServiceLogModel;
use App\Models\ApplicationLogModel;

class Vehicle extends BaseController
{
    protected $vehicleModel;
    protected $vehicleTypeModel;
    protected $locationModel;
    protected $fuelLogModel;
    protected $serviceLogModel;
    protected $logModel;

    public function __construct()
    {
        $this->vehicleModel = new VehicleModel();
        $this->vehicleTypeModel = new VehicleTypeModel();
        $this->locationModel = new LocationModel();
        $this->fuelLogModel = new FuelLogModel();
        $this->serviceLogModel = new ServiceLogModel();
        $this->logModel = new ApplicationLogModel();
    }

    /**
     * Show vehicle list
     */
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $vehicles = $this->vehicleModel->getVehiclesWithDetails();

        $data = [
            'title' => 'Daftar Kendaraan - Sistem Pemesanan Kendaraan',
            'vehicles' => $vehicles
        ];

        return view('vehicle/index', $data);
    }

    /**
     * Show vehicle detail
     */
    public function detail($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $vehicle = $this->vehicleModel->getVehicleById($id);

        if (!$vehicle) {
            return redirect()->to('/vehicle')->with('error', 'Kendaraan tidak ditemukan');
        }

        $fuelLogs = $this->fuelLogModel->getFuelLogsByVehicle($id);
        $serviceLogs = $this->serviceLogModel->getServiceLogsWithDetails();

        $data = [
            'title' => 'Detail Kendaraan - Sistem Pemesanan Kendaraan',
            'vehicle' => $vehicle,
            'fuelLogs' => $fuelLogs,
            'serviceLogs' => $serviceLogs
        ];

        return view('vehicle/detail', $data);
    }

    /**
     * Add fuel log
     */
    public function addFuelLog()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $vehicleId = $this->request->getPost('vehicle_id');

        $rules = [
            'fuel_date' => 'required|valid_date',
            'fuel_type' => 'required',
            'liters' => 'required|decimal|greater_than[0]',
            'total_cost' => 'required|decimal|greater_than[0]',
            'station' => 'required|min_length[3]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fuelData = [
            'vehicle_id' => $vehicleId,
            'fuel_date' => $this->request->getPost('fuel_date'),
            'fuel_type' => $this->request->getPost('fuel_type'),
            'liters' => $this->request->getPost('liters'),
            'total_cost' => $this->request->getPost('total_cost'),
            'mileage_at_fuel' => $this->request->getPost('mileage_at_fuel') ?: null,
            'station' => $this->request->getPost('station'),
            'notes' => $this->request->getPost('notes') ?: null,
            'created_by' => session()->get('user_id')
        ];

        $this->fuelLogModel->insert($fuelData);

        // Update vehicle mileage if provided
        if ($fuelData['mileage_at_fuel']) {
            $this->vehicleModel->updateMileage($vehicleId, $fuelData['mileage_at_fuel']);
        }

        // Log the action
        $this->logModel->log(
            session()->get('user_id'),
            'ADD_FUEL_LOG',
            'Menambah log BBM untuk kendaraan ID: ' . $vehicleId
        );

        return redirect()->to('/vehicle/detail/' . $vehicleId)->with('success', 'Log BBM berhasil ditambahkan');
    }

    /**
     * Add service log
     */
    public function addServiceLog()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $vehicleId = $this->request->getPost('vehicle_id');

        $rules = [
            'service_type' => 'required',
            'service_date' => 'required|valid_date',
            'description' => 'required|min_length[5]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $serviceData = [
            'vehicle_id' => $vehicleId,
            'service_type' => $this->request->getPost('service_type'),
            'service_date' => $this->request->getPost('service_date'),
            'next_service_date' => $this->request->getPost('next_service_date') ?: null,
            'next_service_mileage' => $this->request->getPost('next_service_mileage') ?: null,
            'description' => $this->request->getPost('description'),
            'cost' => $this->request->getPost('cost') ?: 0,
            'workshop' => $this->request->getPost('workshop') ?: null,
            'mileage_at_service' => $this->request->getPost('mileage_at_service') ?: null,
            'status' => 'completed',
            'created_by' => session()->get('user_id')
        ];

        $this->serviceLogModel->insert($serviceData);

        // Log the action
        $this->logModel->log(
            session()->get('user_id'),
            'ADD_SERVICE_LOG',
            'Menambah log service untuk kendaraan ID: ' . $vehicleId
        );

        return redirect()->to('/vehicle/detail/' . $vehicleId)->with('success', 'Log service berhasil ditambahkan');
    }
}
