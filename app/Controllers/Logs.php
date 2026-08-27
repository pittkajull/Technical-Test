<?php

namespace App\Controllers;

use App\Models\ApplicationLogModel;

class Logs extends BaseController
{
    protected $logModel;

    public function __construct()
    {
        $this->logModel = new ApplicationLogModel();
    }

    /**
     * Show logs page
     */
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $logs = $this->logModel->getLogsWithDetails(100);

        $data = [
            'title' => 'Log Aktivitas - Sistem Pemesanan Kendaraan',
            'logs' => $logs
        ];

        return view('logs/index', $data);
    }

    /**
     * Show logs by action
     */
    public function byAction($action)
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $logs = $this->logModel->getLogsByAction($action, 50);

        $data = [
            'title' => 'Log Aktivitas - ' . $action,
            'logs' => $logs,
            'filterAction' => $action
        ];

        return view('logs/index', $data);
    }
}
