<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Log Aktivitas Aplikasi</h4>
        <p class="text-muted mb-0">
            <?php if (isset($filterAction)): ?>
            Filter: <strong><?= $filterAction ?></strong>
            <?php else: ?>
            Semua aktivitas aplikasi
            <?php endif; ?>
        </p>
    </div>
    <a href="/logs" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Semua Log
    </a>
</div>

<!-- Filter Buttons -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2">
            <span class="text-muted me-2 align-self-center">Filter:</span>
            <a href="/logs" class="btn btn-sm <?= !isset($filterAction) ? 'btn-primary' : 'btn-outline-primary' ?>">Semua</a>
            <a href="/logs/action/LOGIN" class="btn btn-sm <?= (isset($filterAction) && $filterAction === 'LOGIN') ? 'btn-primary' : 'btn-outline-primary' ?>">Login</a>
            <a href="/logs/action/CREATE_BOOKING" class="btn btn-sm <?= (isset($filterAction) && $filterAction === 'CREATE_BOOKING') ? 'btn-primary' : 'btn-outline-primary' ?>">Buat Pemesanan</a>
            <a href="/logs/action/APPROVE_BOOKING_L1" class="btn btn-sm <?= (isset($filterAction) && $filterAction === 'APPROVE_BOOKING_L1') ? 'btn-primary' : 'btn-outline-primary' ?>">Setuju L1</a>
            <a href="/logs/action/APPROVE_BOOKING_L2" class="btn btn-sm <?= (isset($filterAction) && $filterAction === 'APPROVE_BOOKING_L2') ? 'btn-primary' : 'btn-outline-primary' ?>">Setuju L2</a>
            <a href="/logs/action/REJECT_BOOKING" class="btn btn-sm <?= (isset($filterAction) && $filterAction === 'REJECT_BOOKING') ? 'btn-primary' : 'btn-outline-primary' ?>">Tolak</a>
            <a href="/logs/action/EXPORT_REPORT" class="btn btn-sm <?= (isset($filterAction) && $filterAction === 'EXPORT_REPORT') ? 'btn-primary' : 'btn-outline-primary' ?>">Export</a>
        </div>
    </div>
</div>

<!-- Logs Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-journal-text me-2"></i>Riwayat Aktivitas</span>
        <span class="badge bg-secondary"><?= count($logs) ?> log</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Deskripsi</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td>
                            <span class="d-block"><?= date('d M Y', strtotime($log->created_at)) ?></span>
                            <small class="text-muted"><?= date('H:i:s', strtotime($log->created_at)) ?></small>
                        </td>
                        <td><strong><?= $log->user_name ?? 'System' ?></strong></td>
                        <td>
                            <?php
                            $badgeClass = match(substr($log->action, 0, 5)) {
                                'LOGI' => 'primary',
                                'LOGO' => 'secondary',
                                'CREA' => 'success',
                                'APPR' => 'info',
                                'REJE' => 'danger',
                                'UPDA' => 'warning',
                                'EXPO' => 'primary',
                                'ADD_' => 'success',
                                default => 'secondary'
                            };
                            ?>
                            <span class="badge bg-<?= $badgeClass ?>"><?= $log->action ?></span>
                        </td>
                        <td><?= $log->description ?></td>
                        <td><code><?= $log->ip_address ?></code></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Belum ada log aktivitas</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
