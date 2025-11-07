-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 07, 2025 at 12:02 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rtrwnetv3`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_11_06_204638_create_paket_table', 1),
(5, '2025_11_06_204640_create_pelanggan_table', 1),
(6, '2025_11_06_204642_create_tagihan_table', 1),
(7, '2025_11_06_204644_create_pembayaran_table', 1),
(8, '2025_11_06_204646_create_permintaan_upgrade_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `paket`
--

CREATE TABLE `paket` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_paket` varchar(255) NOT NULL,
  `harga_bulanan` mediumint NOT NULL,
  `harga_pemasangan` mediumint NOT NULL,
  `kecepatan` varchar(255) NOT NULL,
  `deskripsi` text,
  `status` enum('aktif','tidak_aktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `paket`
--

INSERT INTO `paket` (`id`, `nama_paket`, `harga_bulanan`, `harga_pemasangan`, `kecepatan`, `deskripsi`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Paket Hemat', 250000, 100000, '15 MB', NULL, 'aktif', '2025-11-06 14:01:43', '2025-11-06 14:01:43'),
(2, 'Paket Keluarga', 300000, 100000, '30 MB', NULL, 'aktif', '2025-11-06 14:48:50', '2025-11-06 15:16:55');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `paket_id` bigint UNSIGNED NOT NULL,
  `alamat` text NOT NULL,
  `no_telepon` varchar(255) NOT NULL,
  `tanggal_pemasangan` date NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `user_id`, `paket_id`, `alamat`, `no_telepon`, `tanggal_pemasangan`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 'Pasuruan Lor RT01/01 No.01', '088811132347', '2025-11-06', 'aktif', '2025-11-06 14:10:47', '2025-11-06 15:15:42'),
(2, 3, 1, 'Ds. Bacin, RT 02/04 No.12', '082331331331', '2025-11-01', 'aktif', '2025-11-06 15:36:46', '2025-11-06 15:36:46'),
(3, 4, 1, 'Ds. Jepangpakis, RT03/02, No.31', '083277777333', '2025-11-06', 'aktif', '2025-11-06 16:47:18', '2025-11-06 16:49:40');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` bigint UNSIGNED NOT NULL,
  `tagihan_id` bigint UNSIGNED NOT NULL,
  `pelanggan_id` bigint UNSIGNED NOT NULL,
  `jumlah_bayar` decimal(12,2) NOT NULL,
  `tanggal_bayar` date DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `status` enum('menunggu_verifikasi','lunas','ditolak') NOT NULL DEFAULT 'menunggu_verifikasi',
  `catatan_admin` text,
  `diverifikasi_oleh` bigint UNSIGNED DEFAULT NULL,
  `diverifikasi_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `tagihan_id`, `pelanggan_id`, `jumlah_bayar`, `tanggal_bayar`, `bukti_pembayaran`, `status`, `catatan_admin`, `diverifikasi_oleh`, `diverifikasi_pada`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '250000.00', '2025-11-06', 'bukti_pembayaran/dGbLqXsY1djYQpQKRCYA5IB65ezeOHrv71mDwKeP.png', 'lunas', NULL, 1, '2025-11-06 15:06:17', '2025-11-06 14:31:57', '2025-11-06 15:06:17'),
(2, 2, 1, '250000.00', '2025-11-06', 'bukti_pembayaran/A07jMJmOLEklZAfWqjIfkhmC0P9145rkmZDPIQn5.png', 'menunggu_verifikasi', NULL, NULL, NULL, '2025-11-06 14:46:05', '2025-11-06 14:46:05'),
(3, 5, 3, '400000.00', '2025-11-06', 'bukti_pembayaran/GT1lgrWg5EyJ0Resj6hLguqdeHdN9PUnA1MAJgX4.png', 'lunas', NULL, 1, '2025-11-06 16:49:27', '2025-11-06 16:48:44', '2025-11-06 16:49:27');

-- --------------------------------------------------------

--
-- Table structure for table `permintaan_upgrade`
--

CREATE TABLE `permintaan_upgrade` (
  `id` bigint UNSIGNED NOT NULL,
  `pelanggan_id` bigint UNSIGNED NOT NULL,
  `paket_lama_id` bigint UNSIGNED NOT NULL,
  `paket_baru_id` bigint UNSIGNED NOT NULL,
  `status` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu',
  `alasan` text,
  `catatan_admin` text,
  `diproses_oleh` bigint UNSIGNED DEFAULT NULL,
  `diproses_pada` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `permintaan_upgrade`
--

INSERT INTO `permintaan_upgrade` (`id`, `pelanggan_id`, `paket_lama_id`, `paket_baru_id`, `status`, `alasan`, `catatan_admin`, `diproses_oleh`, `diproses_pada`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, 'disetujui', NULL, NULL, 1, '2025-11-06 15:15:42', '2025-11-06 15:11:55', '2025-11-06 15:15:42'),
(2, 3, 2, 1, 'disetujui', NULL, NULL, 1, '2025-11-06 16:49:40', '2025-11-06 16:48:58', '2025-11-06 16:49:40');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('gWtP3gT1VYGXH9cbkqXapEnAu5n1FDA6p0rEsA49', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiMnExWEpqVmR3NENxRzF3VDQzWkltNTlZVnVSZjdORnRnT0cxQ2xUeSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1762473418);

-- --------------------------------------------------------

--
-- Table structure for table `tagihan`
--

CREATE TABLE `tagihan` (
  `id` bigint UNSIGNED NOT NULL,
  `pelanggan_id` bigint UNSIGNED NOT NULL,
  `paket_id` bigint UNSIGNED NOT NULL,
  `bulan` int NOT NULL,
  `tahun` int NOT NULL,
  `jumlah_tagihan` decimal(12,2) NOT NULL,
  `status` enum('belum_bayar','lunas') NOT NULL DEFAULT 'belum_bayar',
  `tanggal_jatuh_tempo` date NOT NULL,
  `jenis_tagihan` enum('bulanan','upgrade','pemasangan') NOT NULL DEFAULT 'bulanan',
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tagihan`
--

INSERT INTO `tagihan` (`id`, `pelanggan_id`, `paket_id`, `bulan`, `tahun`, `jumlah_tagihan`, `status`, `tanggal_jatuh_tempo`, `jenis_tagihan`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 11, 2025, '350000.00', 'belum_bayar', '2025-11-13', 'pemasangan', 'Tagihan pemasangan pertama', '2025-11-06 14:10:47', '2025-11-06 14:10:47'),
(2, 1, 1, 12, 2025, '250000.00', 'lunas', '2025-12-07', 'bulanan', NULL, '2025-11-06 14:22:39', '2025-11-06 15:06:17'),
(3, 1, 2, 11, 2025, '50000.00', 'belum_bayar', '2025-11-13', 'upgrade', 'Tagihan upgrade dari Paket Hemat ke Paket Keluarga', '2025-11-06 15:15:42', '2025-11-06 15:15:42'),
(4, 2, 1, 11, 2025, '350000.00', 'belum_bayar', '2025-11-08', 'pemasangan', 'Tagihan pemasangan pertama', '2025-11-06 15:36:46', '2025-11-06 15:36:46'),
(5, 3, 2, 11, 2025, '400000.00', 'lunas', '2025-11-13', 'pemasangan', 'Tagihan pemasangan pertama', '2025-11-06 16:47:18', '2025-11-06 16:49:27'),
(6, 3, 1, 11, 2025, '0.00', 'lunas', '2025-11-13', 'upgrade', 'Tagihan upgrade dari Paket Keluarga ke Paket Hemat', '2025-11-06 16:49:40', '2025-11-06 16:56:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pelanggan') NOT NULL DEFAULT 'pelanggan',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', NULL, '$2y$12$falBCMHnGPwtcDrcMwfe2u88R1W.uhWoENVGRMLC4pvaBo4khOFiq', 'admin', NULL, '2025-11-06 13:52:16', '2025-11-06 13:52:16'),
(2, 'Farith', 'farith@gmail.com', NULL, '$2y$12$Wh.KiYaUigIfJPaHXZuQeO5Uh4b99lf7UaXz95yTkfoAXOrKltx2a', 'pelanggan', NULL, '2025-11-06 14:10:47', '2025-11-06 14:10:47'),
(3, 'Wicaksono', 'wicak@gmail.com', NULL, '$2y$12$eOeoPgz9fWyc9WSnRecS6.pM0efiWGgPI6nVVmUIwUKX7l39Yuo8m', 'pelanggan', NULL, '2025-11-06 15:36:46', '2025-11-06 15:36:46'),
(4, 'Guno', 'guno@gmail.com', NULL, '$2y$12$Fu9DtSPgHcuudNfShzGLeuuqIH/YtsUcucdc5ygXoVJb3HeeUnPd2', 'pelanggan', NULL, '2025-11-06 16:47:18', '2025-11-06 16:47:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paket`
--
ALTER TABLE `paket`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelanggan_user_id_foreign` (`user_id`),
  ADD KEY `pelanggan_paket_id_foreign` (`paket_id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembayaran_tagihan_id_foreign` (`tagihan_id`),
  ADD KEY `pembayaran_pelanggan_id_foreign` (`pelanggan_id`),
  ADD KEY `pembayaran_diverifikasi_oleh_foreign` (`diverifikasi_oleh`);

--
-- Indexes for table `permintaan_upgrade`
--
ALTER TABLE `permintaan_upgrade`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permintaan_upgrade_pelanggan_id_foreign` (`pelanggan_id`),
  ADD KEY `permintaan_upgrade_paket_lama_id_foreign` (`paket_lama_id`),
  ADD KEY `permintaan_upgrade_paket_baru_id_foreign` (`paket_baru_id`),
  ADD KEY `permintaan_upgrade_diproses_oleh_foreign` (`diproses_oleh`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tagihan`
--
ALTER TABLE `tagihan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tagihan_pelanggan_id_foreign` (`pelanggan_id`),
  ADD KEY `tagihan_paket_id_foreign` (`paket_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `paket`
--
ALTER TABLE `paket`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `permintaan_upgrade`
--
ALTER TABLE `permintaan_upgrade`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tagihan`
--
ALTER TABLE `tagihan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD CONSTRAINT `pelanggan_paket_id_foreign` FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `pelanggan_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_diverifikasi_oleh_foreign` FOREIGN KEY (`diverifikasi_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pembayaran_pelanggan_id_foreign` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pembayaran_tagihan_id_foreign` FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permintaan_upgrade`
--
ALTER TABLE `permintaan_upgrade`
  ADD CONSTRAINT `permintaan_upgrade_diproses_oleh_foreign` FOREIGN KEY (`diproses_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `permintaan_upgrade_paket_baru_id_foreign` FOREIGN KEY (`paket_baru_id`) REFERENCES `paket` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `permintaan_upgrade_paket_lama_id_foreign` FOREIGN KEY (`paket_lama_id`) REFERENCES `paket` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `permintaan_upgrade_pelanggan_id_foreign` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tagihan`
--
ALTER TABLE `tagihan`
  ADD CONSTRAINT `tagihan_paket_id_foreign` FOREIGN KEY (`paket_id`) REFERENCES `paket` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `tagihan_pelanggan_id_foreign` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
