<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Persetujuan Pemesanan</h4>
        <p class="text-muted mb-0">
            <?php if ($approvalLevel == 1): ?>
            Pemesanan yang menunggu persetujuan Anda (Level 1)
            <?php elseif ($approvalLevel == 2): ?>
            Pemesanan yang menunggu persetujuan Anda (Level 2)
            <?php else: ?>
            Semua pemesanan yang menunggu persetujuan
            <?php endif; ?>
        </p>
    </div>
</div>

<!-- Pending Approvals -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-hourglass-split me-2"></i>Pemesanan Menunggu Persetujuan
        <span class="badge bg-warning ms-2"><?= count($pendingBookings) ?></span>
    </div>
    <div class="card-body">
        <?php if (!empty($pendingBookings)): ?>
        <div class="table-responsive">
            <table class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pemohon</th>
                        <th>Kendaraan</th>
                        <th>Driver</th>
                        <th>Tujuan</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingBookings as $booking): ?>
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
                        <td><?= $booking->destination ?></td>
                        <td>
                            <?= date('d M Y', strtotime($booking->departure_date)) ?>
                            <br><small class="text-muted"><?= $booking->departure_time ?></small>
                        </td>
                        <td>
                            <a href="/approval/detail/<?= $booking->id ?>" class="btn btn-sm btn-primary me-1" title="Review">
                                <i class="bi bi-eye"></i> Review
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
            <h5 class="mt-3">Tidak ada pemesanan yang perlu disetujui</h5>
            <p class="text-muted">Semua pemesanan sudah diproses</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
