<?php
use App\Core\Config;
$base = Config::get('base_url');
$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?? 'Web Sekolah' ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- global style -->
  <link rel="stylesheet" href="<?= Config::get('base_url') ?>/assets/css/style.css">

  <!-- component spesific style -->
  <link rel="stylesheet" href="<?= Config::get('base_url') ?>/assets/css/header.css">
  <link rel="stylesheet" href="<?= Config::get('base_url') ?>/assets/css/public_main.css">
  <link rel="stylesheet" href="<?= Config::get('base_url') ?>/assets/css/home.css">
  <link rel="stylesheet" href="<?= Config::get('base_url') ?>/assets/css/auth.css">
  <link rel="stylesheet" href="<?= Config::get('base_url') ?>/assets/css/pendaftaran.css">
  <link rel="stylesheet" href="<?= Config::get('base_url') ?>/assets/css/footer.css">
</head>
<body>

  <header class="main-header">
    <div class="header-left">
      <a href="<?= $base ?>/">
        <img src="<?= $base ?>/assets/img/logo.png" alt="Logo Sekolah" class="logo">
        <span class="school-name">Ansvin School</span>
      </a>
    </div>

    <nav class="header-center">
      <ul class="nav-menu">
        <li><a class="<?=($page == 'home') ? 'active' : ''?>" href="<?= $base ?>/">Home</a></li>
        <li class="dropdown">
          <a class="<?=($page == 'unit-sekolah') ? 'active' : ''?>" href="<?= $base ?>/pendaftaran">Unit Sekolah</a>
          <ul class="dropdown-menu">
            <li><a  href="<?= $base ?>/profil/sejarah">TK Ansvin</a></li>
            <li><a href="<?= $base ?>/profil/visi-misi">SD Ansvin</a></li>
            <li><a href="<?= $base ?>/profil/struktur">SMP Ansvin</a></li>
            <li><a href="<?= $base ?>/profil/struktur">SMA Ansvin</a></li>
          </ul>
        </li>

        <li class="dropdown">
          <a href="#">Profil</a>
          <ul class="dropdown-menu">
            <li><a href="<?= $base ?>/profil/sejarah">Sejarah</a></li>
            <li><a href="<?= $base ?>/profil/visi-misi">Visi & Misi</a></li>
            <li><a href="<?= $base ?>/profil/struktur">Struktur Organisasi</a></li>
            <li><a href="<?= $base ?>/profil/struktur">Fasilitas</a></li>
          </ul>
        </li>
        <li class="dropdown">
          <a href="#">Akademik</a>
          <ul class="dropdown-menu">
            <li><a href="<?= $base ?>/profil/sejarah">Guru & Staff</a></li>
            <li><a href="<?= $base ?>/profil/visi-misi">Jadwal Pelajaran</a></li>
            <li><a href="<?= $base ?>/profil/struktur">Kelender Akademik</a></li>
          </ul>
        </li>
        <li class="dropdown <?= in_array($page, ['pendaftaran','artikel','pengumuman']) ? 'active' : '' ?>">
          <a href="#">Informasi</a>
          <ul class="dropdown-menu">
            <li><a href="<?= $base ?>/pendaftaran">Pendaftaran</a></li>
            <li><a class="<?= ($page=='artikel') ? 'active' : '' ?>" href="<?= $base ?>/artikel">Artikel</a></li>
            <li><a class="<?= ($page=='pengumuman') ? 'active' : '' ?>" href="<?= $base ?>/profil/visi-misi">Pengumuman</a></li>
          </ul>
        </li>
        <li><a href="<?= $base ?>/kontak">Gallery</a></li>
        <li><a href="<?= $base ?>/kontak">EksKul</a></li>
      </ul>
    </nav>

    <div class="header-right">
      <form class="search-form" method="get" action="<?=$base_url?>/admin/artikel" autocomplete="off">
        <div class="input-wrapper-glob">
          <input type="text" name="searchGlob" placeholder="Search..." id="searchInputGlob" value="">
          <i id="searchIcon" class="fa fa-search"></i>
          <div id="suggestions" class="suggestions"></div>
        </div>
      </form>

      <?php if($user): ?>
      <div class="user-dropdown">
        <img src="<?= $base ?>/assets/img/uploads/default_img.jpg" alt="user-foto" class="user-foto">
        <ul class="dropdown-menu">
          <li><a href="<?= $base ?>/profil">Profil Saya</a></li>
          <?php if($user['role'] === 'admin'): ?>
            <li><a href="<?= $base ?>/admin/dashboard">Dashboard Admin</a></li>
          <?php elseif($user['role'] === 'guru'): ?>
            <li><a href="<?= $base ?>/guru/dashboard">Dashboard Guru</a></li>
          <?php endif; ?>
          <li><a href="<?= $base ?>/auth/logout">Logout</a></li>
        </ul>
      </div>
    <?php else: ?>
      <a href="<?= $base ?>/auth/login" class="btn-login">Login</a>
    <?php endif; ?>
    </div>
  </header>


  <main class="container">