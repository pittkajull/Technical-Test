<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationLogModel extends Model
{
    protected $table = 'application_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'action', 'description', 'ip_address', 'user_agent'
    ];

    // Timestamps handled manually for SQLite compatibility
    protected $useTimestamps = false;

    /**
     * Log an activity
     */
    public function log($userId, $action, $description, $ipAddress = null, $userAgent = null)
    {
        return $this->insert([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $ipAddress ?? $this->getRequestIP(),
            'user_agent' => $userAgent ?? $this->getUserAgent(),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get logs with user info
     */
    public function getLogsWithDetails($limit = 100)
    {
        return $this->select('application_logs.*, users.fullname as user_name')
                    ->join('users', 'users.id = application_logs.user_id', 'left')
                    ->orderBy('application_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get logs by action
     */
    public function getLogsByAction($action, $limit = 50)
    {
        return $this->select('application_logs.*, users.fullname as user_name')
                    ->join('users', 'users.id = application_logs.user_id', 'left')
                    ->where('application_logs.action', $action)
                    ->orderBy('application_logs.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get logs by date range
     */
    public function getLogsByDateRange($startDate, $endDate)
    {
        return $this->select('application_logs.*, users.fullname as user_name')
                    ->join('users', 'users.id = application_logs.user_id', 'left')
                    ->where('application_logs.created_at >=', $startDate)
                    ->where('application_logs.created_at <=', $endDate . ' 23:59:59')
                    ->orderBy('application_logs.created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get request IP address
     */
    private function getRequestIP()
    {
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Get user agent
     */
    private function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    }
}
