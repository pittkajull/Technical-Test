<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Detail Pemesanan</h4>
        <p class="text-muted mb-0">Kode: <strong><?= $booking->booking_code ?></strong></p>
    </div>
    <a href="/booking" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <!-- Booking Info -->
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-info-circle me-2"></i>Informasi Pemesanan</span>
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
                $statusText = match($booking->status) {
                    'pending' => 'Menunggu Persetujuan',
                    'approved_level1' => 'Disetujui Level 1',
                    'approved_level2' => 'Disetujui Level 2',
                    'completed' => 'Selesai',
                    'rejected' => 'Ditolak',
                    'cancelled' => 'Dibatalkan',
                    default => $booking->status
                };
                ?>
                <span class="badge bg-<?= $statusClass ?> fs-6"><?= $statusText ?></span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 40%">Pemohon</td>
                                <td><strong><?= $booking->requester_name ?></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Keperluan</td>
                                <td><?= $booking->purpose ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Asal</td>
                                <td><?= $booking->origin ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tujuan</td>
                                <td><?= $booking->destination ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 40%">Kendaraan</td>
                                <td>
                                    <strong><?= $booking->plate_number ?></strong>
                                    <br><small class="text-muted"><?= $booking->vehicle_type_name ?> - <?= $booking->brand ?> <?= $booking->model ?></small>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Driver</td>
                                <td>
                                    <strong><?= $booking->driver_name ?></strong>
                                    <br><small class="text-muted"><?= $booking->driver_phone ?></small>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Berangkat</td>
                                <td>
                                    <?= date('d M Y', strtotime($booking->departure_date)) ?>
                                    <br><small class="text-muted"><?= $booking->departure_time ?></small>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kembali</td>
                                <td>
                                    <?= $booking->return_date ? date('d M Y', strtotime($booking->return_date)) : '-' ?>
                                    <br><small class="text-muted"><?= $booking->return_time ?? '-' ?></small>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <?php if ($booking->rejection_reason): ?>
                <div class="alert alert-danger mt-3">
                    <h6 class="alert-heading"><i class="bi bi-x-circle me-2"></i>Alasan Penolakan:</h6>
                    <p class="mb-0"><?= $booking->rejection_reason ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Approval History -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-check2-circle me-2"></i>Riwayat Persetujuan
            </div>
            <div class="card-body">
                <?php if (!empty($approvals)): ?>
                <div class="timeline">
                    <?php foreach ($approvals as $approval): ?>
                    <div class="d-flex mb-4">
                        <div class="me-3">
                            <?php
                            $iconClass = match($approval->status) {
                                'approved' => 'bi-check-circle-fill text-success',
                                'rejected' => 'bi-x-circle-fill text-danger',
                                'pending' => 'bi-clock-fill text-warning',
                                default => 'bi-circle text-secondary'
                            };
                            ?>
                            <i class="bi <?= $iconClass ?> fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1">
                                    Level <?= $approval->approval_level ?> - <?= $approval->approver_name ?>
                                </h6>
                                <span class="badge bg-<?= match($approval->status) {
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'pending' => 'warning',
                                    default => 'secondary'
                                } ?>">
                                    <?= $approval->status === 'approved' ? 'Disetujui' : ($approval->status === 'rejected' ? 'Ditolak' : 'Menunggu') ?>
                                </span>
                            </div>
                            <?php if ($approval->notes): ?>
                            <p class="text-muted mb-1"><i class="bi bi-chat-quote me-1"></i><?= $approval->notes ?></p>
                            <?php endif; ?>
                            <?php if ($approval->approved_at): ?>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i><?= date('d M Y H:i', strtotime($approval->approved_at)) ?>
                            </small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-muted text-center mb-0">Belum ada persetujuan</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Actions Sidebar -->
    <div class="col-lg-4">
        <!-- Admin Actions -->
        <?php if (session()->get('role') === 'admin'): ?>
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-gear me-2"></i>Aksi Admin
            </div>
            <div class="card-body">
                <?php if ($booking->status === 'completed' || $booking->status === 'cancelled'): ?>
                <p class="text-muted mb-0">Pemesanan sudah selesai/dibatalkan</p>
                <?php else: ?>
                <form action="/booking/update-status/<?= $booking->id ?>" method="POST">
                    <?php if ($booking->status !== 'rejected'): ?>
                    <div class="mb-3">
                        <label class="form-label">Ubah Status</label>
                        <select class="form-select" name="status" id="statusSelect" required>
                            <option value="" disabled selected>-- Pilih Status --</option>
                            <?php if ($booking->status === 'approved_level2'): ?>
                            <option value="in_progress">Mulai Perjalanan</option>
                            <?php endif; ?>
                            <?php if ($booking->status === 'in_progress' || $booking->status === 'approved_level2'): ?>
                            <option value="completed">Selesai</option>
                            <?php endif; ?>
                            <option value="cancelled">Batalkan</option>
                            <option value="rejected">Tolak</option>
                        </select>
                    </div>
                    
                    <div class="mb-3 d-none" id="rejectionReasonDiv">
                        <label class="form-label">Alasan Penolakan</label>
                        <textarea class="form-control" name="rejection_reason" rows="3" 
                                  placeholder="Masukkan alasan penolakan..."></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="bi bi-check-circle me-2"></i>Update Status
                    </button>
                    <?php endif; ?>
                </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Status History -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Info Tambahan
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="text-muted">Dibuat</td>
                        <td><?= date('d M Y H:i', strtotime($booking->created_at)) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Diupdate</td>
                        <td><?= date('d M Y H:i', strtotime($booking->updated_at)) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('statusSelect')?.addEventListener('change', function() {
    const rejectionDiv = document.getElementById('rejectionReasonDiv');
    if (this.value === 'rejected') {
        rejectionDiv.classList.remove('d-none');
    } else {
        rejectionDiv.classList.add('d-none');
    }
});
</script>
<?= $this->endSection() ?>
