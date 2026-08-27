-- =====================================================
-- DATABASE: fleet_booking
-- Sistem Pemesanan Kendaraan Perusahaan Tambang Nikel
-- =====================================================

CREATE DATABASE IF NOT EXISTS fleet_booking CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE fleet_booking;

-- =====================================================
-- TABEL USERS (Admin & Approver)
-- =====================================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'approver_level1', 'approver_level2') NOT NULL DEFAULT 'admin',
    is_active TINYINT(1) DEFAULT 1,
    last_login DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- TABEL LOCATIONS (Kantor Pusat, Kantor Cabang, Tambang)
-- =====================================================
CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('kantor_pusat', 'kantor_cabang', 'tambang') NOT NULL,
    address TEXT,
    latitude DECIMAL(10, 8) NULL,
    longitude DECIMAL(11, 8) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- TABEL VEHICLE_TYPES (Jenis Kendaraan)
-- =====================================================
CREATE TABLE vehicle_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description VARCHAR(100)
) ENGINE=InnoDB;

-- =====================================================
-- TABEL VEHICLES (Data Kendaraan)
-- =====================================================
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plate_number VARCHAR(20) NOT NULL UNIQUE,
    vehicle_type_id INT NOT NULL,
    brand VARCHAR(50),
    model VARCHAR(50),
    year INT,
    color VARCHAR(30),
    ownership ENUM('milik_perusahaan', 'sewa') NOT NULL DEFAULT 'milik_perusahaan',
    rental_company VARCHAR(100) NULL COMMENT 'Nama perusahaan persewaan jika kendaraan disewa',
    fuel_type ENUM('pertalite', 'pertamax', 'solar', 'dex') NOT NULL DEFAULT 'solar',
    status ENUM('tersedia', 'dalam_perjalanan', 'service', 'tidak_aktif') DEFAULT 'tersedia',
    current_mileage INT DEFAULT 0,
    location_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_type_id) REFERENCES vehicle_types(id),
    FOREIGN KEY (location_id) REFERENCES locations(id)
) ENGINE=InnoDB;

-- =====================================================
-- TABEL DRIVERS (Data Driver)
-- =====================================================
CREATE TABLE drivers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    license_number VARCHAR(50),
    license_type ENUM('A', 'B1', 'B2', 'C', 'D') NOT NULL DEFAULT 'B2',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- TABEL FUEL_LOGS (Konsumsi BBM)
-- =====================================================
CREATE TABLE fuel_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    fuel_date DATE NOT NULL,
    fuel_type ENUM('pertalite', 'pertamax', 'solar', 'dex') NOT NULL,
    liters DECIMAL(10, 2) NOT NULL,
    total_cost DECIMAL(15, 2) NOT NULL,
    mileage_at_fuel INT COMMENT 'Odometer saat pengisian BBM',
    station VARCHAR(100),
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB;

-- =====================================================
-- TABEL SERVICE_LOGS (Jadwal & Riwayat Service)
-- =====================================================
CREATE TABLE service_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    service_type ENUM('routine', 'repair', 'emergency') NOT NULL DEFAULT 'routine',
    service_date DATE NOT NULL,
    next_service_date DATE NULL,
    next_service_mileage INT NULL,
    description TEXT NOT NULL,
    cost DECIMAL(15, 2) DEFAULT 0,
    workshop VARCHAR(100),
    mileage_at_service INT,
    status ENUM('scheduled', 'in_progress', 'completed') DEFAULT 'scheduled',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
) ENGINE=InnoDB;

-- =====================================================
-- TABEL BOOKINGS (Pemesanan Kendaraan)
-- =====================================================
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_code VARCHAR(20) NOT NULL UNIQUE,
    vehicle_id INT NOT NULL,
    driver_id INT NOT NULL,
    requester_id INT NOT NULL COMMENT 'User yang memesan kendaraan',
    purpose TEXT NOT NULL COMMENT 'Keperluan pemakaian kendaraan',
    origin VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    departure_date DATE NOT NULL,
    departure_time TIME NOT NULL,
    return_date DATE NULL,
    return_time TIME NULL,
    estimated_return_date DATE NULL,
    estimated_return_time TIME NULL,
    status ENUM('pending', 'approved_level1', 'approved_level2', 'rejected', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    rejection_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
    FOREIGN KEY (driver_id) REFERENCES drivers(id),
    FOREIGN KEY (requester_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- =====================================================
-- TABEL BOOKING_APPROVALS (Persetujuan Berjenjang)
-- =====================================================
CREATE TABLE booking_approvals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    approver_id INT NOT NULL COMMENT 'User yang menyetujui',
    approval_level INT NOT NULL COMMENT 'Level persetujuan (1 atau 2)',
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    notes TEXT NULL,
    approved_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (approver_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- =====================================================
-- TABEL APPLICATION_LOGS (Log Aktivitas)
-- =====================================================
CREATE TABLE application_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

-- =====================================================
-- SEED DATA: Users
-- =====================================================
INSERT INTO users (username, password, fullname, email, role, is_active) VALUES
('admin', '$2y$10$5JCx.eB795LNGD8KhbrH4eTbEdCnYNrWp6KsQJJ4MclH6qIEV3Riy', 'Administrator', 'admin@tambang.com', 'admin', 1),
('approver1', '$2y$10$5JCx.eB795LNGD8KhbrH4eTbEdCnYNrWp6KsQJJ4MclH6qIEV3Riy', 'Budi Santoso', 'budi@tambang.com', 'approver_level1', 1),
('approver2', '$2y$10$5JCx.eB795LNGD8KhbrH4eTbEdCnYNrWp6KsQJJ4MclH6qIEV3Riy', 'Siti Rahayu', 'siti@tambang.com', 'approver_level2', 1);

-- Password semua user: password

-- =====================================================
-- SEED DATA: Locations
-- =====================================================
INSERT INTO locations (name, type, address) VALUES
('Kantor Pusat Jakarta', 'kantor_pusat', 'Jl. Sudirman No. 100, Jakarta Pusat'),
('Kantor Cabang Surabaya', 'kantor_cabang', 'Jl. Pemuda No. 50, Surabaya'),
('Tambang Morowali', 'tambang', 'Morowali, Sulawesi Tengah'),
('Tambang Halmahera', 'tambang', 'Halmahera, Maluku Utara'),
('Tambang Wajo', 'tambang', 'Wajo, Sulawesi Selatan'),
('Tambang Kolaka', 'tambang', 'Kolaka, Sulawesi Tenggara'),
('Tambang North Konawe', 'tambang', 'Konawe Utara, Sulawesi Tenggara'),
('Tambang South Konawe', 'tambang', 'Konawe Selatan, Sulawesi Tenggara');

-- =====================================================
-- SEED DATA: Vehicle Types
-- =====================================================
INSERT INTO vehicle_types (name, description) VALUES
('Bus Mini', 'Kendaraan angkutan orang (kapasitas 16 orang)'),
('Pickup', 'Kendaraan angkutan barang ringan'),
('Dump Truck', 'Kendaraan angkutan barang berat'),
('SUV', 'Kendaraan operasional penumpang'),
('Motor', 'Kendaraan roda dua untuk operasional');

-- =====================================================
-- SEED DATA: Drivers
-- =====================================================
INSERT INTO drivers (name, phone, license_number, license_type, is_active) VALUES
('Ahmad Fauzi', '081234567890', 'SIM-A-001', 'A', 1),
('Dedi Kurniawan', '081234567891', 'SIM-B2-002', 'B2', 1),
('Rizky Pratama', '081234567892', 'SIM-B2-003', 'B2', 1),
('Hendra Wijaya', '081234567893', 'SIM-C-004', 'C', 1),
('Andi Saputra', '081234567894', 'SIM-B1-005', 'B1', 1);

-- =====================================================
-- SEED DATA: Vehicles
-- =====================================================
INSERT INTO vehicles (plate_number, vehicle_type_id, brand, model, year, color, ownership, fuel_type, status, current_mileage, location_id) VALUES
('B 1234 ABC', 1, 'Toyota', 'HiAce', 2022, 'Putih', 'milik_perusahaan', 'solar', 'tersedia', 45000, 1),
('B 2345 DEF', 2, 'Mitsubishi', 'Triton', 2023, 'Hitam', 'milik_perusahaan', 'solar', 'tersedia', 23000, 2),
('B 3456 GHI', 3, 'Hino', 'Ranger', 2021, 'Kuning', 'milik_perusahaan', 'solar', 'tersedia', 67000, 3),
('B 4567 JKL', 4, 'Toyota', 'Fortuner', 2023, 'Silver', 'milik_perusahaan', 'pertamax', 'tersedia', 15000, 1),
('B 5678 MNO', 5, 'Honda', 'CRF150', 2023, 'Merah', 'milik_perusahaan', 'pertalite', 'tersedia', 8000, 3),
('B 6789 PQR', 1, 'Isuzu', 'Elf', 2022, 'Biru', 'sewa', 'solar', 'tersedia', 52000, 4),
('B 7890 STU', 2, 'Toyota', 'Hilux', 2023, 'Putih', 'sewa', 'solar', 'tersedia', 19000, 5),
('B 8901 UVW', 3, 'Mitsubishi', 'Canter', 2022, 'Oranye', 'sewa', 'solar', 'dalam_perjalanan', 38000, 6),
('B 9012 XYZ', 4, 'Suzuki', 'Jimny', 2024, 'Hijau', 'milik_perusahaan', 'pertalite', 'tersedia', 5000, 7),
('B 0123 ABC', 4, 'Honda', 'BR-V', 2023, 'Putih', 'milik_perusahaan', 'pertalite', 'service', 21000, 8);

-- =====================================================
-- SEED DATA: Sample Fuel Logs
-- =====================================================
INSERT INTO fuel_logs (vehicle_id, fuel_date, fuel_type, liters, total_cost, mileage_at_fuel, station, created_by) VALUES
(1, '2026-08-01', 'solar', 60.5, 665500, 44500, 'SPBU Morowali', 1),
(1, '2026-08-10', 'solar', 55.0, 605000, 44800, 'SPBU Morowali', 1),
(1, '2026-08-20', 'solar', 58.3, 641300, 45000, 'SPBU Jakarta', 1),
(2, '2026-08-05', 'solar', 45.2, 497200, 22800, 'SPBU Surabaya', 1),
(2, '2026-08-15', 'solar', 42.0, 462000, 23000, 'SPBU Surabaya', 1),
(3, '2026-08-02', 'solar', 120.5, 1325500, 66500, 'SPBU Tambang', 1),
(3, '2026-08-12', 'solar', 115.0, 1265000, 67000, 'SPBU Tambang', 1),
(4, '2026-08-03', 'pertamax', 40.0, 560000, 14800, 'SPBU Jakarta', 1),
(4, '2026-08-18', 'pertamax', 38.5, 539000, 15000, 'SPBU Jakarta', 1),
(5, '2026-08-08', 'pertalite', 8.5, 76500, 7800, 'SPBU Tambang', 1),
(5, '2026-08-22', 'pertalite', 9.0, 81000, 8000, 'SPBU Tambang', 1),
(6, '2026-08-07', 'solar', 70.0, 770000, 51500, 'SPBU Halmahera', 1),
(6, '2026-08-17', 'solar', 65.5, 720500, 52000, 'SPBU Halmahera', 1),
(7, '2026-08-04', 'solar', 38.0, 418000, 18800, 'SPBU Wajo', 1),
(7, '2026-08-14', 'solar', 36.5, 401500, 19000, 'SPBU Wajo', 1),
(8, '2026-08-06', 'solar', 95.0, 1045000, 37500, 'SPBU Kolaka', 1),
(9, '2026-08-11', 'pertalite', 12.0, 108000, 4800, 'SPBU Konawe', 1),
(9, '2026-08-25', 'pertalite', 10.5, 94500, 5000, 'SPBU Konawe', 1),
(10, '2026-08-09', 'pertalite', 30.0, 270000, 20800, 'SPBU Selatan', 1),
(10, '2026-08-23', 'pertalite', 28.5, 256500, 21000, 'SPBU Selatan', 1);

-- =====================================================
-- SEED DATA: Sample Service Logs
-- =====================================================
INSERT INTO service_logs (vehicle_id, service_type, service_date, next_service_date, next_service_mileage, description, cost, workshop, mileage_at_service, status) VALUES
(1, 'routine', '2026-07-01', '2026-10-01', 50000, 'Service rutin: ganti oli, filter udara, cek rem', 2500000, 'Workshop Utama Jakarta', 40000, 'completed'),
(2, 'routine', '2026-07-15', '2026-10-15', 30000, 'Service rutin: ganti oli, cek mesin', 2000000, 'Workshop Surabaya', 20000, 'completed'),
(3, 'repair', '2026-08-05', NULL, NULL, 'Perbaikan sistem rem depan', 5000000, 'Workshop Tambang', 66500, 'completed'),
(4, 'routine', '2026-06-20', '2026-09-20', 20000, 'Service rutin: ganti oli mesin + transmisi', 3000000, 'Workshop Utama Jakarta', 12000, 'completed'),
(5, 'routine', '2026-08-01', '2026-11-01', 10000, 'Service rutin: ganti oli, cek rantai', 500000, 'Workshop Tambang', 7500, 'completed'),
(10, 'routine', '2026-08-25', '2026-11-25', 26000, 'Service berkala: ganti oli + filter', 1500000, 'Workshop Selatan', 21000, 'in_progress');

-- =====================================================
-- SEED DATA: Sample Bookings
-- =====================================================
INSERT INTO bookings (booking_code, vehicle_id, driver_id, requester_id, purpose, origin, destination, departure_date, departure_time, return_date, return_time, status) VALUES
('BK-20260801-001', 1, 1, 1, 'Mengangkut karyawan ke lokasi tambang Morowali', 'Kantor Pusat Jakarta', 'Tambang Morowali', '2026-08-01', '07:00:00', '2026-08-01', '17:00:00', 'completed'),
('BK-20260805-002', 2, 2, 1, 'Pengiriman peralatan ke tambang', 'Kantor Cabang Surabaya', 'Tambang Wajo', '2026-08-05', '06:30:00', '2026-08-05', '16:00:00', 'completed'),
('BK-20260810-003', 4, 3, 1, 'Inspeksi lokasi tambang Halmahera', 'Kantor Pusat Jakarta', 'Tambang Halmahera', '2026-08-10', '08:00:00', '2026-08-12', '18:00:00', 'completed'),
('BK-20260815-004', 6, 1, 1, 'Mengangkut tim survey ke lokasi', 'Tambang Morowali', 'Tambang Kolaka', '2026-08-15', '07:30:00', NULL, NULL, 'approved_level2'),
('BK-20260820-005', 3, 4, 1, 'Pengiriman material tambang', 'Tambang Halmahera', 'Pelabuhan Tobelo', '2026-08-20', '06:00:00', NULL, NULL, 'approved_level1'),
('BK-20260825-006', 5, 5, 1, 'Patroli area tambang', 'Tambang Morowali', 'Area Operasional Timur', '2026-08-25', '07:00:00', NULL, NULL, 'pending'),
('BK-20260826-007', 7, 2, 1, 'Distribusi bantuan ke desa sekitar tambang', 'Kantor Cabang Surabaya', 'Desa Wakorumba', '2026-08-26', '08:00:00', NULL, NULL, 'pending');

-- =====================================================
-- SEED DATA: Sample Booking Approvals
-- =====================================================
INSERT INTO booking_approvals (booking_id, approver_id, approval_level, status, notes, approved_at) VALUES
(1, 2, 1, 'approved', 'Disetujui untuk pemakaian kendaraan', '2026-07-31 10:00:00'),
(1, 3, 2, 'approved', 'Disetujui, pastikan driver dalam kondisi fit', '2026-07-31 14:00:00'),
(2, 2, 1, 'approved', 'Setuju, kirim peralatan sesuai jadwal', '2026-08-04 09:00:00'),
(2, 3, 2, 'approved', 'Disetujui', '2026-08-04 11:00:00'),
(3, 2, 1, 'approved', 'Setuju inspeksi', '2026-08-09 10:00:00'),
(3, 3, 2, 'approved', 'Disetujui, siapkan dokumentasi', '2026-08-09 15:00:00'),
(4, 2, 1, 'approved', 'Setuju pengiriman tim', '2026-08-14 09:30:00'),
(4, 3, 2, 'approved', 'Disetujui', '2026-08-14 13:00:00'),
(5, 2, 1, 'approved', 'Setuju pengiriman material', '2026-08-19 08:00:00'),
(5, 3, 2, 'pending', NULL, NULL),
(6, 2, 1, 'pending', NULL, NULL),
(7, 2, 1, 'pending', NULL, NULL);

-- =====================================================
-- SEED DATA: Application Logs
-- =====================================================
INSERT INTO application_logs (user_id, action, description, ip_address) VALUES
(1, 'LOGIN', 'Admin berhasil login', '192.168.1.100'),
(1, 'CREATE_BOOKING', 'Membuat pemesanan BK-20260801-001 untuk kendaraan B 1234 ABC', '192.168.1.100'),
(2, 'APPROVE_BOOKING', 'Menyetujui pemesanan BK-20260801-001 level 1', '192.168.1.101'),
(3, 'APPROVE_BOOKING', 'Menyetujui pemesanan BK-20260801-001 level 2', '192.168.1.102'),
(1, 'CREATE_BOOKING', 'Membuat pemesanan BK-20260805-002 untuk kendaraan B 2345 DEF', '192.168.1.100'),
(2, 'APPROVE_BOOKING', 'Menyetujui pemesanan BK-20260805-002 level 1', '192.168.1.101'),
(3, 'APPROVE_BOOKING', 'Menyetujui pemesanan BK-20260805-002 level 2', '192.168.1.102');
