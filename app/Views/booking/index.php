<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Daftar Pemesanan Kendaraan</h4>
        <p class="text-muted mb-0">Kelola semua pemesanan kendaraan</p>
    </div>
    <a href="/booking/create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Pemesanan Baru
    </a>
</div>

<!-- Booking List -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover dataTable" id="bookingTable">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pemohon</th>
                        <th>Kendaraan</th>
                        <th>Driver</th>
                        <th>Asal</th>
                        <th>Tujuan</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                    <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td>
                            <strong class="text-primary"><?= $booking->booking_code ?></strong>
                        </td>
                        <td><?= $booking->requester_name ?></td>
                        <td>
                            <span class="d-block"><?= $booking->plate_number ?></span>
                            <small class="text-muted"><?= $booking->vehicle_type_name ?></small>
                        </td>
                        <td><?= $booking->driver_name ?></td>
                        <td><?= $booking->origin ?></td>
                        <td><?= $booking->destination ?></td>
                        <td>
                            <span class="d-block"><?= date('d M Y', strtotime($booking->departure_date)) ?></span>
                            <small class="text-muted"><?= $booking->departure_time ?></small>
                        </td>
                        <td>
                            <?php
                            $statusClass = match($booking->status) {
                                'pending' => 'warning',
                                'approved_level1' => 'info',
                                'approved_level2' => 'primary',
                                'completed' => 'success',
                                'rejected' => 'danger',
                                'cancelled' => 'secondary',
                                'in_progress' => 'info',
                                default => 'secondary'
                            };
                            $statusText = match($booking->status) {
                                'pending' => 'Menunggu',
                                'approved_level1' => 'Disetujui L1',
                                'approved_level2' => 'Disetujui L2',
                                'completed' => 'Selesai',
                                'rejected' => 'Ditolak',
                                'cancelled' => 'Dibatalkan',
                                'in_progress' => 'Berlangsung',
                                default => $booking->status
                            };
                            ?>
                            <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                        </td>
                        <td>
                            <a href="/booking/detail/<?= $booking->id ?>" class="btn btn-sm btn-outline-primary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Belum ada pemesanan</p>
                            <a href="/booking/create" class="btn btn-primary mt-2">
                                <i class="bi bi-plus-circle me-2"></i>Buat Pemesanan Baru
                            </a>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
