<div class="artikel-detail-container">
  <?php
  use App\Core\FlashMessage;

  FlashMessage::show();
  ?>
  
  <div class="header-detail-artikel">
    <h1><?= htmlspecialchars($artikel['judul']) ?></h1>
    
    <div class="btn-detail-artikel">
      <a href="<?= $base_url ?>/admin/artikel/edit/<?= $artikel['id'] ?>">
        Edit 
      </a>
      
      <form action="<?= $base_url ?>/admin/artikel/delete/<?= $artikel['id'] ?>" method="POST" onsubmit="return confirm('Yakin ingin menghapus artikel ini?')">
        <button type="submit" class="btn-edit-artikel">Hapus</button>
      </form>
    </div>
  </div>

  <?php
    // Tampilkan thumbnail utama jika ada
    $thumb = '';
    foreach($artikel['images'] ?? [] as $img){
      if($img['is_thumbnail']) $thumb = $img['path'];
    }
  ?>

  <?php if($thumb): ?>
    <img src="<?=$base_url?>/<?= $thumb ?>" alt="Thumbnail" class="artikel-detail-thumb">
  <?php endif; ?>

  <p class="artikel-detail-information">
    Kategori: <?= htmlspecialchars($artikel['kategori_nama']) ?> |
    Status: <?= htmlspecialchars($artikel['status']) ?> |
    Tanggal: <?= htmlspecialchars($artikel['tanggal_posting']) ?>
  </p>

  <!-- Tampilkan isi artikel (HTML dari TinyMCE) -->
  <div class="artikel-isi">
    <?= $artikel['isi'] ?>
  </div>

  <!-- Tampilkan gambar tambahan -->
  <?php
  // Ambil semua gambar tambahan (bukan thumbnail)
  $gambar_tambahan = array_filter($artikel['images'] ?? [], fn($img) => empty($img['is_thumbnail']));
  ?>
  <?php if (!empty($gambar_tambahan)): ?>
    <h3>Foto terkait:</h3>
    <div class="artikel-detail-gallery">
      <?php foreach ($artikel['images'] as $img): ?>
        <?php if (!$img['is_thumbnail']): ?>
          <figure>
            <img src="<?= $base_url ?>/<?= $img['path'] ?>" alt="">
            <?php if (!empty($img['caption'])): ?>
              <figcaption>Caption: <?= htmlspecialchars($img['caption']) ?></figcaption>
            <?php endif; ?>
          </figure>
        <?php endif; ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>