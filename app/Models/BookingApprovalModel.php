<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingApprovalModel extends Model
{
    protected $table = 'booking_approvals';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'booking_id', 'approver_id', 'approval_level', 'status', 'notes', 'approved_at'
    ];

    // Timestamps handled by SQLite DEFAULT
    protected $useTimestamps = false;

    /**
     * Get approval by booking ID and level
     */
    public function getApprovalByBookingAndLevel($bookingId, $level)
    {
        return $this->select('booking_approvals.*, users.fullname as approver_name')
                    ->join('users', 'users.id = booking_approvals.approver_id', 'left')
                    ->where('booking_approvals.booking_id', $bookingId)
                    ->where('booking_approvals.approval_level', $level)
                    ->first();
    }

    /**
     * Get all approvals for a booking
     */
    public function getApprovalsByBooking($bookingId)
    {
        return $this->select('booking_approvals.*, users.fullname as approver_name')
                    ->join('users', 'users.id = booking_approvals.approver_id', 'left')
                    ->where('booking_approvals.booking_id', $bookingId)
                    ->orderBy('booking_approvals.approval_level', 'ASC')
                    ->findAll();
    }

    /**
     * Update approval status
     */
    public function updateApprovalStatus($id, $status, $notes = null)
    {
        $data = [
            'status' => $status,
            'approved_at' => date('Y-m-d H:i:s')
        ];
        
        if ($notes !== null) {
            $data['notes'] = $notes;
        }
        
        return $this->update($id, $data);
    }

    /**
     * Check if booking has level 1 approval
     */
    public function hasLevel1Approval($bookingId)
    {
        return $this->where('booking_id', $bookingId)
                    ->where('approval_level', 1)
                    ->where('status', 'approved')
                    ->first() !== null;
    }

    /**
     * Check if booking has level 2 approval
     */
    public function hasLevel2Approval($bookingId)
    {
        return $this->where('booking_id', $bookingId)
                    ->where('approval_level', 2)
                    ->where('status', 'approved')
                    ->first() !== null;
    }

    /**
     * Create initial approvals for a booking (2 levels)
     */
    public function createInitialApprovals($bookingId, $approverLevel1Id, $approverLevel2Id)
    {
        // Level 1 approval
        $this->insert([
            'booking_id' => $bookingId,
            'approver_id' => $approverLevel1Id,
            'approval_level' => 1,
            'status' => 'pending'
        ]);
        
        // Level 2 approval
        $this->insert([
            'booking_id' => $bookingId,
            'approver_id' => $approverLevel2Id,
            'approval_level' => 2,
            'status' => 'pending'
        ]);
        
        return true;
    }

    /**
     * Count pending approvals by approver
     */
    public function countPendingByApprover($approverId)
    {
        return $this->where('approver_id', $approverId)
                    ->where('status', 'pending')
                    ->countAllResults();
    }
}
