<?php

namespace App\Controllers;

use App\Models\BookingModel;
use App\Models\FuelLogModel;
use App\Models\VehicleModel;
use App\Models\ApplicationLogModel;

class Report extends BaseController
{
    protected $bookingModel;
    protected $fuelLogModel;
    protected $vehicleModel;
    protected $logModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->fuelLogModel = new FuelLogModel();
        $this->vehicleModel = new VehicleModel();
        $this->logModel = new ApplicationLogModel();
    }

    /**
     * Show report page
     */
    public function index()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        $bookings = $this->bookingModel->getBookingsByDateRange($startDate, $endDate);
        $totalBookings = count($bookings);

        // Count by status
        $statusCounts = [];
        foreach ($bookings as $booking) {
            $status = $booking->status;
            if (!isset($statusCounts[$status])) {
                $statusCounts[$status] = 0;
            }
            $statusCounts[$status]++;
        }

        $data = [
            'title' => 'Laporan Pemesanan - Sistem Pemesanan Kendaraan',
            'bookings' => $bookings,
            'totalBookings' => $totalBookings,
            'statusCounts' => $statusCounts,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];

        return view('report/index', $data);
    }

    /**
     * Export bookings to CSV (Excel-compatible)
     */
    public function export()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        $bookings = $this->bookingModel->getBookingsForExport($startDate, $endDate);

        // Log the export action
        $this->logModel->log(
            session()->get('user_id'),
            'EXPORT_REPORT',
            'Export laporan pemesanan dari ' . $startDate . ' sampai ' . $endDate
        );

        // Set headers for CSV download
        $filename = 'Laporan_Pemesanan_' . $startDate . '_sd_' . $endDate . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Add BOM for Excel UTF-8 compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Add header row
        fputcsv($output, [
            'Kode Pemesanan',
            'Tanggal Berangkat',
            'Waktu Berangkat',
            'Tanggal Kembali',
            'Waktu Kembali',
            'Plat Nomor',
            'Jenis Kendaraan',
            'Kendaraan',
            'Driver',
            'Pemohon',
            'Keperluan',
            'Asal',
            'Tujuan',
            'Status'
        ]);
        
        // Add data rows
        foreach ($bookings as $booking) {
            fputcsv($output, [
                $booking->Kode_Pemesanan,
                $booking->Tanggal_Berangkat,
                $booking->Waktu_Berangkat,
                $booking->Tanggal_Kembali ?? '-',
                $booking->Waktu_Kembali ?? '-',
                $booking->Plat_Nomor,
                $booking->Jenis_Kendaraan,
                $booking->Kendaraan,
                $booking->Driver,
                $booking->Pemohon,
                $booking->Keperluan,
                $booking->Asal,
                $booking->Tujuan,
                $booking->Status
            ]);
        }
        
        fclose($output);
        exit();
    }

    /**
     * Export to Excel (XLSX) using PhpSpreadsheet
     */
    public function exportExcel()
    {
        if (!session()->get('is_logged_in')) {
            return redirect()->to('/auth');
        }

        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-t');

        $bookings = $this->bookingModel->getBookingsForExport($startDate, $endDate);

        // Log the export action
        $this->logModel->log(
            session()->get('user_id'),
            'EXPORT_REPORT_EXCEL',
            'Export laporan pemesanan (Excel) dari ' . $startDate . ' sampai ' . $endDate
        );

        // Create Excel file using PhpSpreadsheet-like approach (CSV with Excel headers)
        $filename = 'Laporan_Pemesanan_' . $startDate . '_sd_' . $endDate . '.xls';
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo '<table border="1">';
        echo '<thead>';
        echo '<tr style="background-color: #4CAF50; color: white; font-weight: bold;">';
        echo '<th>Kode Pemesanan</th>';
        echo '<th>Tanggal Berangkat</th>';
        echo '<th>Waktu Berangkat</th>';
        echo '<th>Tanggal Kembali</th>';
        echo '<th>Waktu Kembali</th>';
        echo '<th>Plat Nomor</th>';
        echo '<th>Jenis Kendaraan</th>';
        echo '<th>Kendaraan</th>';
        echo '<th>Driver</th>';
        echo '<th>Pemohon</th>';
        echo '<th>Keperluan</th>';
        echo '<th>Asal</th>';
        echo '<th>Tujuan</th>';
        echo '<th>Status</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($bookings as $booking) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($booking->Kode_Pemesanan) . '</td>';
            echo '<td>' . htmlspecialchars($booking->Tanggal_Berangkat) . '</td>';
            echo '<td>' . htmlspecialchars($booking->Waktu_Berangkat) . '</td>';
            echo '<td>' . htmlspecialchars($booking->Tanggal_Kembali ?? '-') . '</td>';
            echo '<td>' . htmlspecialchars($booking->Waktu_Kembali ?? '-') . '</td>';
            echo '<td>' . htmlspecialchars($booking->Plat_Nomor) . '</td>';
            echo '<td>' . htmlspecialchars($booking->Jenis_Kendaraan) . '</td>';
            echo '<td>' . htmlspecialchars($booking->Kendaraan) . '</td>';
            echo '<td>' . htmlspecialchars($booking->Driver) . '</td>';
            echo '<td>' . htmlspecialchars($booking->Pemohon) . '</td>';
            echo '<td>' . htmlspecialchars($booking->Keperluan) . '</td>';
            echo '<td>' . htmlspecialchars($booking->Asal) . '</td>';
            echo '<td>' . htmlspecialchars($booking->Tujuan) . '</td>';
            echo '<td>' . htmlspecialchars($booking->Status) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        exit();
    }
}
