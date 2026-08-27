<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Review Pemesanan</h4>
        <p class="text-muted mb-0">Kode: <strong><?= $booking->booking_code ?></strong></p>
    </div>
    <a href="/approval" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <!-- Booking Info -->
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Detail Pemesanan
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Informasi Pemohon</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 35%">Pemohon</td>
                                <td><strong><?= $booking->requester_name ?></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Keperluan</td>
                                <td><?= $booking->purpose ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Detail Perjalanan</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 35%">Asal</td>
                                <td><strong><?= $booking->origin ?></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tujuan</td>
                                <td><strong><?= $booking->destination ?></strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Berangkat</td>
                                <td>
                                    <?= date('d M Y', strtotime($booking->departure_date)) ?>
                                    pukul <?= $booking->departure_time ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kembali</td>
                                <td>
                                    <?= $booking->return_date ? date('d M Y', strtotime($booking->return_date)) : 'Belum diketahui' ?>
                                    <?= $booking->return_time ? 'pukul ' . $booking->return_time : '' ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Kendaraan</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="mb-1"><?= $booking->plate_number ?></h5>
                                <p class="mb-0 text-muted">
                                    <?= $booking->vehicle_type_name ?> - <?= $booking->brand ?> <?= $booking->model ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Driver</h6>
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="mb-1"><?= $booking->driver_name ?></h5>
                                <p class="mb-0 text-muted">Telp: <?= $booking->driver_phone ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approval History -->
        <div class="card">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Riwayat Persetujuan
            </div>
            <div class="card-body">
                <?php if (!empty($approvals)): ?>
                <?php foreach ($approvals as $approval): ?>
                <div class="d-flex mb-3 <?= $approval !== end($approvals) ? 'pb-3 border-bottom' : '' ?>">
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
                            <h6 class="mb-1">Level <?= $approval->approval_level ?> - <?= $approval->approver_name ?></h6>
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
                <?php else: ?>
                <p class="text-muted text-center mb-0">Belum ada persetujuan</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Approval Action -->
    <div class="col-lg-4">
        <?php
        // Find current user's approval record
        $currentApproval = null;
        foreach ($approvals as $approval) {
            if ($approval->approver_id == session()->get('user_id') && $approval->status === 'pending') {
                $currentApproval = $approval;
                break;
            }
        }
        ?>
        
        <?php if ($currentApproval): ?>
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-check2-square me-2"></i>Form Persetujuan
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <small>
                        <strong>Level Persetujuan:</strong> <?= $currentApproval->approval_level ?><br>
                        <strong>Anda adalah:</strong> <?= $currentApproval->approver_name ?>
                    </small>
                </div>

                <form action="/approval/process/<?= $booking->id ?>" method="POST">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan / Keterangan</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" name="action" value="approve" class="btn btn-success btn-lg">
                            <i class="bi bi-check-circle me-2"></i>Setujui
                        </button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger btn-lg">
                            <i class="bi bi-x-circle me-2"></i>Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php elseif ($booking->status === 'pending'): ?>
        <div class="card">
            <div class="card-body text-center">
                <i class="bi bi-hourglass text-warning" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Menunggu Persetujuan L1</h5>
                <p class="text-muted">Pemesanan ini menunggu persetujuan dari Level 1</p>
            </div>
        </div>
        <?php elseif ($booking->status === 'approved_level1'): ?>
        <div class="card">
            <div class="card-body text-center">
                <i class="bi bi-hourglass text-info" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Menunggu Persetujuan L2</h5>
                <p class="text-muted">Pemesanan ini sudah disetujui L1, menunggu L2</p>
            </div>
        </div>
        <?php else: ?>
        <div class="card">
            <div class="card-body text-center">
                <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Sudah Diproses</h5>
                <p class="text-muted">Pemesanan ini sudah tidak menunggu persetujuan</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
