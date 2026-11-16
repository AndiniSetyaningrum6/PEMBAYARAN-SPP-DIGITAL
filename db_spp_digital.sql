-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 16, 2026 at 05:49 PM
-- Server version: 10.11.16-MariaDB
-- PHP Version: 8.3.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_spp_digital`
--

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(10) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('siswa','wali_murid','bendahara') NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_whatsapp` varchar(20) DEFAULT NULL,
  `status_akun` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `terakhir_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nama`, `username`, `password`, `role`, `email`, `no_whatsapp`, `status_akun`, `terakhir_login`, `created_at`) VALUES
(1, 'Bendahara Komite', 'bendahara', '94665964e241fa3e8fcc687143c32f74', 'bendahara', 'bendahara@sekolah.sch.id', '6281234567801', 'aktif', NULL, '2026-11-16 15:58:29'),
(2, 'Wali Murid Contoh', '2025001', '303641e4a3a57498851317f863fbc50a', 'wali_murid', 'wali@email.com', '6281234567802', 'aktif', NULL, '2026-11-16 15:58:29'),
(3, 'Siswa Contoh', 'siswa2025001', '1f5cc082506d0121b0fb53ae605ef9e9', 'siswa', 'siswa@email.com', '6281234567803', 'aktif', NULL, '2026-11-16 15:58:29'),
(4, 'Akun Nonaktif', 'nonaktif', 'd81c18ebe2e6014f65aa29579bce0854', 'wali_murid', 'nonaktif@email.com', '6281234567804', 'nonaktif', NULL, '2026-11-16 15:58:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `uq_username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
