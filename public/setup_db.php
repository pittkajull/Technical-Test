<?php
/**
 * Database Setup Script - PT Nikel Sejahtera Mining
 * 
 * Mengimport langsung dari fleet_booking.sql (satu sumber data)
 * File ini OTOMATIS sync dengan SQL dump, jadi data selalu konsisten.
 */

$sqlFile = __DIR__ . '/../fleet_booking.sql';
$dbPath = __DIR__ . '/../writable/fleet_booking.db';

echo "<!DOCTYPE html>";
echo "<html lang='id'><head><meta charset='UTF-8'><title>Database Setup</title>";
echo "<style>
    body { font-family: 'Segoe UI', sans-serif; max-width: 700px; margin: 40px auto; padding: 20px; background: #f5f5f5; }
    .card { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .success { color: #27ae60; } .error { color: #e74c3c; } .info { color: #3498db; }
    table { border-collapse: collapse; width: 100%; margin: 15px 0; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    th { background: #f8f9fa; }
    .btn { display: inline-block; padding: 12px 24px; background: #3498db; color: white; text-decoration: none; border-radius: 8px; margin-top: 15px; }
    .btn:hover { background: #2980b9; }
</style></head><body><div class='card'>";

echo "<h1>🚛 Database Setup - PT Nikel Sejahtera Mining</h1>";

// Cek file SQL
if (!file_exists($sqlFile)) {
    echo "<p class='error'>❌ File fleet_booking.sql tidak ditemukan!</p>";
    echo "<p>Pastikan file <code>fleet_booking.sql</code> ada di root project.</p>";
    echo "</div></body></html>";
    exit(1);
}

echo "<p class='info'>📄 Sumber data: <code>fleet_booking.sql</code></p>";

// Hapus database lama jika ada
if (file_exists($dbPath)) {
    unlink($dbPath);
    echo "<p>🗑️ Database lama dihapus</p>";
}

// Buat database baru
try {
    $db = new SQLite3($dbPath);
    $db->enableExceptions(true);
    $db->exec('PRAGMA foreign_keys = ON');
    
    echo "<p>📦 Database baru dibuat: <code>writable/fleet_booking.db</code></p>";
    
    // Baca dan eksekusi SQL file
    $sql = file_get_contents($sqlFile);
    
    // Split per statement (handle multiple statements)
    // SQLite3::exec() hanya bisa execute satu statement
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($s) { return !empty($s) && strpos($s, '--') !== 0; }
    );
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        // Skip comments-only lines
        $cleanStatement = preg_replace('/--.*$/m', '', $statement);
        $cleanStatement = trim($cleanStatement);
        if (empty($cleanStatement)) continue;
        
        try {
            $db->exec($statement);
            $successCount++;
        } catch (Exception $e) {
            $errorCount++;
            // Skip duplicate table errors (normal on re-run)
            if (strpos($e->getMessage(), 'already exists') === false) {
                echo "<p class='error'>⚠️ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
        }
    }
    
    echo "<p class='success'>✅ Import selesai: $successCount statements berhasil, $errorCount errors (normal jika table sudah ada)</p>";
    
    // Verifikasi data
    echo "<hr><h2>📊 Data yang diimport:</h2>";
    
    $tables = [
        'users' => '👤 Users',
        'locations' => '📍 Lokasi',
        'vehicle_types' => '🚗 Jenis Kendaraan',
        'vehicles' => '🚛 Kendaraan',
        'drivers' => '👨‍✈️ Driver',
        'fuel_logs' => '⛽ Log BBM',
        'service_logs' => '🔧 Log Service',
        'bookings' => '📋 Pemesanan',
        'booking_approvals' => '✅ Persetujuan',
        'application_logs' => '📝 Log Aktivitas'
    ];
    
    echo "<table>";
    echo "<tr><th>Table</th><th>Jumlah Data</th></tr>";
    
    foreach ($tables as $table => $label) {
        $result = $db->querySingle("SELECT COUNT(*) FROM $table");
        echo "<tr><td>$label</td><td><strong>$result</strong> records</td></tr>";
    }
    echo "</table>";
    
    // Tampilkan info login
    echo "<hr><h2>🔐 Login Credentials:</h2>";
    echo "<table>";
    echo "<tr><th>Username</th><th>Password</th><th>Nama</th><th>Role</th></tr>";
    
    $users = $db->query("SELECT username, fullname, role FROM users ORDER BY id");
    while ($user = $users->fetchArray(SQLITE3_ASSOC)) {
        echo "<tr>";
        echo "<td><code>" . htmlspecialchars($user['username']) . "</code></td>";
        echo "<td><code>password</code></td>";
        echo "<td>" . htmlspecialchars($user['fullname']) . "</td>";
        echo "<td>" . htmlspecialchars($user['role']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    $db->close();
    
    echo "<hr>";
    echo "<h2 class='success'>✅ Database Siap Digunakan!</h2>";
    echo "<p>Semua data sudah diimport dari <code>fleet_booking.sql</code>.</p>";
    echo "<a class='btn' href='../index.php/auth'>→ Buka Aplikasi</a>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "</div></body></html>";
