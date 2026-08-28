<?php

namespace App\Controllers;

use App\Models\BookingModel;
use App\Models\BookingApprovalModel;
use App\Models\VehicleModel;
use App\Models\ApplicationLogModel;

class Approval extends BaseController
{
    protected $bookingModel;
    protected $approvalModel;
    protected $vehicleModel;
    protected $logModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->approvalModel = new BookingApprovalModel();
        $this->vehicleModel = new VehicleModel();
        $this->logModel = new ApplicationLogModel();
    }

    /**
     * Show pending approvals based on user role
     */
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $userId = session()->get('user_id');
        $role = session()->get('role');

        if ($role === 'approver_level1') {
            $pendingBookings = $this->bookingModel->getPendingBookingsForLevel1($userId);
            $level = 1;
        } elseif ($role === 'approver_level2') {
            $pendingBookings = $this->bookingModel->getPendingBookingsForLevel2($userId);
            $level = 2;
        } else {
            // Admin can see all pending with details
            $pendingBookings = $this->bookingModel->getBookingsWithDetails();
            $pendingBookings = array_filter($pendingBookings, fn($b) => $b->status === 'pending');
            $level = 0;
        }

        $data = [
            'title' => 'Persetujuan Pemesanan - Sistem Pemesanan Kendaraan',
            'pendingBookings' => $pendingBookings,
            'approvalLevel' => $level
        ];

        return view('approval/index', $data);
    }

    /**
     * Show approval detail
     */
    public function detail($id)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $booking = $this->bookingModel->getBookingById($id);

        if (!$booking) {
            return redirect()->to('/approval')->with('error', 'Pemesanan tidak ditemukan');
        }

        $approvals = $this->approvalModel->getApprovalsByBooking($id);

        $data = [
            'title' => 'Detail Persetujuan - Sistem Pemesanan Kendaraan',
            'booking' => $booking,
            'approvals' => $approvals
        ];

        return view('approval/detail', $data);
    }

    /**
     * Process approval
     */
    public function process($bookingId)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $userId = session()->get('user_id');
        $role = session()->get('role');
        $action = $this->request->getPost('action');
        $notes = $this->request->getPost('notes');

        // Determine approval level based on role
        $level = ($role === 'approver_level1') ? 1 : 2;

        // Get the approval record
        $approval = $this->approvalModel->getApprovalByBookingAndLevel($bookingId, $level);

        if (!$approval) {
            return redirect()->to('/approval')->with('error', 'Data persetujuan tidak ditemukan');
        }

        // Check if this approver has permission
        if ($approval->approver_id != $userId) {
            return redirect()->to('/approval')->with('error', 'Anda tidak memiliki akses untuk menyetujui pemesanan ini');
        }

        // Check if already processed
        if ($approval->status !== 'pending') {
            return redirect()->to('/approval')->with('error', 'Persetujuan sudah diproses');
        }

        // Process approval
        if ($action === 'approve') {
            $this->approvalModel->updateApprovalStatus($approval->id, 'approved', $notes);

            // Sequential: L1 approve -> status approved_level1, L2 approve -> status approved_level2
            if ($level === 1) {
                $this->bookingModel->update($bookingId, ['status' => 'approved_level1']);

                $this->logModel->log(
                    $userId,
                    'APPROVE_BOOKING_L1',
                    'Menyetujui pemesanan level 1'
                );
            } elseif ($level === 2) {
                $this->bookingModel->update($bookingId, ['status' => 'approved_level2']);

                $this->logModel->log(
                    $userId,
                    'APPROVE_BOOKING_L2',
                    'Menyetujui pemesanan level 2 - Pemesanan disetujui penuh'
                );
            }

            return redirect()->to('/approval')->with('success', 'Pemesanan berhasil disetujui');
            
        } elseif ($action === 'reject') {
            $this->approvalModel->updateApprovalStatus($approval->id, 'rejected', $notes);

            // Update booking status to rejected
            $this->bookingModel->update($bookingId, [
                'status' => 'rejected',
                'rejection_reason' => $notes
            ]);

            // Get booking to update vehicle status
            $booking = $this->bookingModel->find($bookingId);
            if ($booking) {
                $this->vehicleModel->updateStatus($booking->vehicle_id, 'tersedia');
            }

            $this->logModel->log(
                $userId,
                'REJECT_BOOKING',
                'Menolak pemesanan dengan alasan: ' . $notes
            );

            return redirect()->to('/approval')->with('success', 'Pemesanan berhasil ditolak');
        }

        return redirect()->to('/approval')->with('error', 'Aksi tidak valid');
    }
}
