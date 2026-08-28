# Sistem Pemesanan Kendaraan - PT Nikel Sejahtera Mining

Aplikasi web untuk monitoring dan pemesanan kendaraan operasional perusahaan tambang nikel. Dibangun dengan CodeIgniter 4, Bootstrap 5, dan SQLite.

---

## Spesifikasi

| Komponen | Versi |
|----------|-------|
| PHP | 8.2+ |
| Database | SQLite3 |
| Framework | CodeIgniter 4.7.4 |
| Frontend | Bootstrap 5.3.2 |
| Charts | Chart.js 4.4.1 |
| DataTables | DataTables 1.13.7 |

---

## Fitur

1. **Dashboard** - Grafik pemakaian kendaraan per bulan, per status, per jenis kendaraan, dan konsumsi BBM
2. **Pemesanan Kendaraan** - Form pemesanan dengan pemilihan kendaraan, driver, dan 2 penyetuju
3. **Persetujuan Berjenjang** - Approval 2 level (Level 1 dan Level 2)
4. **Manajemen Kendaraan** - Data kendaraan, log BBM, dan log service
5. **Laporan** - Filter periode tanggal, export CSV dan Excel
6. **Log Aktivitas** - Pencatatan semua aktivitas pengguna

---

## Instalasi

```bash
# 1. Install dependencies
composer install

# 2. Setup database
# Buka: http://localhost:8080/public/setup_db.php

# 3. Jalankan server
php spark serve --port 8080

# 4. Buka aplikasi
# http://localhost:8080/index.php/auth
```

---

## Login

| Username | Password | Role |
|----------|----------|------|
| `admin` | `password` | Admin |
| `approver1` | `password` | Approver Level 1 |
| `approver2` | `password` | Approver Level 2 |

---

## Struktur

```
fleet-booking/
├── app/
│   ├── Config/          Konfigurasi aplikasi
│   ├── Controllers/     9 controllers
│   ├── Models/          10 models
│   └── Views/           12 view templates
├── writable/
│   └── fleet_booking.db Database SQLite
├── public/
│   ├── index.php        Entry point
│   ├── setup_db.php     Setup database
│   └── router.php       Router untuk PHP built-in server
├── fleet_booking.sql    SQL dump
└── README.md
```
