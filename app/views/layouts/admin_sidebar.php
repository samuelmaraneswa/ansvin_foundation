<?php

use App\Core\Config;

$base = Config::get('base_url');

// Deteksi role dan unit
$rolePrefix = ($user['role'] === 'admin_unit') ? 'unit' : 'admin';

// Mapping unit_id ke slug
$unitSlugMap = [
  1 => 'tk',
  2 => 'sd',
  3 => 'smp',
  4 => 'sma'
];

// Jika user admin_unit, tentukan slug unit-nya
$unitSlug = $unitSlugMap[$user['unit_id']] ?? null;
// echo "<div style='margin: 100px 100px'>";
// debug($unitSlug);die;
// echo "</div>";
?>

<aside id="sidebar" class="sidebar">
  <!-- search form -->
  <div class="sidebar-search">
    <form action="" method="get">
      <input type="text" id="sidebarSearch" placeholder="Search menu...">
      <i class="fas fa-search search-icon"></i>
    </form>
  </div>

  <!-- menu -->
  <ul class="sidebar-menu">
    <li>
      <a class="<?= ($page == 'dashboard') ? 'active' : '' ?>"
        href="<?= $base ?>/admin/dashboard">Dashboard</a>
    </li>

    <?php if ($user && $user['role'] === 'super_admin'): ?>
      <li class="has-submenu">
        <a href="javascript:void(0)">Unit Sekolah</a>
        <ul class="submenu">
          <li><a href="<?= $base ?>/unit/tk">Ansvin TK</a></li>
          <li><a href="<?= $base ?>/unit/sd">Ansvin SD</a></li>
          <li><a href="<?= $base ?>/unit/smp">Ansvin SMP</a></li>
          <li><a href="<?= $base ?>/unit/sma">Ansvin SMA</a></li>
        </ul>
      </li>
    <?php endif; ?>

    <li>
      <a class="<?= ($page == 'artikel') ? 'active' : '' ?>"
        href="<?= $base ?>/<?= $rolePrefix ?>/<?= $unitSlug ? $unitSlug . '/' : '' ?>artikel">
        Artikel
      </a>
    </li>

    <li>
      <a href="<?= $base ?>/<?= $rolePrefix ?>/<?= $unitSlug ? $unitSlug . '/' : '' ?>fasilitas">
        Fasilitas
      </a>
    </li>

    <!-- Link Pegawai -->
    <?php if ($user['role'] === 'super_admin'): ?>
      <li>
        <a class="<?= ($page == 'pegawai') ? 'active' : '' ?>"
          href="<?= $base ?>/admin/pegawai">Pegawai
        </a>
      </li>
    <?php elseif ($user['role'] === 'admin_unit' && $unitSlug): ?>
      <li>
        <a class="<?= ($page == 'pegawai') ? 'active' : '' ?>"
          href="<?= $base ?>/unit/<?= $unitSlug ?>/pegawai">Pegawai
        </a>
      </li>
    <?php endif; ?>

    <?php if ($user && $user['role'] === 'admin_unit'): ?>
      <li class="has-submenu <?= ($page == 'mapel' || $page == 'guru_mapel') ? 'open' : '' ?>">
        <a href="javascript:void(0)" class="<?= ($page == 'mapel' || $page == 'guru_mapel') ? 'active' : '' ?>">Pelajaran</a>
        <ul class="submenu">
          <li>
            <a class="<?= ($page == 'mapel') ? 'active-submenu' : '' ?>"
              href="<?= $base ?>/unit/<?= $unitSlug ?>/mapel">Mata Pelajaran</a>
          </li>
          <li>
            <a class="<?= ($page == 'guru_mapel') ? 'active-submenu' : '' ?>"
              href="<?= $base ?>/unit/<?= $unitSlug ?>/guru_mapel">Guru Mapel</a>
          </li>
          <li><a href="<?= $base ?>/unit/smp">Ansvin SMP</a></li>
          <li><a href="<?= $base ?>/unit/sma">Ansvin SMA</a></li>
        </ul>
      </li>
    <?php endif; ?>

    <!-- Siswa -->
    <li class="has-submenu <?= ($page == 'calon_siswa' || $page == 'siswa') ? 'open' : '' ?>">
      <a href="javascript:void(0)">Siswa</a>
      <ul class="submenu">
        <?php if ($user['role'] === 'super_admin'): ?>
          <li>
            <a class="<?= ($page == 'calon_siswa') ? 'active-submenu' : '' ?>"
              href="<?= $base ?>/admin/pendaftaran">
              Calon Siswa
            </a>
          </li>
          <li>
            <a class="<?= ($page == 'siswa') ? 'active' : '' ?>"
              href="<?= $base ?>/admin/siswa">
              Siswa Aktif
            </a>
          </li>
        <?php elseif ($user['role'] === 'admin_unit' && $unitSlug): ?>
          <li>
            <a class="<?= ($page == 'calon_siswa') ? 'active-submenu' : '' ?>"
              href="<?= $base ?>/unit/<?= $unitSlug ?>/calon-siswa">
              Calon Siswa
            </a>
          </li>
          <li>
            <a class="<?= ($page == 'siswa') ? 'active' : '' ?>"
              href="<?= $base ?>/unit/<?= $unitSlug ?>/siswa">
              Siswa Aktif
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </li>


    <!-- Keuangan -->
    <li class="has-submenu">
      <a href="javascript:void(0)">Keuangan</a>
      <ul class="submenu">
        <?php if ($user['role'] === 'super_admin'): ?>
          <li><a href="<?= $base ?>/admin/keuangan/billing">Billing Siswa</a></li>
          <li><a href="<?= $base ?>/admin/keuangan/payroll">Payroll Pegawai</a></li>
        <?php elseif ($user['role'] === 'admin_unit' && $unitSlug): ?>
          <li><a href="<?= $base ?>/unit/<?= $unitSlug ?>/billing">Billing Siswa</a></li>
          <li><a href="<?= $base ?>/unit/<?= $unitSlug ?>/payroll">Payroll Pegawai</a></li>
        <?php endif; ?>
      </ul>
    </li>

    <!-- Laporan -->
    <li>
      <a href="<?= $base ?>/<?= $rolePrefix ?>/<?= $unitSlug ? $unitSlug . '/' : '' ?>laporan">
        Laporan
      </a>
    </li>

  </ul>
</aside>