<?php

namespace App\Controllers;

use App\Models\BookingModel;
use App\Models\BookingApprovalModel;
use App\Models\VehicleModel;
use App\Models\DriverModel;
use App\Models\UserModel;
use App\Models\ApplicationLogModel;

class Booking extends BaseController
{
    protected $bookingModel;
    protected $approvalModel;
    protected $vehicleModel;
    protected $driverModel;
    protected $userModel;
    protected $logModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->approvalModel = new BookingApprovalModel();
        $this->vehicleModel = new VehicleModel();
        $this->driverModel = new DriverModel();
        $this->userModel = new UserModel();
        $this->logModel = new ApplicationLogModel();
    }

    /**
     * Show booking list
     */
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Hanya admin yang bisa melihat daftar pemesanan');
        }

        $bookings = $this->bookingModel->getBookingsWithDetails();

        $data = [
            'title' => 'Daftar Pemesanan - Sistem Pemesanan Kendaraan',
            'bookings' => $bookings
        ];

        return view('booking/index', $data);
    }

    /**
     * Show create booking form
     */
    public function create()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Hanya admin yang bisa membuat pemesanan');
        }

        $vehicles = $this->vehicleModel->getAvailableVehicles();
        $drivers = $this->driverModel->getActiveDrivers();
        $approvers = $this->userModel->getApprovers();

        $data = [
            'title' => 'Pemesanan Baru - Sistem Pemesanan Kendaraan',
            'vehicles' => $vehicles,
            'drivers' => $drivers,
            'approvers' => $approvers
        ];

        return view('booking/create', $data);
    }

    /**
     * Store new booking
     */
    public function store()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Hanya admin yang bisa membuat pemesanan');
        }

        // Validate input
        $rules = [
            'vehicle_id' => 'required|integer',
            'driver_id' => 'required|integer',
            'approver_level1' => 'required|integer',
            'approver_level2' => 'required|integer',
            'purpose' => 'required|min_length[5]',
            'origin' => 'required|min_length[3]',
            'destination' => 'required|min_length[3]',
            'departure_date' => 'required|valid_date',
            'departure_time' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get form data
        $vehicleId = $this->request->getPost('vehicle_id');
        $driverId = $this->request->getPost('driver_id');
        $approverLevel1Id = $this->request->getPost('approver_level1');
        $approverLevel2Id = $this->request->getPost('approver_level2');

        // Check if vehicle is available
        $vehicle = $this->vehicleModel->find($vehicleId);
        if ($vehicle->status !== 'tersedia') {
            return redirect()->back()->withInput()->with('error', 'Kendaraan tidak tersedia');
        }

        // Check if approvers are different
        if ($approverLevel1Id == $approverLevel2Id) {
            return redirect()->back()->withInput()->with('error', 'Persetujuan level 1 dan 2 harus dari orang yang berbeda');
        }

        // Create booking
        $bookingData = [
            'booking_code' => $this->bookingModel->generateBookingCode(),
            'vehicle_id' => $vehicleId,
            'driver_id' => $driverId,
            'requester_id' => session()->get('user_id'),
            'purpose' => $this->request->getPost('purpose'),
            'origin' => $this->request->getPost('origin'),
            'destination' => $this->request->getPost('destination'),
            'departure_date' => $this->request->getPost('departure_date'),
            'departure_time' => $this->request->getPost('departure_time'),
            'estimated_return_date' => $this->request->getPost('estimated_return_date') ?: null,
            'estimated_return_time' => $this->request->getPost('estimated_return_time') ?: null,
            'status' => 'pending'
        ];

        $bookingId = $this->bookingModel->insert($bookingData);

        if ($bookingId) {
            // Create approval records (2 levels)
            $this->approvalModel->createInitialApprovals($bookingId, $approverLevel1Id, $approverLevel2Id);

            // Update vehicle status
            $this->vehicleModel->updateStatus($vehicleId, 'dalam_perjalanan');

            // Log the action
            $this->logModel->log(
                session()->get('user_id'),
                'CREATE_BOOKING',
                'Membuat pemesanan ' . $bookingData['booking_code'] . ' untuk kendaraan ' . $vehicle->plate_number
            );

            return redirect()->to('/booking')->with('success', 'Pemesanan berhasil dibuat dengan kode: ' . $bookingData['booking_code']);
        }

        return redirect()->back()->withInput()->with('error', 'Gagal membuat pemesanan');
    }

    /**
     * Show booking detail
     */
    public function detail($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Hanya admin yang bisa melihat detail pemesanan');
        }

        $booking = $this->bookingModel->getBookingById($id);

        if (!$booking) {
            return redirect()->to('/booking')->with('error', 'Pemesanan tidak ditemukan');
        }

        $approvals = $this->approvalModel->getApprovalsByBooking($id);

        $data = [
            'title' => 'Detail Pemesanan - Sistem Pemesanan Kendaraan',
            'booking' => $booking,
            'approvals' => $approvals
        ];

        return view('booking/detail', $data);
    }

    /**
     * Update booking status (for admin)
     */
    public function updateStatus($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        if (session()->get('role') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'Hanya admin yang bisa mengubah status pemesanan');
        }

        $status = $this->request->getPost('status');
        $rejectionReason = $this->request->getPost('rejection_reason');

        $booking = $this->bookingModel->find($id);

        if (!$booking) {
            return redirect()->to('/booking')->with('error', 'Pemesanan tidak ditemukan');
        }

        // Validasi status
        $validStatuses = ['pending', 'approved_level1', 'approved_level2', 'rejected', 'in_progress', 'completed', 'cancelled'];
        if (empty($status) || !in_array($status, $validStatuses)) {
            return redirect()->back()->with('error', 'Status tidak valid');
        }

        $updateData = ['status' => $status];

        if ($status === 'rejected' && $rejectionReason) {
            $updateData['rejection_reason'] = $rejectionReason;
        }

        if ($status === 'completed') {
            $updateData['return_date'] = date('Y-m-d');
            $updateData['return_time'] = date('H:i:s');
            $this->vehicleModel->updateStatus($booking->vehicle_id, 'tersedia');
        }

        if ($status === 'cancelled') {
            $this->vehicleModel->updateStatus($booking->vehicle_id, 'tersedia');
        }

        $this->bookingModel->update($id, $updateData);

        // Log the action
        $this->logModel->log(
            session()->get('user_id'),
            'UPDATE_BOOKING_STATUS',
            'Mengubah status pemesanan ' . $booking->booking_code . ' menjadi ' . $status
        );

        return redirect()->to('/booking/detail/' . $id)->with('success', 'Status pemesanan berhasil diupdate');
    }
}
