<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Pemesanan Kendaraan Baru</h4>
        <p class="text-muted mb-0">Isi form berikut untuk melakukan pemesanan kendaraan</p>
    </div>
    <a href="/booking" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-form me-2"></i>Form Pemesanan
            </div>
            <div class="card-body">
                <form action="/booking/store" method="POST">
                    <div class="row">
                        <!-- Vehicle Selection -->
                        <div class="col-md-6 mb-3">
                            <label for="vehicle_id" class="form-label">Kendaraan <span class="text-danger">*</span></label>
                            <select class="form-select" id="vehicle_id" name="vehicle_id" required>
                                <option value="">-- Pilih Kendaraan --</option>
                                <?php if (!empty($vehicles)): ?>
                                <?php foreach ($vehicles as $vehicle): ?>
                                <option value="<?= $vehicle->id ?>" 
                                    data-plate="<?= $vehicle->plate_number ?>"
                                    data-type="<?= $vehicle->type_name ?>">
                                    <?= $vehicle->plate_number ?> - <?= $vehicle->brand ?> <?= $vehicle->model ?> (<?= $vehicle->type_name ?>)
                                </option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Driver Selection -->
                        <div class="col-md-6 mb-3">
                            <label for="driver_id" class="form-label">Driver <span class="text-danger">*</span></label>
                            <select class="form-select" id="driver_id" name="driver_id" required>
                                <option value="">-- Pilih Driver --</option>
                                <?php if (!empty($drivers)): ?>
                                <?php foreach ($drivers as $driver): ?>
                                <option value="<?= $driver->id ?>">
                                    <?= $driver->name ?> (<?= $driver->license_type ?>)
                                </option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Purpose -->
                    <div class="mb-3">
                        <label for="purpose" class="form-label">Keperluan / Tujuan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="purpose" name="purpose" rows="3" 
                                  placeholder="Jelaskan keperluan pemakaian kendaraan..." required></textarea>
                    </div>

                    <div class="row">
                        <!-- Origin -->
                        <div class="col-md-6 mb-3">
                            <label for="origin" class="form-label">Asal / Lokasi Keberangkatan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="origin" name="origin" 
                                   placeholder="Contoh: Kantor Pusat Jakarta" required>
                        </div>

                        <!-- Destination -->
                        <div class="col-md-6 mb-3">
                            <label for="destination" class="form-label">Tujuan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="destination" name="destination" 
                                   placeholder="Contoh: Tambang Morowali" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Departure Date & Time -->
                        <div class="col-md-3 mb-3">
                            <label for="departure_date" class="form-label">Tanggal Berangkat <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="departure_date" name="departure_date" 
                                   min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="departure_time" class="form-label">Waktu Berangkat <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="departure_time" name="departure_time" required>
                        </div>

                        <!-- Return Date & Time -->
                        <div class="col-md-3 mb-3">
                            <label for="estimated_return_date" class="form-label">Estimasi Tanggal Kembali</label>
                            <input type="date" class="form-control" id="estimated_return_date" name="estimated_return_date">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="estimated_return_time" class="form-label">Estimasi Waktu Kembali</label>
                            <input type="time" class="form-control" id="estimated_return_time" name="estimated_return_time">
                        </div>
                    </div>

                    <hr>

                    <h6 class="mb-3"><i class="bi bi-person-check me-2"></i>Persetujuan (2 Level)</h6>

                    <div class="row">
                        <!-- Approver Level 1 -->
                        <div class="col-md-6 mb-3">
                            <label for="approver_level1" class="form-label">Penyetuju Level 1 <span class="text-danger">*</span></label>
                            <select class="form-select" id="approver_level1" name="approver_level1" required>
                                <option value="">-- Pilih Penyetuju L1 --</option>
                                <?php if (!empty($approvers)): ?>
                                <?php foreach ($approvers as $approver): ?>
                                <?php if ($approver->role === 'approver_level1'): ?>
                                <option value="<?= $approver->id ?>">
                                    <?= $approver->fullname ?> (Level 1)
                                </option>
                                <?php endif; ?>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Approver Level 2 -->
                        <div class="col-md-6 mb-3">
                            <label for="approver_level2" class="form-label">Penyetuju Level 2 <span class="text-danger">*</span></label>
                            <select class="form-select" id="approver_level2" name="approver_level2" required>
                                <option value="">-- Pilih Penyetuju L2 --</option>
                                <?php if (!empty($approvers)): ?>
                                <?php foreach ($approvers as $approver): ?>
                                <?php if ($approver->role === 'approver_level2'): ?>
                                <option value="<?= $approver->id ?>">
                                    <?= $approver->fullname ?> (Level 2)
                                </option>
                                <?php endif; ?>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="/booking" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Simpan Pemesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <i class="bi bi-info-circle me-2"></i>Informasi
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <h6 class="alert-heading"><i class="bi bi-lightbulb me-2"></i>Cara Pemesanan:</h6>
                    <ol class="mb-0 mt-2 small">
                        <li>Pilih kendaraan yang tersedia</li>
                        <li>Tentukan driver untuk kendaraan</li>
                        <li>Isi detail perjalanan (asal, tujuan, tanggal)</li>
                        <li>Pilih 2 orang penyetuju (Level 1 & Level 2)</li>
                        <li>Submit dan tunggu persetujuan</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <i class="bi bi-exclamation-triangle me-2"></i>Catatan Penting
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2">
                        <i class="bi bi-check text-success me-2"></i>
                        Penyetuju Level 1 dan 2 harus berbeda
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check text-success me-2"></i>
                        Pemesanan harus disetujui kedua level
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check text-success me-2"></i>
                        Kendaraan akan otomatis tersedia setelah selesai
                    </li>
                    <li>
                        <i class="bi bi-check text-success me-2"></i>
                        Semua aktivitas tercatat dalam log
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
