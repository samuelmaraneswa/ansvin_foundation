<?php
use App\Core\Config;
use App\Core\FlashMessage;

$base = Config::get('base_url');

// Ambil old input dari session (jika ada)
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>

<div class="artikel-form"> 
  <h2 class="page-title">Edit Artikel</h2>

  <form action="<?= $base ?>/admin/artikel/update/<?= $artikel['id'] ?>" method="POST" enctype="multipart/form-data">
    <?php FlashMessage::show();?>
    
    <!-- CSRF Token -->
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

    <!-- Judul -->
    <div class="form-group">
      <label for="judul">Judul Artikel</label>
      <input type="text" name="judul" id="judul" 
        value="<?= htmlspecialchars($old['judul'] ?? $artikel['judul'])?>" required>
    </div>

    <!-- Kategori -->
    <div class="form-group">
      <label for="kategori">Kategori</label>
      <select name="kategori_id" id="kategori" required>
        <option value="">-- Pilih Kategori --</option>
        <?php foreach($kategori as $k): ?>
          <?php
            // tentukan id yang harus dipilih
            $selectedId = $old['kategori_id'] ?? $artikel['kategori_id'];
          ?>
          <option value="<?= $k['id'] ?>" 
            <?= ($artikel['kategori_id'] == $k['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($k['nama']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- Status -->
    <div class="form-group">
      <label for="status">Status Artikel</label>
      <?php 
        $selectedStatus = $old['status'] ?? $artikel['status']; 
      ?>
      <select name="status" id="status" required>
        <option value="draft" <?= $selectedStatus === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="publish" <?= $selectedStatus === 'publish' ? 'selected' : '' ?>>Publish</option>
        <option value="archived" <?= $selectedStatus === 'archived' ? 'selected' : '' ?>>Archived</option>
      </select>
    </div>

    <!-- Isi Artikel -->
    <div class="form-group textarea-tambah">
      <label for="isi">Isi Artikel</label>
      <textarea name="isi" id="isi"><?= $old['isi'] ?? $artikel['isi'] ?></textarea>
    </div>

    <!-- Thumbnail lama -->
    <?php 
    $thumb = '';
    foreach ($artikel['images'] ?? [] as $img) {
      if ($img['is_thumbnail']) $thumb = $img['path'];
    }
    ?>
    <?php if ($thumb): ?>
      <div class="form-group">
        <label>Thumbnail Sekarang:</label>
        <img src="<?= $base ?>/<?= $thumb ?>" alt="Thumbnail Lama" class="preview-thumbnail-edit">
      </div>
    <?php endif; ?>

    <!-- Upload Thumbnail baru -->
    <div class="form-group">
      <p class="form-note">Jika Anda tidak memilih file baru, thumbnail lama akan tetap digunakan.</p>
      <label for="thumbnail">Ganti Thumbnail (opsional)</label>
      <input type="file" name="thumbnail" id="thumbnail" accept="image/*">
    </div>

    <!-- Gambar tambahan lama -->
    <?php if (!empty($artikel['images'])): ?>
      <div class="form-group">
        <label>Gambar Tambahan Lama:</label>
        <div class="artikel-gallery-edit">
          <?php foreach ($artikel['images'] as $img): ?>
            <?php if ($img['is_thumbnail']) continue; ?>

            <div class="old-image" data-id="<?= $img['id'] ?>">
              <img src="<?= $base ?>/<?= $img['path'] ?>" alt="Gambar tambahan">
              <input type="hidden" name="old_image_id[]" value="<?= $img['id'] ?>">
              <input type="text" name="old_captions[]" value="<?= htmlspecialchars($img['caption']) ?>" placeholder="Caption gambar ini">
              <button type="button" class="delete-image">
                <i class="fas fa-trash"></i>
              </button>
            </div>
            
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Upload Gambar Baru -->
    <div class="form-group">
      <label for="images">Upload Gambar Tambahan Baru</label>
      <p class="form-note">Anda dapat memilih beberapa gambar untuk artikel ini.</p>
      <input type="file" name="new_images[]" id="images" multiple accept="image/*">
    </div>
    <div id="image-preview"></div>

    <!-- Hidden untuk array gambar yang dihapus -->
    <input type="hidden" name="deleted_image_ids[]" id="deleted_image_ids">

    <!-- Tanggal Posting -->
    <div class="form-group">
      <label for="tanggal_posting">Tanggal Posting</label>
      <input type="datetime-local" name="tanggal_posting" id="tanggal_posting"
        value="<?= date('Y-m-d\TH:i', strtotime($old['tanggal_posting'] ?? $artikel['tanggal_posting'])) ?>">
    </div>

    <!-- Tombol -->
    <div class="form-buttons">
      <button type="submit" class="btn-primary">Perbarui Artikel</button>
      <a href="<?= $base ?>/admin/artikel/detail/<?= $artikel['id'] ?>" class="btn-secondary">Kembali</a>
    </div>
  </form>
</div>
