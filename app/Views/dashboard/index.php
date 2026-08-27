<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Dashboard</h4>
        <p class="text-muted mb-0">Selamat datang, <?= session()->get('fullname') ?></p>
    </div>
    <div>
        <span class="text-muted">
            <i class="bi bi-calendar3 me-1"></i>
            <?= date('d M Y') ?>
        </span>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-primary"><?= $totalVehicles ?></h3>
                        <p class="text-muted mb-0">Total Kendaraan</p>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-truck" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-success"><?= $availableVehicles ?></h3>
                        <p class="text-muted mb-0">Kendaraan Tersedia</p>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-check-circle" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-warning"><?= $totalDrivers ?></h3>
                        <p class="text-muted mb-0">Total Driver</p>
                    </div>
                    <div class="text-warning">
                        <i class="bi bi-person-badge" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card stat-card danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-danger"><?= $totalBookings ?></h3>
                        <p class="text-muted mb-0">Total Pemesanan</p>
                    </div>
                    <div class="text-danger">
                        <i class="bi bi-calendar-check" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <!-- Booking by Month Chart -->
    <div class="col-xl-8 col-lg-7 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-graph-up me-2"></i>Grafik Pemesanan Kendaraan</span>
                <select class="form-select form-select-sm w-auto" id="yearFilter">
                    <option value="2026" selected>2026</option>
                    <option value="2025">2025</option>
                </select>
            </div>
            <div class="card-body">
                <canvas id="bookingChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Booking by Status Chart -->
    <div class="col-xl-4 col-lg-5 mb-3">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-pie-chart me-2"></i>Status Pemesanan
            </div>
            <div class="card-body">
                <canvas id="statusChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Second Charts Row -->
<div class="row mb-4">
    <!-- Bookings by Vehicle Type -->
    <div class="col-xl-6 mb-3">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart me-2"></i>Pemesanan per Jenis Kendaraan
            </div>
            <div class="card-body">
                <canvas id="vehicleTypeChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Fuel Cost Chart -->
    <div class="col-xl-6 mb-3">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-fuel-pump me-2"></i>Konsumsi BBM per Bulan
            </div>
            <div class="card-body">
                <canvas id="fuelChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Data -->
<div class="row">
    <!-- Recent Bookings -->
    <div class="col-xl-8 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>Pemesanan Terbaru</span>
                <a href="/booking" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Kendaraan</th>
                                <th>Driver</th>
                                <th>Tujuan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentBookings)): ?>
                            <?php foreach ($recentBookings as $booking): ?>
                            <tr>
                                <td>
                                    <a href="/booking/detail/<?= $booking->id ?>" class="text-decoration-none">
                                        <strong><?= $booking->booking_code ?></strong>
                                    </a>
                                </td>
                                <td><?= $booking->plate_number ?></td>
                                <td><?= $booking->driver_name ?></td>
                                <td><?= substr($booking->destination, 0, 20) ?>...</td>
                                <td><?= date('d M Y', strtotime($booking->departure_date)) ?></td>
                                <td>
                                    <?php
                                    $statusClass = match($booking->status) {
                                        'pending' => 'warning',
                                        'approved_level1', 'approved_level2' => 'info',
                                        'completed' => 'success',
                                        'rejected' => 'danger',
                                        'cancelled' => 'secondary',
                                        default => 'secondary'
                                    };
                                    $statusText = match($booking->status) {
                                        'pending' => 'Menunggu',
                                        'approved_level1' => 'Disetujui L1',
                                        'approved_level2' => 'Disetujui L2',
                                        'completed' => 'Selesai',
                                        'rejected' => 'Ditolak',
                                        'cancelled' => 'Dibatalkan',
                                        default => $booking->status
                                    };
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0">Belum ada pemesanan</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Logs -->
    <div class="col-xl-4 mb-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-journal-text me-2"></i>Aktivitas Terbaru</span>
                <?php if (session()->get('role') === 'admin'): ?>
                <a href="/logs" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (!empty($recentLogs)): ?>
                    <?php foreach ($recentLogs as $log): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">
                                    <?php
                                    $icon = match(substr($log->action, 0, 5)) {
                                        'LOGI' => 'bi-box-arrow-in-right',
                                        'LOGO' => 'bi-box-arrow-right',
                                        'CREA' => 'bi-plus-circle',
                                        'APPR' => 'bi-check-circle',
                                        'REJE' => 'bi-x-circle',
                                        default => 'bi-activity'
                                    };
                                    ?>
                                    <i class="bi <?= $icon ?> me-1"></i>
                                    <?= $log->action ?>
                                </h6>
                                <p class="mb-1 small text-muted"><?= $log->description ?></p>
                            </div>
                            <small class="text-muted">
                                <?= date('d M H:i', strtotime($log->created_at)) ?>
                            </small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="list-group-item text-center py-4 text-muted">
                        <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                        <p class="mt-2 mb-0">Belum ada aktivitas</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Booking by Month Chart
const monthlyData = <?= json_encode($monthlyData) ?>;
const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

new Chart(document.getElementById('bookingChart'), {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Jumlah Pemesanan',
            data: monthlyData,
            borderColor: '#3498db',
            backgroundColor: 'rgba(52, 152, 219, 0.1)',
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#3498db',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

// Status Chart
const statusStats = <?= json_encode($bookingsStats) ?>;
const statusLabels = Object.keys(statusStats).map(s => {
    const map = {
        'pending': 'Menunggu',
        'approved_level1': 'Disetujui L1',
        'approved_level2': 'Disetujui L2',
        'completed': 'Selesai',
        'rejected': 'Ditolak',
        'cancelled': 'Dibatalkan'
    };
    return map[s] || s;
});
const statusColors = ['#f39c12', '#3498db', '#2980b9', '#27ae60', '#e74c3c', '#95a5a6'];

new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: Object.values(statusStats),
            backgroundColor: statusColors.slice(0, Object.keys(statusStats).length),
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Vehicle Type Chart
const typeData = <?= json_encode($bookingsByType) ?>;
new Chart(document.getElementById('vehicleTypeChart'), {
    type: 'bar',
    data: {
        labels: typeData.map(t => t.type_name),
        datasets: [{
            label: 'Jumlah Pemesanan',
            data: typeData.map(t => t.total),
            backgroundColor: [
                '#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6'
            ],
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

// Fuel Cost Chart
const fuelData = <?= json_encode($fuelCostData) ?>;
new Chart(document.getElementById('fuelChart'), {
    type: 'bar',
    data: {
        labels: months,
        datasets: [{
            label: 'Biaya BBM (Rp)',
            data: fuelData,
            backgroundColor: 'rgba(231, 76, 60, 0.7)',
            borderColor: '#e74c3c',
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});
</script>
<?= $this->endSection() ?>
