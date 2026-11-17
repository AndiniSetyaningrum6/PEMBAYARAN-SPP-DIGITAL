<?php
require 'config.php';

// Jika sudah login, langsung ke beranda
if (!empty($_SESSION['id_pengguna'])) {
    header('Location: ' . halaman_beranda($_SESSION['role']));
    exit;
}

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));

$error = '';
$success = isset($_GET['daftar']) ? 'Pendaftaran berhasil. Silakan login dengan akun yang baru dibuat.' : '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $error = 'Sesi tidak valid. Silakan muat ulang halaman.';
    } elseif ($username === '' || $password === '') {
        // Alternatif UC-01: kolom belum diisi
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $koneksi->prepare('SELECT id_pengguna, nama, password, role, status_akun FROM pengguna WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $password_valid = false;
        if ($user) {
            // Mendukung akun lama yang password-nya masih tersimpan sebagai MD5.
            // Setelah login berhasil, hash lama langsung diperbarui ke password_hash().
            $password_valid = password_verify($password, $user['password']);
            if (!$password_valid && preg_match('/^[a-f0-9]{32}$/i', $user['password'])
                && hash_equals(strtolower($user['password']), md5($password))) {
                $password_valid = true;
                $hash_baru = password_hash($password, PASSWORD_DEFAULT);
                $migrasi = $koneksi->prepare('UPDATE pengguna SET password = ? WHERE id_pengguna = ?');
                $migrasi->bind_param('si', $hash_baru, $user['id_pengguna']);
                $migrasi->execute();
                $migrasi->close();
            }
        }

        if (!$user || !$password_valid) {
            // Alternatif UC-01: username/password salah
            $error = 'Username atau password salah.';
        } elseif ($user['status_akun'] !== 'aktif') {
            // Alternatif UC-01: akun tidak aktif
            $error = 'Akun Anda tidak aktif. Silakan hubungi bendahara/tata usaha sekolah.';
        } else {
            session_regenerate_id(true);
            $_SESSION['id_pengguna'] = (int)$user['id_pengguna'];
            $_SESSION['nama']        = $user['nama'];
            $_SESSION['role']        = $user['role'];
            $_SESSION['csrf']        = bin2hex(random_bytes(32));

            $upd = $koneksi->prepare('UPDATE pengguna SET terakhir_login = NOW() WHERE id_pengguna = ?');
            $upd->bind_param('i', $user['id_pengguna']);
            $upd->execute();
            $upd->close();

            header('Location: ' . halaman_beranda($user['role']));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login | SPP Digital</title>
<style>
  :root{
    --bg:#eef3fb; --card:#ffffff; --text:#1c2733; --muted:#64748b;
    --primary:#1f6feb; --primary-dark:#1556bd; --line:#d8e0ec;
    --err-bg:#fdecec; --err-text:#b42318;
  }
  *{box-sizing:border-box}
  body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;
       font-family:"Segoe UI",system-ui,-apple-system,Roboto,Arial,sans-serif;
       background:linear-gradient(135deg,#e3edfd 0%,#f6f9ff 55%,#e8f5ee 100%);color:var(--text);padding:20px}
  .wrap{width:100%;max-width:900px;display:grid;grid-template-columns:1fr 1fr;
        background:var(--card);border-radius:20px;overflow:hidden;
        box-shadow:0 20px 50px rgba(31,64,120,.15)}
  .side{background:linear-gradient(160deg,#1f6feb,#14418f);color:#fff;padding:44px 36px;
        display:flex;flex-direction:column;justify-content:center}
  .logo{width:56px;height:56px;border-radius:14px;background:rgba(255,255,255,.18);
        display:flex;align-items:center;justify-content:center;font-size:28px;margin-bottom:22px}
  .side h2{margin:0 0 10px;font-size:26px;line-height:1.25}
  .side p{margin:0 0 22px;opacity:.9;line-height:1.55;font-size:15px}
  .side ul{margin:0;padding:0;list-style:none;font-size:14px}
  .side li{margin-bottom:10px;padding-left:26px;position:relative;opacity:.95}
  .side li::before{content:"✓";position:absolute;left:0;font-weight:700}
  .form{padding:44px 40px}
  .form h1{margin:0 0 6px;font-size:24px}
  .sub{margin:0 0 26px;color:var(--muted);font-size:14px}
  label{display:block;font-size:13px;font-weight:600;margin-bottom:6px}
  .field{margin-bottom:18px;position:relative}
  input[type=text],input[type=password]{width:100%;padding:12px 14px;font-size:15px;
        border:1.5px solid var(--line);border-radius:10px;outline:none;background:#fff;color:var(--text);
        transition:border-color .15s, box-shadow .15s}
  input:focus{border-color:var(--primary);box-shadow:0 0 0 4px rgba(31,111,235,.15)}
  .toggle{position:absolute;right:12px;top:34px;background:none;border:0;cursor:pointer;
          color:var(--muted);font-size:13px;font-weight:600}
  .btn{width:100%;padding:13px;border:0;border-radius:10px;background:var(--primary);color:#fff;
       font-size:16px;font-weight:600;cursor:pointer;transition:background .15s}
  .btn:hover{background:var(--primary-dark)}
  .alert{background:var(--err-bg);color:var(--err-text);padding:11px 14px;border-radius:10px;
         font-size:14px;margin-bottom:18px;border:1px solid #f5c2c0}
  .hint{margin-top:22px;font-size:12.5px;color:var(--muted);line-height:1.6;
        background:#f5f8fd;border:1px dashed var(--line);border-radius:10px;padding:10px 14px}
  .foot{margin-top:18px;text-align:center;font-size:12px;color:var(--muted)}
  @media (max-width:720px){
    .wrap{grid-template-columns:1fr}
    .side{padding:28px 26px}
    .side ul{display:none}
    .form{padding:30px 24px}
  }
</style>
</head>
<body>
<div class="wrap">
  <section class="side">
    <div class="logo">🎓</div>
    <h2>Sistem Pembayaran<br>SPP Digital</h2>
    <p>Cek tagihan, bayar nontunai, dan unduh kuitansi digital dalam satu portal terpusat.</p>
    <ul>
      <li>Lihat tagihan &amp; riwayat pembayaran</li>
      <li>Bayar via QRIS, E-Wallet, Virtual Account</li>
      <li>Pengingat jatuh tempo otomatis</li>
      <li>Kuitansi digital sebagai bukti bayar</li>
    </ul>
  </section>

  <section class="form">
    <h1>Selamat datang</h1>
    <p class="sub">Masuk untuk mengakses akun Anda.</p>

    <?php if ($success): ?>
      <div class="alert" role="status" style="background:#eaf8ef;color:#176b36;border-color:#b8e2c5"><?= h($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert" role="alert"><?= h($error) ?></div>
    <?php endif; ?>

    <form method="post" action="login.php" autocomplete="off" novalidate>
      <input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>">

      <div class="field">
        <label for="username">Username / NIS</label>
        <input type="text" id="username" name="username" value="<?= h($username) ?>"
               placeholder="Masukkan username atau NIS" autofocus>
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Masukkan password">
        <button type="button" class="toggle" onclick="
          var p=document.getElementById('password');
          p.type = p.type==='password' ? 'text' : 'password';
          this.textContent = p.type==='password' ? 'Tampilkan' : 'Sembunyikan';">Tampilkan</button>
      </div>

      <button type="submit" class="btn">Login</button>
    </form>

    <p style="text-align:center;margin:18px 0 0;font-size:14px;color:var(--muted)">
      Belum punya akun? <a href="register.php" style="color:var(--primary);font-weight:600;text-decoration:none">Daftar sekarang</a>
    </p>

    <div class="hint">
      <strong>Akun contoh</strong> (password: <code>password123</code>)<br>
      Bendahara: <code>bendahara</code> · Wali murid: <code>2025001</code> · Siswa: <code>siswa2025001</code>
    </div>
    <div class="foot">© 2026 Kelompok 8 · PTI 25 A</div>
  </section>
</div>
</body>
</html>
