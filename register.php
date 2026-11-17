<?php
require 'config.php';

if (!empty($_SESSION['id_pengguna'])) {
    header('Location: ' . halaman_beranda($_SESSION['role']));
    exit;
}
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$error = '';
$nama = '';
$username = '';
$role = 'wali_murid';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';
    $role = $_POST['role'] ?? 'wali_murid';
    $csrf = $_POST['csrf'] ?? '';

    if (!hash_equals($_SESSION['csrf'], $csrf)) {
        $error = 'Sesi tidak valid. Silakan muat ulang halaman.';
    } elseif ($nama === '' || $username === '' || $password === '' || $konfirmasi === '') {
        $error = 'Semua kolom wajib diisi.';
    } elseif (mb_strlen($nama) > 100) {
        $error = 'Nama maksimal 100 karakter.';
    } elseif (!preg_match('/^[A-Za-z0-9_.-]{4,50}$/', $username)) {
        $error = 'Username harus 4–50 karakter dan hanya boleh berisi huruf, angka, titik, garis bawah, atau tanda hubung.';
    } elseif (strlen($password) < 8) {
        $error = 'Password minimal 8 karakter.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi password tidak sama.';
    } elseif (!in_array($role, ['siswa', 'wali_murid'], true)) {
        $error = 'Jenis akun tidak valid.';
    } else {
        $cek = $koneksi->prepare('SELECT id_pengguna FROM pengguna WHERE username = ? LIMIT 1');
        $cek->bind_param('s', $username);
        $cek->execute();
        $sudah_ada = $cek->get_result()->num_rows > 0;
        $cek->close();

        if ($sudah_ada) {
            $error = 'Username sudah digunakan. Silakan pilih username lain.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $status = 'aktif';
            $stmt = $koneksi->prepare('INSERT INTO pengguna (nama, username, password, role, status_akun) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $nama, $username, $hash, $role, $status);

            if ($stmt->execute()) {
                $stmt->close();
                header('Location: login.php?daftar=berhasil');
                exit;
            }
            $stmt->close();
            $error = 'Pendaftaran belum berhasil. Silakan coba kembali.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Daftar Akun | SPP Digital</title>
<style>
*{box-sizing:border-box}
body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;font-family:"Segoe UI",system-ui,Arial,sans-serif;background:linear-gradient(135deg,#e3edfd,#f6f9ff 55%,#e8f5ee);color:#1c2733}
.card{width:100%;max-width:480px;background:#fff;padding:36px;border-radius:18px;box-shadow:0 18px 45px rgba(31,64,120,.13)}
h1{font-size:25px;margin:0 0 8px}
.sub{color:#64748b;font-size:14px;margin:0 0 24px}
.field{margin-bottom:17px}
label{display:block;font-size:13px;font-weight:600;margin-bottom:7px}
input,select{width:100%;padding:12px 13px;border:1.5px solid #d8e0ec;border-radius:9px;font-size:15px;background:#fff;color:#1c2733}
input:focus,select:focus{outline:none;border-color:#1f6feb;box-shadow:0 0 0 3px rgba(31,111,235,.12)}
.btn{width:100%;padding:13px;border:0;border-radius:9px;background:#1f6feb;color:white;font-size:15px;font-weight:600;cursor:pointer}
.btn:hover{background:#1556bd}
.alert{background:#fdecec;color:#b42318;border:1px solid #f5c2c0;padding:11px 13px;border-radius:9px;margin-bottom:17px;font-size:14px}
.note{font-size:12px;color:#64748b;line-height:1.5;margin-top:7px}
.foot{text-align:center;margin-top:20px;font-size:14px;color:#64748b}
a{color:#1f6feb;text-decoration:none;font-weight:600}
@media(max-width:520px){.card{padding:26px 21px}}
</style>
</head>
<body>
<main class="card">
  <h1>Buat Akun SPP Digital</h1>
  <p class="sub">Isi data berikut untuk membuat akun baru.</p>

  <?php if ($error): ?>
    <div class="alert" role="alert"><?= h($error) ?></div>
  <?php endif; ?>

  <form method="post" action="register.php">
    <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">

    <div class="field">
      <label for="nama">Nama lengkap</label>
      <input type="text" id="nama" name="nama" maxlength="100" value="<?= h($nama) ?>" placeholder="Masukkan nama lengkap" required>
    </div>

    <div class="field">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" minlength="4" maxlength="50" pattern="[A-Za-z0-9_.-]+" value="<?= h($username) ?>" placeholder="Buat username" autocomplete="username" required>
      <div class="note">4–50 karakter; gunakan huruf, angka, titik, garis bawah, atau tanda hubung.</div>
    </div>

    <div class="field">
      <label for="role">Daftar sebagai</label>
      <select id="role" name="role" required>
        <option value="wali_murid" <?= $role === 'wali_murid' ? 'selected' : '' ?>>Wali Murid</option>
        <option value="siswa" <?= $role === 'siswa' ? 'selected' : '' ?>>Siswa</option>
      </select>
    </div>

    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" minlength="8" placeholder="Buat password (minimal 8 karakter)" autocomplete="new-password" required>
    </div>

    <div class="field">
      <label for="konfirmasi_password">Konfirmasi password</label>
      <input type="password" id="konfirmasi_password" name="konfirmasi_password" minlength="8" placeholder="Masukkan ulang password" autocomplete="new-password" required>
    </div>

    <button class="btn" type="submit">Daftar</button>
  </form>

  <div class="foot">Sudah punya akun? <a href="login.php">Login di sini</a></div>
</main>
</body>
</html>
