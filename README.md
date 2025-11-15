# Sistem Manajemen RT-RW Net

Sistem manajemen RT-RW Net berbasis Laravel untuk mengelola pelanggan, paket internet, tagihan, dan pembayaran secara terintegrasi.

## 📋 Deskripsi

Sistem ini dirancang untuk membantu pengelola RT-RW Net dalam mengelola:
- Data pelanggan dan paket internet
- Tagihan bulanan otomatis
- Pembayaran dan verifikasi
- Permintaan upgrade paket
- Monitoring dan laporan

## ✨ Fitur Utama

### 👨‍💼 Panel Admin
- **Manajemen Paket Internet**
  - CRUD paket (nama, harga bulanan, harga pemasangan, kecepatan)
  - Status aktif/nonaktif paket
  
- **Manajemen Pelanggan**
  - Buat akun pelanggan baru beserta paketnya
  - Edit dan hapus data pelanggan
  - Auto-generate tagihan pemasangan pertama
  
- **Manajemen Tagihan**
  - Buat tagihan manual (bulanan, upgrade, pemasangan)
  - Filter tagihan berdasarkan status, jenis, dan pelanggan
  - Monitoring tagihan belum bayar
  
- **Verifikasi Pembayaran**
  - Lihat bukti pembayaran yang diupload pelanggan
  - Verifikasi dan konfirmasi pembayaran
  - Tolak pembayaran dengan catatan
  
- **Kelola Permintaan Upgrade**
  - Lihat permintaan upgrade paket dari pelanggan
  - Setujui/tolak permintaan upgrade
  - Auto-generate tagihan upgrade saat disetujui

### 👤 Panel Pelanggan
- **Dashboard**
  - Lihat tagihan bulan ini
  - Total tagihan belum bayar
  - Riwayat pembayaran terbaru
  
- **Tagihan**
  - Lihat semua tagihan (bulanan, upgrade, pemasangan)
  - Detail tagihan per item
  
- **Pembayaran**
  - Upload bukti pembayaran
  - Lihat status pembayaran (menunggu verifikasi, lunas, ditolak)
  - Riwayat pembayaran
  
- **Permintaan Upgrade Paket**
  - Request upgrade/downgrade paket
  - Lihat status permintaan
  - Riwayat permintaan upgrade

## 🛠️ Teknologi yang Digunakan

- **Backend Framework**: Laravel 11
- **Frontend**: 
  - Blade Templates
  - Tailwind CSS
  - Alpine.js
- **Database**: MySQL
- **PHP Version**: ^8.2

## 📦 Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL
- Web Server (Apache/Nginx) atau Laragon/XAMPP

### Langkah Instalasi

1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd rt-rw-net-v2backup
   ```

2. **Install Dependencies**
   ```bash
   # Install PHP dependencies
   composer install
   
   # Install Node dependencies
   npm install
   ```

3. **Konfigurasi Environment**
   ```bash
   # Copy file .env
   cp .env.example .env
   
   # Generate application key
   php artisan key:generate
   ```

4. **Konfigurasi Database**
   
   Edit file `.env` dan sesuaikan konfigurasi database:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=rtrwnetv3
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Jalankan Migrasi**
   ```bash
   php artisan migrate
   ```

   Atau jika ingin menggunakan data sample dari file SQL:
   ```bash
   # Import database dari file rtrwnetv3.sql
   mysql -u root -p rtrwnetv3 < rtrwnetv3.sql
   ```

6. **Build Assets**
   ```bash
   npm run build
   # atau untuk development
   npm run dev
   ```

7. **Jalankan Server**
   ```bash
   php artisan serve
   ```

   Aplikasi akan berjalan di `http://localhost:8000`

## 👥 User Default

Setelah import database, gunakan kredensial berikut:

**Admin:**
- Email: `admin@gmail.com`
- Password: `password` (atau sesuai yang ada di database)

**Pelanggan:**
- Email: `farith@gmail.com`
- Password: `password` (atau sesuai yang ada di database)

> ⚠️ **Penting**: Ganti password default setelah instalasi pertama!

## 🗄️ Struktur Database

### Tabel Utama

- **users**: Data user (admin & pelanggan)
- **pelanggan**: Data pelanggan dengan `pelanggan_id` sebagai primary key
  - Format `pelanggan_id`: `[id][user_id][paket_id]` (contoh: "122")
- **paket**: Paket internet yang tersedia
- **tagihan**: Tagihan (bulanan, upgrade, pemasangan)
- **pembayaran**: Data pembayaran dengan bukti upload
- **permintaan_upgrade**: Permintaan upgrade/downgrade paket

### Format Pelanggan ID

Sistem menggunakan format khusus untuk `pelanggan_id`:
- **Format**: `[id][user_id][paket_id]`
- **Contoh**: 
  - `"122"` = ID pelanggan 1, User ID 2, Paket ID 2
  - `"231"` = ID pelanggan 2, User ID 3, Paket ID 1

> **Catatan**: Format ini memungkinkan maksimal 9 untuk masing-masing ID (1 digit per bagian).

## 🔐 Autentikasi & Authorization

Sistem menggunakan 2 role:
- **Admin**: Akses penuh ke semua fitur
- **Pelanggan**: Hanya akses ke fitur pelanggan

Middleware:
- `EnsureUserIsAdmin`: Membatasi akses hanya untuk admin
- `EnsureUserIsPelanggan`: Membatasi akses hanya untuk pelanggan

## 📝 Alur Kerja

### 1. Pemasangan Baru
1. Admin membuat akun pelanggan baru dan memilih paket
2. Sistem otomatis membuat tagihan pemasangan (harga pemasangan + harga paket)
3. Pelanggan melakukan pemasangan
4. Admin menandai tagihan pemasangan sebagai lunas

### 2. Tagihan Bulanan
1. Admin membuat tagihan bulanan (atau otomatis via scheduler)
2. Pelanggan melihat tagihan di dashboard
3. Pelanggan upload bukti pembayaran
4. Admin verifikasi pembayaran
5. Status tagihan otomatis menjadi "lunas" setelah verifikasi

### 3. Upgrade Paket
1. Pelanggan request upgrade paket melalui panel
2. Admin review permintaan
3. Admin setujui/tolak permintaan
4. Jika disetujui:
   - Sistem update paket pelanggan
   - Generate `pelanggan_id` baru
   - Buat tagihan upgrade (selisih harga bulanan)

## 🚀 Penggunaan

### Untuk Admin

1. **Login** dengan kredensial admin
2. **Dashboard**: Lihat statistik dan notifikasi
3. **Manajemen Paket**: Kelola paket internet
4. **Manajemen Pelanggan**: Tambah/edit pelanggan
5. **Tagihan**: Buat dan kelola tagihan
6. **Pembayaran**: Verifikasi pembayaran pelanggan
7. **Permintaan Upgrade**: Kelola permintaan upgrade

### Untuk Pelanggan

1. **Login** dengan kredensial pelanggan
2. **Dashboard**: Lihat tagihan dan status
3. **Tagihan**: Lihat semua tagihan
4. **Pembayaran**: Upload bukti pembayaran
5. **Permintaan Upgrade**: Request upgrade paket

## 🔧 Konfigurasi Tambahan

### Storage Link (untuk upload file)
```bash
php artisan storage:link
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Optimize
```bash
php artisan optimize
```

## 📁 Struktur Project

```
rt-rw-net-v2backup/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/          # Controller untuk admin
│   │   │   └── Pelanggan/      # Controller untuk pelanggan
│   │   └── Middleware/          # Middleware untuk auth
│   └── Models/                  # Eloquent models
├── database/
│   ├── migrations/              # Database migrations
│   └── database.sqlite          # SQLite (opsional)
├── resources/
│   ├── views/                   # Blade templates
│   │   ├── admin/               # Views untuk admin
│   │   └── pelanggan/           # Views untuk pelanggan
│   ├── css/                     # CSS files
│   └── js/                      # JavaScript files
├── routes/
│   ├── web.php                  # Web routes
│   └── auth.php                 # Auth routes
├── public/                      # Public assets
└── storage/                     # Storage untuk upload
```

## 🐛 Troubleshooting

### Error: "Call to undefined relationship [user]"
- Pastikan tidak menggunakan eager loading `pelanggan.user`
- Gunakan `pelanggan` saja, lalu load user secara manual dengan `getUser()`

### Error: "Pelanggan ID sudah mencapai batas maksimal"
- Format `pelanggan_id` menggunakan 1 digit per bagian
- Maksimal 9 pelanggan dengan user_id dan paket_id yang sama
- Pertimbangkan upgrade ke format multi-digit jika diperlukan

### Error saat migrasi
- Pastikan database sudah dibuat
- Pastikan kredensial database di `.env` benar
- Backup database sebelum migrasi

## 📄 License

MIT License

## 👨‍💻 Kontribusi

Kontribusi sangat diterima! Silakan:
1. Fork repository
2. Buat branch untuk fitur baru (`git checkout -b feature/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📞 Support

Untuk pertanyaan atau bantuan, silakan buat issue di repository ini.

## 🙏 Acknowledgments

- Laravel Framework
- Tailwind CSS
- Alpine.js
- Semua kontributor

---

**Dibuat dengan ❤️ untuk memudahkan pengelolaan RT-RW Net**
