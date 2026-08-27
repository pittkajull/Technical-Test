<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Laporan Pemesanan Kendaraan</h4>
        <p class="text-muted mb-0">Laporan periodik pemesanan kendaraan</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/report/export?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-2"></i>Export CSV
        </a>
        <a href="/report/export-excel?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" class="btn btn-primary">
            <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form action="/report" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" name="start_date" value="<?= $startDate ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" class="form-control" name="end_date" value="<?= $endDate ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-2"></i>Tampilkan
                </button>
                <button type="button" class="btn btn-outline-secondary" onclick="setThisMonth()">
                    <i class="bi bi-calendar3 me-2"></i>Bulan Ini
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stat-card primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-primary"><?= $totalBookings ?></h3>
                        <p class="text-muted mb-0">Total Pemesanan</p>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-calendar-check" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-success"><?= $statusCounts['completed'] ?? 0 ?></h3>
                        <p class="text-muted mb-0">Selesai</p>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="text-warning"><?= $statusCounts['pending'] ?? 0 ?></h3>
                        <p class="text-muted mb-0">Menunggu</p>
                    </div>
                    <div class="text-warning">
                        <i class="bi bi-hourglass" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-table me-2"></i>Data Pemesanan (<?= date('d M Y', strtotime($startDate)) ?> - <?= date('d M Y', strtotime($endDate)) ?>)</span>
        <span class="badge bg-primary"><?= $totalBookings ?> data</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Pemohon</th>
                        <th>Kendaraan</th>
                        <th>Driver</th>
                        <th>Asal</th>
                        <th>Tujuan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong class="text-primary"><?= $booking->booking_code ?></strong></td>
                        <td><?= $booking->requester_name ?></td>
                        <td>
                            <span class="d-block"><?= $booking->plate_number ?></span>
                            <small class="text-muted"><?= $booking->vehicle_type_name ?></small>
                        </td>
                        <td><?= $booking->driver_name ?></td>
                        <td><?= $booking->origin ?></td>
                        <td><?= $booking->destination ?></td>
                        <td><?= date('d M Y', strtotime($booking->departure_date)) ?></td>
                        <td>
                            <?php
                            $statusClass = match($booking->status) {
                                'pending' => 'warning',
                                'approved_level1' => 'info',
                                'approved_level2' => 'primary',
                                'completed' => 'success',
                                'rejected' => 'danger',
                                'cancelled' => 'secondary',
                                default => 'secondary'
                            };
                            ?>
                            <span class="badge bg-<?= $statusClass ?>"><?= $booking->status_text ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Tidak ada data untuk periode ini</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function setThisMonth() {
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
    document.querySelector('[name="start_date"]').value = firstDay.toISOString().split('T')[0];
    document.querySelector('[name="end_date"]').value = lastDay.toISOString().split('T')[0];
}
</script>
<?= $this->endSection() ?>
