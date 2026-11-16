<?php
require 'config.php';
wajib_login('siswa');
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Siswa | SPP Digital</title>
<style>
  body{margin:0;font-family:"Segoe UI",system-ui,Arial,sans-serif;background:#eef3fb;color:#1c2733}
  header{background:#1f6feb;color:#fff;padding:16px 28px;display:flex;justify-content:space-between;align-items:center}
  header a{color:#fff;text-decoration:none;background:rgba(255,255,255,.18);padding:8px 16px;border-radius:8px;font-size:14px}
  main{max-width:900px;margin:30px auto;padding:0 20px}
  .card{background:#fff;border-radius:14px;padding:26px;box-shadow:0 6px 20px rgba(31,64,120,.08)}
  ul{line-height:1.9}
</style>
</head>
<body>
<header>
  <strong>SPP Digital · Siswa</strong>
  <a href="logout.php">Logout</a>
</header>
<main>
  <div class="card">
    <h2>Halo, <?= h($_SESSION['nama']) ?> 👋</h2>
    <p>Login berhasil. Ini halaman beranda sementara untuk peran <strong>Siswa</strong>.</p>
    <ul>
      <li>Lihat status tagihan SPP</li><li>Riwayat pembayaran</li>
    </ul>
  </div>
</main>
</body>
</html>
