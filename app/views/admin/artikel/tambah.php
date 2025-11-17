<?php
use App\Core\Config;
use App\Core\FlashMessage;

$base = Config::get('base_url');

// Ambil old input dari session (jika ada)
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>

<div class="artikel-form">
  <?=FlashMessage::show();?>
  <h2 class="page-title">Tambah Artikel Baru</h2>

  <form action="<?= $base ?>/admin/artikel/store" method="POST" enctype="multipart/form-data">
    <!-- CSRF Token -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <!-- Judul -->
    <div class="form-group">
      <label for="judul">Judul Artikel</label>
      <input type="text" name="judul" id="judul" value="<?= htmlspecialchars($old['judul'] ?? '')?>">
    </div> 

    <!-- Kategori -->
    <div class="form-group">
      <label for="kategori">Kategori</label>
      <select name="kategori_id" id="kategori">
        <option value="">-- Pilih Kategori --</option>
        <?php foreach($kategori as $k): ?>
          <option value="<?= $k['id'] ?>" <?= isset($old['kategori_id']) && $old['kategori_id'] == $k['id'] ? 'selected' : '' ?>>
             <?= htmlspecialchars($k['nama']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Status -->
    <div class="form-group">
      <label for="status">Status Artikel</label>
      <select name="status" id="status" required>
        <option value="draft" <?= (isset($old['status']) && $old['status'] === 'draft') ? 'selected' : ''?>>Draft</option>
        <option value="publish" <?= (isset($old['status']) && $old['status'] === 'publish') ? 'selected' : ''?>>Publish</option>
        <option value="archived" <?= (isset($old['status']) && $old['status'] === 'archived') ? 'selected' : ''?>>Archived</option>
      </select>
    </div>

    <!-- Isi -->
    <div class="form-group textarea-tambah">
      <label for="isi">Isi Artikel</label>
      <textarea name="isi" id="isi"><?= htmlspecialchars($old['isi'] ?? '')?></textarea>
    </div>

    <!-- Upload Gambar Tambahan -->
    <div class="form-group">
      <label for="images">Upload Gambar Tambahan</label>
      <p class="form-note">Anda dapat memilih beberapa gambar untuk artikel ini.</p>
      <input type="file" name="images[]" id="images" multiple accept="image/*">
    </div>

    <!-- caption -->
    <div id="image-preview"></div>

    <!-- Thumbnail -->
    <div class="form-group">
      <label for="thumbnail">Gambar Thumbnail (opsional)</label>
      <input type="file" name="thumbnail" id="thumbnail" accept="image/*">
    </div>

    <!-- Tanggal Posting -->
    <div class="form-group">
      <label for="tanggal_posting">Tanggal Posting</label>
      <input type="datetime-local" name="tanggal_posting" id="tanggal_posting" value="<?= htmlspecialchars($old['tanggal_posting'] ?? date('Y-m-d\TH:i')) ?>">
    </div>

    <!-- Tombol -->
    <div class="form-buttons">
      <button type="submit" class="btn-primary">Simpan Artikel</button>
      <a href="<?= $base ?>/admin/artikel" class="btn-secondary">Kembali</a>
    </div>
  </form>
</div>
