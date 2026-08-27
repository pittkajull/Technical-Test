<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Detail Kendaraan</h4>
        <p class="text-muted mb-0"><?= $vehicle->plate_number ?> - <?= $vehicle->brand ?> <?= $vehicle->model ?></p>
    </div>
    <a href="/vehicle" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <!-- Vehicle Info -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-truck me-2"></i>Informasi Kendaraan
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td class="text-muted">Plat Nomor</td>
                        <td><strong class="text-primary fs-5"><?= $vehicle->plate_number ?></strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Merek</td>
                        <td><?= $vehicle->brand ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Model</td>
                        <td><?= $vehicle->model ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tahun</td>
                        <td><?= $vehicle->year ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Warna</td>
                        <td><?= $vehicle->color ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Jenis</td>
                        <td><?= $vehicle->type_name ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Pemilikan</td>
                        <td>
                            <span class="badge bg-<?= $vehicle->ownership === 'milik_perusahaan' ? 'primary' : 'warning' ?>">
                                <?= $vehicle->ownership === 'milik_perusahaan' ? 'Milik Perusahaan' : 'Sewa' ?>
                            </span>
                        </td>
                    </tr>
                    <?php if ($vehicle->rental_company): ?>
                    <tr>
                        <td class="text-muted">Perusahaan Sewa</td>
                        <td><?= $vehicle->rental_company ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="text-muted">BBM</td>
                        <td><?= ucfirst($vehicle->fuel_type) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Kilometer</td>
                        <td><strong><?= number_format($vehicle->current_mileage) ?> km</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Lokasi</td>
                        <td><?= $vehicle->location_name ?? '-' ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
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
                            <span class="badge bg-<?= $statusClass ?> fs-6"><?= $statusText ?></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Logs -->
    <div class="col-lg-8">
        <!-- Fuel Logs -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-fuel-pump me-2"></i>Riwayat Pengisian BBM</span>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addFuelModal">
                    <i class="bi bi-plus-circle me-1"></i>Tambah
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis BBM</th>
                                <th>Liter</th>
                                <th>Total Biaya</th>
                                <th>KM</th>
                                <th>SPBU</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($fuelLogs)): ?>
                            <?php foreach ($fuelLogs as $log): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($log->fuel_date)) ?></td>
                                <td><span class="badge bg-secondary"><?= ucfirst($log->fuel_type) ?></span></td>
                                <td><?= number_format($log->liters, 2) ?> L</td>
                                <td>Rp <?= number_format($log->total_cost, 0, ',', '.') ?></td>
                                <td><?= $log->mileage_at_fuel ? number_format($log->mileage_at_fuel) . ' km' : '-' ?></td>
                                <td><?= $log->station ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">Belum ada data BBM</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Service Logs -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-wrench me-2"></i>Riwayat Service</span>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                    <i class="bi bi-plus-circle me-1"></i>Tambah
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Deskripsi</th>
                                <th>Biaya</th>
                                <th>Bengkel</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($serviceLogs)): ?>
                            <?php 
                            $vehicleServices = array_filter($serviceLogs, fn($s) => $s->vehicle_id == $vehicle->id);
                            ?>
                            <?php if (!empty($vehicleServices)): ?>
                            <?php foreach ($vehicleServices as $log): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($log->service_date)) ?></td>
                                <td>
                                    <span class="badge bg-<?= match($log->service_type) {
                                        'routine' => 'primary',
                                        'repair' => 'warning',
                                        'emergency' => 'danger',
                                        default => 'secondary'
                                    } ?>">
                                        <?= ucfirst($log->service_type) ?>
                                    </span>
                                </td>
                                <td><?= substr($log->description, 0, 50) ?>...</td>
                                <td>Rp <?= number_format($log->cost, 0, ',', '.') ?></td>
                                <td><?= $log->workshop ?? '-' ?></td>
                                <td>
                                    <span class="badge bg-<?= match($log->status) {
                                        'completed' => 'success',
                                        'in_progress' => 'info',
                                        'scheduled' => 'warning',
                                        default => 'secondary'
                                    } ?>">
                                        <?= ucfirst($log->status) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">Belum ada data service</td>
                            </tr>
                            <?php endif; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-3 text-muted">Belum ada data service</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Fuel Modal -->
<div class="modal fade" id="addFuelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/vehicle/add-fuel-log" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-fuel-pump me-2"></i>Tambah Log BBM</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="vehicle_id" value="<?= $vehicle->id ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="fuel_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis BBM <span class="text-danger">*</span></label>
                            <select class="form-select" name="fuel_type" required>
                                <option value="solar">Solar</option>
                                <option value="pertalite">Pertalite</option>
                                <option value="pertamax">Pertamax</option>
                                <option value="dex">Dex</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kilometer Saat Isi BBM</label>
                            <input type="number" class="form-control" name="mileage_at_fuel" value="<?= $vehicle->current_mileage ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Liter <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="liters" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Biaya (Rp) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="total_cost" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">SPBU / Stasiun <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="station" placeholder="Contoh: SPBU Morowali" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/vehicle/add-service-log" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-wrench me-2"></i>Tambah Log Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="vehicle_id" value="<?= $vehicle->id ?>">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Service <span class="text-danger">*</span></label>
                            <select class="form-select" name="service_type" required>
                                <option value="routine">Rutin</option>
                                <option value="repair">Perbaikan</option>
                                <option value="emergency">Darurat</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Service <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="service_date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Jelaskan pekerjaan service..." required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Biaya (Rp)</label>
                            <input type="number" class="form-control" name="cost" value="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bengkel</label>
                            <input type="text" class="form-control" name="workshop" placeholder="Nama bengkel">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kilometer Saat Service</label>
                            <input type="number" class="form-control" name="mileage_at_service" value="<?= $vehicle->current_mileage ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Service Berikutnya (Tanggal)</label>
                            <input type="date" class="form-control" name="next_service_date">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Service Berikutnya (Kilometer)</label>
                        <input type="number" class="form-control" name="next_service_mileage">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
