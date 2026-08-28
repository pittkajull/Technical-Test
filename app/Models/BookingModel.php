<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'booking_code', 'vehicle_id', 'driver_id', 'requester_id', 'purpose',
        'origin', 'destination', 'departure_date', 'departure_time',
        'return_date', 'return_time', 'estimated_return_date', 'estimated_return_time',
        'status', 'rejection_reason'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Generate unique booking code
     */
    public function generateBookingCode()
    {
        $date = date('Ymd');
        $lastBooking = $this->where('booking_code LIKE', "BK-{$date}-%")
                            ->orderBy('id', 'DESC')
                            ->first();
        
        if ($lastBooking) {
            $lastNumber = (int) substr($lastBooking->booking_code, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return "BK-{$date}-{$newNumber}";
    }

    /**
     * Get all bookings with details
     */
    public function getBookingsWithDetails()
    {
        return $this->select('bookings.*, 
                            vehicles.plate_number, 
                            vehicle_types.name as vehicle_type_name,
                            drivers.name as driver_name,
                            users.fullname as requester_name,
                            l1.fullname as approver_level1_name,
                            l2.fullname as approver_level2_name')
                    ->join('vehicles', 'vehicles.id = bookings.vehicle_id', 'left')
                    ->join('vehicle_types', 'vehicle_types.id = vehicles.vehicle_type_id', 'left')
                    ->join('drivers', 'drivers.id = bookings.driver_id', 'left')
                    ->join('users', 'users.id = bookings.requester_id', 'left')
                    ->join('booking_approvals ba1', 'ba1.booking_id = bookings.id AND ba1.approval_level = 1', 'left')
                    ->join('users l1', 'l1.id = ba1.approver_id', 'left')
                    ->join('booking_approvals ba2', 'ba2.booking_id = bookings.id AND ba2.approval_level = 2', 'left')
                    ->join('users l2', 'l2.id = ba2.approver_id', 'left')
                    ->orderBy('bookings.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get booking by ID with details
     */
    public function getBookingById($id)
    {
        return $this->select('bookings.*, 
                            vehicles.plate_number, 
                            vehicles.brand,
                            vehicles.model,
                            vehicle_types.name as vehicle_type_name,
                            drivers.name as driver_name,
                            drivers.phone as driver_phone,
                            users.fullname as requester_name')
                    ->join('vehicles', 'vehicles.id = bookings.vehicle_id', 'left')
                    ->join('vehicle_types', 'vehicle_types.id = vehicles.vehicle_type_id', 'left')
                    ->join('drivers', 'drivers.id = bookings.driver_id', 'left')
                    ->join('users', 'users.id = bookings.requester_id', 'left')
                    ->where('bookings.id', $id)
                    ->first();
    }

    /**
     * Get bookings by status
     */
    public function getBookingsByStatus($status)
    {
        return $this->where('status', $status)->findAll();
    }

    /**
     * Get pending bookings for approver level 1
     */
    public function getPendingBookingsForLevel1($approverId)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT bookings.*, 
                vehicles.plate_number, 
                vehicle_types.name as vehicle_type_name,
                drivers.name as driver_name,
                users.fullname as requester_name
                FROM bookings 
                JOIN booking_approvals ON booking_approvals.booking_id = bookings.id AND booking_approvals.approval_level = 1
                LEFT JOIN vehicles ON vehicles.id = bookings.vehicle_id
                LEFT JOIN vehicle_types ON vehicle_types.id = vehicles.vehicle_type_id
                LEFT JOIN drivers ON drivers.id = bookings.driver_id
                LEFT JOIN users ON users.id = bookings.requester_id
                WHERE bookings.status NOT IN ('rejected', 'cancelled', 'completed') 
                AND booking_approvals.approver_id = " . (int)$approverId . "
                AND booking_approvals.status = 'pending'";
        return $db->query($sql)->getResult();
    }

    /**
     * Get pending bookings for approver level 2
     * Shows bookings where L2 approval is still pending (regardless of booking status)
     */
    public function getPendingBookingsForLevel2($approverId)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT bookings.*, 
                vehicles.plate_number, 
                vehicle_types.name as vehicle_type_name,
                drivers.name as driver_name,
                users.fullname as requester_name
                FROM bookings 
                JOIN booking_approvals ON booking_approvals.booking_id = bookings.id AND booking_approvals.approval_level = 2
                LEFT JOIN vehicles ON vehicles.id = bookings.vehicle_id
                LEFT JOIN vehicle_types ON vehicle_types.id = vehicles.vehicle_type_id
                LEFT JOIN drivers ON drivers.id = bookings.driver_id
                LEFT JOIN users ON users.id = bookings.requester_id
                WHERE bookings.status NOT IN ('rejected', 'cancelled', 'completed') 
                AND booking_approvals.approver_id = " . (int)$approverId . "
                AND booking_approvals.status = 'pending'";
        return $db->query($sql)->getResult();
    }

    /**
     * Count bookings by status
     */
    public function countByStatus()
    {
        return $this->select('status, COUNT(*) as total')
                    ->groupBy('status')
                    ->findAll();
    }

    /**
     * Get booking statistics by month
     */
    public function getBookingStatsByMonth($year = null)
    {
        $year = $year ?? date('Y');
        
        return $this->select("strftime('%m', departure_date) as month, COUNT(*) as total")
                    ->where("strftime('%Y', departure_date)", $year)
                    ->groupBy("strftime('%m', departure_date)")
                    ->findAll();
    }

    /**
     * Get booking statistics by vehicle type
     */
    public function getBookingStatsByVehicleType()
    {
        return $this->select('vehicle_types.name as type_name, COUNT(*) as total')
                    ->join('vehicles', 'vehicles.id = bookings.vehicle_id')
                    ->join('vehicle_types', 'vehicle_types.id = vehicles.vehicle_type_id')
                    ->groupBy('vehicle_types.id')
                    ->findAll();
    }

    /**
     * Get bookings by date range (for reports)
     */
    public function getBookingsByDateRange($startDate, $endDate)
    {
        return $this->select("bookings.*, 
                            vehicles.plate_number, 
                            vehicles.brand,
                            vehicles.model,
                            vehicle_types.name as vehicle_type_name,
                            drivers.name as driver_name,
                            users.fullname as requester_name,
                            CASE 
                                WHEN bookings.status = 'completed' THEN 'Selesai'
                                WHEN bookings.status = 'pending' THEN 'Menunggu'
                                WHEN bookings.status = 'approved_level1' THEN 'Disetujui L1'
                                WHEN bookings.status = 'approved_level2' THEN 'Disetujui L2'
                                WHEN bookings.status = 'in_progress' THEN 'Berlangsung'
                                WHEN bookings.status = 'rejected' THEN 'Ditolak'
                                WHEN bookings.status = 'cancelled' THEN 'Dibatalkan'
                            END as status_text")
                    ->join('vehicles', 'vehicles.id = bookings.vehicle_id', 'left')
                    ->join('vehicle_types', 'vehicle_types.id = vehicles.vehicle_type_id', 'left')
                    ->join('drivers', 'drivers.id = bookings.driver_id', 'left')
                    ->join('users', 'users.id = bookings.requester_id', 'left')
                    ->where('bookings.departure_date >=', $startDate)
                    ->where('bookings.departure_date <=', $endDate)
                    ->orderBy('bookings.departure_date', 'ASC')
                    ->findAll();
    }

    /**
     * Get total booking count
     */
    public function getTotalBookings()
    {
        return $this->countAllResults();
    }

    /**
     * Get bookings for export
     */
    public function getBookingsForExport($startDate, $endDate)
    {
        return $this->select("bookings.booking_code as Kode_Pemesanan,
                            bookings.departure_date as Tanggal_Berangkat,
                            bookings.departure_time as Waktu_Berangkat,
                            bookings.return_date as Tanggal_Kembali,
                            bookings.return_time as Waktu_Kembali,
                            vehicles.plate_number as Plat_Nomor,
                            vehicle_types.name as Jenis_Kendaraan,
                            (vehicles.brand || ' ' || vehicles.model) as Kendaraan,
                            drivers.name as Driver,
                            users.fullname as Pemohon,
                            bookings.purpose as Keperluan,
                            bookings.origin as Asal,
                            bookings.destination as Tujuan,
                            bookings.status as Status")
                    ->join('vehicles', 'vehicles.id = bookings.vehicle_id', 'left')
                    ->join('vehicle_types', 'vehicle_types.id = vehicles.vehicle_type_id', 'left')
                    ->join('drivers', 'drivers.id = bookings.driver_id', 'left')
                    ->join('users', 'users.id = bookings.requester_id', 'left')
                    ->where('bookings.departure_date >=', $startDate)
                    ->where('bookings.departure_date <=', $endDate)
                    ->orderBy('bookings.departure_date', 'ASC')
                    ->findAll();
    }
}
