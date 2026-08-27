<?php

namespace App\Controllers;

use App\Models\VehicleModel;
use App\Models\BookingModel;
use App\Models\DriverModel;
use App\Models\FuelLogModel;
use App\Models\UserModel;
use App\Models\ApplicationLogModel;

class Dashboard extends BaseController
{
    protected $vehicleModel;
    protected $bookingModel;
    protected $driverModel;
    protected $fuelLogModel;
    protected $userModel;
    protected $logModel;

    public function __construct()
    {
        $this->vehicleModel = new VehicleModel();
        $this->bookingModel = new BookingModel();
        $this->driverModel = new DriverModel();
        $this->fuelLogModel = new FuelLogModel();
        $this->userModel = new UserModel();
        $this->logModel = new ApplicationLogModel();
    }

    /**
     * Show dashboard
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        // Get statistics
        $totalVehicles = $this->vehicleModel->countAllResults();
        $availableVehicles = $this->vehicleModel->where('status', 'tersedia')->countAllResults();
        $totalDrivers = $this->driverModel->countActive();
        $totalBookings = $this->bookingModel->getTotalBookings();

        // Get booking statistics
        $bookingsByStatus = $this->bookingModel->countByStatus();
        $bookingsStats = [];
        foreach ($bookingsByStatus as $stat) {
            $bookingsStats[$stat->status] = $stat->total;
        }

        // Get vehicle statistics
        $vehiclesByStatus = $this->vehicleModel->countByStatus();
        $vehiclesStats = [];
        foreach ($vehiclesByStatus as $stat) {
            $vehiclesStats[$stat->status] = $stat->total;
        }

        // Get bookings by month for chart
        $bookingsByMonth = $this->bookingModel->getBookingStatsByMonth();
        $monthlyData = array_fill(0, 12, 0);
        foreach ($bookingsByMonth as $stat) {
            $monthlyData[$stat->month - 1] = $stat->total;
        }

        // Get bookings by vehicle type for chart
        $bookingsByType = $this->bookingModel->getBookingStatsByVehicleType();

        // Get fuel cost by month for chart
        $fuelCostByMonth = $this->fuelLogModel->getTotalFuelCostByMonth();
        $fuelCostData = array_fill(0, 12, 0);
        foreach ($fuelCostByMonth as $stat) {
            $fuelCostData[$stat->month - 1] = $stat->total_cost;
        }

        // Get recent bookings
        $recentBookings = $this->bookingModel->getBookingsWithDetails();
        $recentBookings = array_slice($recentBookings, 0, 5);

        // Get recent logs
        $recentLogs = $this->logModel->getLogsWithDetails(10);

        $data = [
            'title' => 'Dashboard - Sistem Pemesanan Kendaraan',
            'totalVehicles' => $totalVehicles,
            'availableVehicles' => $availableVehicles,
            'totalDrivers' => $totalDrivers,
            'totalBookings' => $totalBookings,
            'bookingsStats' => $bookingsStats,
            'vehiclesStats' => $vehiclesStats,
            'monthlyData' => $monthlyData,
            'bookingsByType' => $bookingsByType,
            'fuelCostData' => $fuelCostData,
            'recentBookings' => $recentBookings,
            'recentLogs' => $recentLogs
        ];

        return view('dashboard/index', $data);
    }

    /**
     * Get dashboard data as JSON (for AJAX)
     */
    public function getData()
    {
        $bookingsByMonth = $this->bookingModel->getBookingStatsByMonth();
        $bookingsByType = $this->bookingModel->getBookingStatsByVehicleType();
        $fuelCostByMonth = $this->fuelLogModel->getTotalFuelCostByMonth();

        return $this->response->setJSON([
            'bookings_by_month' => $bookingsByMonth,
            'bookings_by_type' => $bookingsByType,
            'fuel_cost_by_month' => $fuelCostByMonth
        ]);
    }
}
