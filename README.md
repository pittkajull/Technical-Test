# 🚛 PT Nikel Sejahtera Mining - Sistem Pemesanan Kendaraan

Aplikasi web untuk monitoring dan pemesanan kendaraan operasional perusahaan tambang nikel. Dibangun dengan CodeIgniter 4, Bootstrap 5, dan SQLite.

---

## 🏢 Tentang Perusahaan

PT Nikel Sejahtera Mining adalah perusahaan tambang nikel yang beroperasi di beberapa wilayah Indonesia:
- **Kantor Pusat**: Jakarta Selatan
- **Kantor Cabang**: Makassar
- **Tambang**: Morowali, Halmahera Selatan, Wajo, Kolaka Utara, Konawe Utara, Bulukumba

---

## 🔐 Login Credentials

| Username | Password | Nama | Role |
|----------|----------|------|------|
| `admin` | `password` | Administrator | Admin |
| `approver1` | `password` | Budi Santoso | Approver Level 1 |
| `approver2` | `password` | Siti Rahayu | Approver Level 2 |

---

## 💻 Spesifikasi Teknis

| Komponen | Versi |
|----------|-------|
| **PHP** | 8.2+ |
| **Database** | SQLite3 (built-in) |
| **Framework** | CodeIgniter 4.7.4 |
| **Frontend** | Bootstrap 5.3.2 |
| **Charts** | Chart.js 4.4.1 |
| **DataTables** | DataTables 1.13.7 |

---

## 🛠️ Instalasi

### Prasyarat
- PHP 8.1+ dengan extension: `pdo_sqlite`, `sqlite3`, `mbstring`
- Composer

### Langkah

```bash
# 1. Copy project
cp -r fleet-booking /path/to/webroot/
cd /path/to/webroot/fleet-booking

# 2. Install dependencies
composer install

# 3. Setup database (buka di browser)
# http://localhost/fleet-booking/public/setup_db.php

# 4. Jalankan server
php spark serve --port 8080

# 5. Buka aplikasi
# http://localhost:8080/index.php/auth
```

---

## Fitur Aplikasi

### 1. Dashboard
- Grafik pemesanan kendaraan per bulan
- Grafik status pemesanan
- Grafik pemesanan per jenis kendaraan
- Grafik konsumsi BBM per bulan
- Statistik kendaraan & driver

### 2. Pemesanan Kendaraan
- Form pemesanan lengkap
- Pilih kendaraan tersedia & driver
- Pilih 2 penyetuju (Level 1 & 2)
- Status otomatis berjenjang

### 3. Persetujuan Berjenjang
- Level 1: Budi Santoso
- Level 2: Siti Rahayu
- Catatan pada setiap persetujuan

### 4. Manajemen Kendaraan
- 10 kendaraan (6 milik perusahaan + 4 sewa)
- Log pengisian BBM
- Log service kendaraan

### 5. Laporan
- Filter periode tanggal
- Export CSV & Excel

### 6. Log Aktivitas
- Pencatatan semua aktivitas pengguna

---

## 📊 Data Master

| Data | Jumlah | Keterangan |
|------|--------|------------|
| Users | 3 | 1 admin, 2 approver (Level 1 & 2) |
| Lokasi | 8 | 1 pusat, 1 cabang, 6 tambang |
| Jenis Kendaraan | 5 | Bus Mini, Pickup, Dump Truck, SUV, Motor |
| Driver | 5 | SIM A, B1, B2, C |
| Kendaraan | 10 | 6 milik perusahaan + 4 sewa |
| Bookings | 7 | 3 completed, 2 approved, 2 pending |

---

## 📁 Struktur

```
fleet-booking/
├── app/
│   ├── Config/          (45 config files + Boot/)
│   ├── Controllers/     (9 controllers)
│   ├── Models/          (10 models)
│   ├── Views/           (12 view templates)
│   └── Database/        (Migrations & Seeds)
├── writable/
│   └── fleet_booking.db (SQLite database)
├── public/
│   ├── index.php        (Entry point)
│   ├── setup_db.php     (Database setup)
│   └── router.php       (PHP built-in server router)
├── tests/               (PHPUnit tests)
├── fleet_booking.sql    (SQL dump reference)
├── composer.json
└── README.md
```

---

**PT Nikel Sejahtera Mining** © 2026
