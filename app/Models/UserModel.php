<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'username', 'password', 'fullname', 'email', 'role', 'is_active', 'last_login'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Find user by username
     */
    public function findByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Find user by role
     */
    public function findByRole($role)
    {
        return $this->where('role', $role)->findAll();
    }

    /**
     * Get all approvers (level 1 and level 2)
     */
    public function getApprovers()
    {
        return $this->whereIn('role', ['approver_level1', 'approver_level2'])
                    ->where('is_active', 1)
                    ->findAll();
    }

    /**
     * Get approver level 1
     */
    public function getApproverLevel1()
    {
        return $this->where('role', 'approver_level1')
                    ->where('is_active', 1)
                    ->findAll();
    }

    /**
     * Get approver level 2
     */
    public function getApproverLevel2()
    {
        return $this->where('role', 'approver_level2')
                    ->where('is_active', 1)
                    ->findAll();
    }

    /**
     * Count users by role
     */
    public function countByRole()
    {
        return $this->select('role, COUNT(*) as total')
                    ->groupBy('role')
                    ->findAll();
    }

    /**
     * Update last login
     */
    public function updateLastLogin($userId)
    {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }
}
