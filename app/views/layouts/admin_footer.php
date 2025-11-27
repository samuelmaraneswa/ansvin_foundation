<script>
  const base_url = "<?= $base ?>";
</script>

<script src="https://cdn.tiny.cloud/1/ye0cq1ux7lwbtxhgegqsrec3njnn3qluy7z13re2nz2kf0n8/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script src="<?= $base ?>/assets/js/artikel/admin_artikel.js"></script>

<script src="<?= $base ?>/assets/js/artikel/klik_diluar.js"></script>
<script src="<?= $base ?>/assets/js/artikel/artikel-livesearch.js"></script>

<?php if ($user['role'] === 'super_admin'): ?>
  <script type="module" src="<?= $base ?>/assets/js/pegawai/admin/mainPegawai.js"></script>
<?php elseif ($user['role'] === 'admin_unit'): ?>
  <script type="module" src="<?= $base ?>/assets/js/pegawai/unit/mainUnitPegawai.js"></script>
<?php endif; ?>

<?php if ($user['role'] === 'super_admin'): ?>
  <script type="module" src="<?= $base ?>/assets/js/pendaftaran/admin/mainCalonSiswa.js"></script>
<?php elseif ($user['role'] === 'admin_unit'): ?>
  <script type="module" src="<?= $base ?>/assets/js/pendaftaran/unit/mainUnitCalonSiswa.js"></script>
<?php endif; ?>

<?php if($user['role'] === "admin_unit"):?>
  <script type="module" src="<?= $base ?>/assets/js/mapel/mainMapel.js"></script>
<?php endif;?>

<?php if($user['role'] === "admin_unit"):?>
  <script type="module" src="<?= $base ?>/assets/js/guru_mapel/mainGuruMapel.js"></script>
<?php endif;?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="<?= $base ?>/assets/js/admin.js"></script>