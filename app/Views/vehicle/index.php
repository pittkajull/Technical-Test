<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Daftar Kendaraan</h4>
        <p class="text-muted mb-0">Kelola data kendaraan perusahaan</p>
    </div>
</div>

<!-- Vehicle List -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover dataTable">
                <thead>
                    <tr>
                        <th>Plat Nomor</th>
                        <th>Kendaraan</th>
                        <th>Jenis</th>
                        <th>Lokasi</th>
                        <th>Pemilikan</th>
                        <th>KM</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($vehicles)): ?>
                    <?php foreach ($vehicles as $vehicle): ?>
                    <tr>
                        <td>
                            <strong class="text-primary"><?= $vehicle->plate_number ?></strong>
                        </td>
                        <td>
                            <span class="d-block"><?= $vehicle->brand ?> <?= $vehicle->model ?></span>
                            <small class="text-muted"><?= $vehicle->year ?> - <?= $vehicle->color ?></small>
                        </td>
                        <td><?= $vehicle->type_name ?></td>
                        <td><?= $vehicle->location_name ?? '-' ?></td>
                        <td>
                            <span class="badge bg-<?= $vehicle->ownership === 'milik_perusahaan' ? 'primary' : 'warning' ?>">
                                <?= $vehicle->ownership === 'milik_perusahaan' ? 'Milik Perusahaan' : 'Sewa' ?>
                            </span>
                        </td>
                        <td><?= number_format($vehicle->current_mileage) ?> km</td>
                        <td>
                            <?php
                            $statusClass = match($vehicle->status) {
                                'tersedia' => 'success',
                                'dalam_perjalanan' => 'info',
                                'service' => 'warning',
                                'tidak_aktif' => 'danger',
                                default => 'secondary'
                            };
                            $statusText = match($vehicle->status) {
                                'tersedia' => 'Tersedia',
                                'dalam_perjalanan' => 'Dalam Perjalanan',
                                'service' => 'Service',
                                'tidak_aktif' => 'Tidak Aktif',
                                default => $vehicle->status
                            };
                            ?>
                            <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                        </td>
                        <td>
                            <a href="/vehicle/detail/<?= $vehicle->id ?>" class="btn btn-sm btn-outline-primary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Belum ada kendaraan</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
