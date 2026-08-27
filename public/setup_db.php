<?php
/**
 * Database Setup Script - Clean Version
 * PT Nikel Sejahtera Mining
 * Hanya buat schema + user login saja
 */

$dbPath = __DIR__ . '/../writable/fleet_booking.db';

if (file_exists($dbPath)) {
    unlink($dbPath);
}

try {
    $db = new SQLite3($dbPath);
    $db->enableExceptions(true);
    
    echo "<h1>✅ Database Setup - PT Nikel Sejahtera Mining</h1>";
    
    // ===== SCHEMA =====
    $db->exec("CREATE TABLE users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        fullname TEXT NOT NULL,
        email TEXT,
        role TEXT NOT NULL CHECK(role IN ('admin', 'approver_level1', 'approver_level2')),
        is_active INTEGER DEFAULT 1,
        last_login TEXT,
        created_at TEXT DEFAULT (datetime('now')),
        updated_at TEXT DEFAULT (datetime('now'))
    )");

    $db->exec("CREATE TABLE locations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        type TEXT NOT NULL CHECK(type IN ('kantor_pusat', 'kantor_cabang', 'tambang')),
        address TEXT,
        latitude REAL,
        longitude REAL,
        created_at TEXT DEFAULT (datetime('now'))
    )");

    $db->exec("CREATE TABLE vehicle_types (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        description TEXT
    )");

    $db->exec("CREATE TABLE vehicles (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        plate_number TEXT NOT NULL UNIQUE,
        vehicle_type_id INTEGER NOT NULL,
        brand TEXT,
        model TEXT,
        year INTEGER,
        color TEXT,
        ownership TEXT NOT NULL CHECK(ownership IN ('milik_perusahaan', 'sewa')),
        rental_company TEXT,
        fuel_type TEXT NOT NULL CHECK(fuel_type IN ('pertalite', 'pertamax', 'solar', 'dex')),
        status TEXT DEFAULT 'tersedia' CHECK(status IN ('tersedia', 'dalam_perjalanan', 'service', 'tidak_aktif')),
        current_mileage INTEGER DEFAULT 0,
        location_id INTEGER,
        created_at TEXT DEFAULT (datetime('now')),
        updated_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (vehicle_type_id) REFERENCES vehicle_types(id),
        FOREIGN KEY (location_id) REFERENCES locations(id)
    )");

    $db->exec("CREATE TABLE drivers (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        phone TEXT,
        license_number TEXT,
        license_type TEXT NOT NULL CHECK(license_type IN ('A', 'B1', 'B2', 'C', 'D')),
        is_active INTEGER DEFAULT 1,
        created_at TEXT DEFAULT (datetime('now'))
    )");

    $db->exec("CREATE TABLE fuel_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        vehicle_id INTEGER NOT NULL,
        fuel_date TEXT NOT NULL,
        fuel_type TEXT NOT NULL,
        liters REAL NOT NULL,
        total_cost REAL NOT NULL,
        mileage_at_fuel INTEGER,
        station TEXT,
        notes TEXT,
        created_by INTEGER,
        created_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
        FOREIGN KEY (created_by) REFERENCES users(id)
    )");

    $db->exec("CREATE TABLE service_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        vehicle_id INTEGER NOT NULL,
        service_type TEXT NOT NULL CHECK(service_type IN ('routine', 'repair', 'emergency')),
        service_date TEXT NOT NULL,
        next_service_date TEXT,
        next_service_mileage INTEGER,
        description TEXT NOT NULL,
        cost REAL DEFAULT 0,
        workshop TEXT,
        mileage_at_service INTEGER,
        status TEXT DEFAULT 'scheduled' CHECK(status IN ('scheduled', 'in_progress', 'completed')),
        created_by INTEGER,
        created_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
        FOREIGN KEY (created_by) REFERENCES users(id)
    )");

    $db->exec("CREATE TABLE bookings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        booking_code TEXT NOT NULL UNIQUE,
        vehicle_id INTEGER NOT NULL,
        driver_id INTEGER NOT NULL,
        requester_id INTEGER NOT NULL,
        purpose TEXT NOT NULL,
        origin TEXT NOT NULL,
        destination TEXT NOT NULL,
        departure_date TEXT NOT NULL,
        departure_time TEXT NOT NULL,
        return_date TEXT,
        return_time TEXT,
        estimated_return_date TEXT,
        estimated_return_time TEXT,
        status TEXT DEFAULT 'pending' CHECK(status IN ('pending', 'approved_level1', 'approved_level2', 'rejected', 'in_progress', 'completed', 'cancelled')),
        rejection_reason TEXT,
        created_at TEXT DEFAULT (datetime('now')),
        updated_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (vehicle_id) REFERENCES vehicles(id),
        FOREIGN KEY (driver_id) REFERENCES drivers(id),
        FOREIGN KEY (requester_id) REFERENCES users(id)
    )");

    $db->exec("CREATE TABLE booking_approvals (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        booking_id INTEGER NOT NULL,
        approver_id INTEGER NOT NULL,
        approval_level INTEGER NOT NULL,
        status TEXT DEFAULT 'pending' CHECK(status IN ('pending', 'approved', 'rejected')),
        notes TEXT,
        approved_at TEXT,
        created_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
        FOREIGN KEY (approver_id) REFERENCES users(id)
    )");

    $db->exec("CREATE TABLE application_logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        action TEXT NOT NULL,
        description TEXT NOT NULL,
        ip_address TEXT,
        user_agent TEXT,
        created_at TEXT DEFAULT (datetime('now')),
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");
    echo "<p>✓ Semua tabel dibuat</p>";

    // ===== USERS ONLY =====
    $hashedPassword = password_hash('password', PASSWORD_BCRYPT);

    $stmt = $db->prepare("INSERT INTO users (username, password, fullname, email, role) VALUES (:u, :p, :f, :e, :r)");
    $users = [
        ['admin', 'Setiawan Budiman', 'admin@nikelseahtera.co.id', 'admin'],
        ['approver1', 'Ir. Bambang Sutrisno', 'bambang@nikelseahtera.co.id', 'approver_level1'],
        ['approver2', 'Ir. Dewi Kartika', 'dewi@nikelseahtera.co.id', 'approver_level2'],
    ];
    foreach ($users as $u) {
        $stmt->bindValue(':u', $u[0]); $stmt->bindValue(':p', $hashedPassword);
        $stmt->bindValue(':f', $u[1]); $stmt->bindValue(':e', $u[2]); $stmt->bindValue(':r', $u[3]);
        $stmt->execute(); $stmt->reset();
    }
    echo "<p>✓ 3 users (admin, approver1, approver2)</p>";

    // ===== DRIVERS =====
    $stmt = $db->prepare("INSERT INTO drivers (name, phone, license_number, license_type) VALUES (:n, :p, :l, :t)");
    $drivers = [
        ['Ahmad Fauzi', '0812-3456-7890', 'SIM-B2-001', 'B2'],
        ['Dedi Kurniawan', '0813-4567-8901', 'SIM-B2-002', 'B2'],
        ['Rizky Pratama', '0815-5678-9012', 'SIM-C-003', 'C'],
        ['Hendra Wijaya', '0816-6789-0123', 'SIM-A-004', 'A'],
        ['Andi Saputra', '0817-7890-1234', 'SIM-B1-005', 'B1'],
    ];
    foreach ($drivers as $d) {
        $stmt->bindValue(':n', $d[0]); $stmt->bindValue(':p', $d[1]);
        $stmt->bindValue(':l', $d[2]); $stmt->bindValue(':t', $d[3]);
        $stmt->execute(); $stmt->reset();
    }
    echo "<p>✓ 5 drivers</p>";

    // ===== VEHICLE TYPES =====
    $stmt = $db->prepare("INSERT INTO vehicle_types (name, description) VALUES (:n, :d)");
    $types = [
        ['Bus Mini', 'Kendaraan angkutan karyawan (kapasitas 16 orang)'],
        ['Pickup', 'Kendaraan angkutan barang ringan'],
        ['Dump Truck', 'Kendaraan angkutan ore/nikel berat'],
        ['SUV Operasional', 'Kendaraan operasional pejabat/manajemen'],
        ['Motor Dinasti', 'Sepeda motor untuk operasional lapangan'],
    ];
    foreach ($types as $t) {
        $stmt->bindValue(':n', $t[0]); $stmt->bindValue(':d', $t[1]);
        $stmt->execute(); $stmt->reset();
    }
    echo "<p>✓ 5 jenis kendaraan</p>";

    // ===== VEHICLES =====
    $stmt = $db->prepare("INSERT INTO vehicles (plate_number, vehicle_type_id, brand, model, year, color, ownership, fuel_type, status, current_mileage) VALUES (:plate, :tid, :brand, :model, :year, :color, :own, :fuel, :status, :km)");
    $vehicles = [
        ['B 1234 NSA', 1, 'Toyota', 'HiAce Commuter', 2022, 'Putih', 'milik_perusahaan', 'solar', 'tersedia', 45200],
        ['B 2345 NSA', 2, 'Mitsubishi', 'Triton DC', 2023, 'Hitam', 'milik_perusahaan', 'solar', 'tersedia', 18500],
        ['B 3456 NSA', 3, 'Hino', 'Ranger 500', 2021, 'Kuning', 'milik_perusahaan', 'solar', 'tersedia', 67800],
        ['B 4567 NSA', 4, 'Toyota', 'Fortuner VRZ', 2023, 'Silver', 'milik_perusahaan', 'pertamax', 'tersedia', 22100],
        ['B 5678 NSA', 5, 'Honda', 'CRF150L', 2024, 'Merah', 'milik_perusahaan', 'pertalite', 'tersedia', 8900],
    ];
    foreach ($vehicles as $v) {
        $stmt->bindValue(':plate', $v[0]);
        $stmt->bindValue(':tid', $v[1], SQLITE3_INTEGER);
        $stmt->bindValue(':brand', $v[2]);
        $stmt->bindValue(':model', $v[3]);
        $stmt->bindValue(':year', $v[4], SQLITE3_INTEGER);
        $stmt->bindValue(':color', $v[5]);
        $stmt->bindValue(':own', $v[6]);
        $stmt->bindValue(':fuel', $v[7]);
        $stmt->bindValue(':status', $v[8]);
        $stmt->bindValue(':km', $v[9], SQLITE3_INTEGER);
        $stmt->execute(); $stmt->reset();
    }
    echo "<p>✓ 5 kendaraan</p>";

    $db->close();

    echo "<hr>";
    echo "<h2>✅ Database Bersih Siap Digunakan!</h2>";
    echo "<p>Semua tabel sudah dibuat kosong. Silakan input data melalui aplikasi.</p>";
    
    echo "<h3>🔐 Login:</h3>";
    echo "<table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'><th>Username</th><th>Password</th><th>Role</th></tr>";
    echo "<tr><td>admin</td><td>password</td><td>Admin</td></tr>";
    echo "<tr><td>approver1</td><td>password</td><td>Approver Level 1</td></tr>";
    echo "<tr><td>approver2</td><td>password</td><td>Approver Level 2</td></tr>";
    echo "</table>";
    
    echo "<br><a href='../index.php/auth' style='display: inline-block; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px;'>→ Buka Aplikasi</a>";

} catch (Exception $e) {
    echo "<h1>❌ Error!</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
}
