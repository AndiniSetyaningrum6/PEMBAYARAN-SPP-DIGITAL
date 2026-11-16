<?php
// ===== Konfigurasi koneksi database (XAMPP/Laragon default) =====
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_spp_digital');

mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($koneksi->connect_errno) {
    die('Koneksi database gagal. Pastikan MySQL aktif dan database db_spp_digital sudah di-import lewat phpMyAdmin.');
}
$koneksi->set_charset('utf8mb4');

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Halaman beranda sesuai peran (UC-01)
function halaman_beranda($role) {
    return [
        'bendahara'  => 'beranda_bendahara.php',
        'wali_murid' => 'beranda_wali.php',
        'siswa'      => 'beranda_siswa.php',
    ][$role] ?? 'login.php';
}

// Batasi akses halaman berdasarkan peran
function wajib_login($role) {
    if (empty($_SESSION['id_pengguna']) || $_SESSION['role'] !== $role) {
        header('Location: login.php');
        exit;
    }
}
