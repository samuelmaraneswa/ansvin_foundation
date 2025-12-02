<?php
use App\Core\Config;

$base = Config::get('base_url');
$user = $user ?? null; // pastikan $user dikirim dari controller
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($title ?? 'Admin Panel') ?></title>
  <link rel="stylesheet" href="<?= $base ?>/assets/css/style.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/admin_header.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/admin_sidebar.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/admin_dashboard.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/admin_main.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/admin_artikel.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/pegawai.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/pegawai/unitPegawai.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/pendaftaran/admin_pendaftaran.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/mapel/mapel.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/guru_mapel/guru_mapel.css">
  <link rel="stylesheet" href="<?= $base ?>/assets/css/jadpel/jadpel.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<header class="admin-header">
  <div class="left">
    <button id="sidebarToggle" class="logo"><i class="fa-solid fa-xmark"></i></button>
    <span class="title">Admin Panel</span>
  </div>

  <div class="right">
    <i class="fas fa-bell"></i>
    <i class="fas fa-envelope"></i>
    <i class="fas fa-chart-line"></i>

    <div class="user-profile">
      <img src="<?= $base ?>/<?= htmlspecialchars($user['foto'] ?? 'default_img.jpg') ?>" alt="User" class="user-avatar" />
      <div class="dropdown">
        <ul>
          <li><a href="<?= $base ?>/admin/profile">Profil</a></li>
          <li><a href="<?= $base ?>/auth/logout">Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</header>

<?php 
  if (method_exists($this, 'includeView')) {
    $this->includeView('layouts/admin_sidebar');
  }
?>

