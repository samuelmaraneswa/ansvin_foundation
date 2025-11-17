<div class="artikel-grid">
  <?php if(!empty($artikels)): ?>
    <?php foreach($artikels as $artikel): ?>
      <?php 
        // Potong isi artikel untuk cuplikan
        $cuplikan = strip_tags($artikel['isi']);
        if(strlen($cuplikan) > 100){
          $cuplikan = substr($cuplikan,0,120) . '<span class="selengkapnya">...baca selengkapnya</span>';
        }
      ?>
      <div class="artikel-card">
        <a href="<?=$base_url?>/admin/artikel/detail/<?= $artikel['id'] ?>" class="card-link-wrapper">
          
          <?php if (!empty($artikel['thumbnail'])): ?>
            <img src="<?=$base_url?>/<?= $artikel['thumbnail'] ?>" alt="Thumbnail" class="card-thumb">
          <?php endif; ?>

          <div class="card-body">
            <h3 class="card-title"><?= htmlspecialchars($artikel['judul']) ?></h3>
            <span class="card-category">Category: <?= $artikel['kategori_nama'] ?></span>
            <p class="card-text">
              <?= $cuplikan ?>
            </p>
          </div>
          
          <div class="card-footer"> 
            <div class="information-footer">
              <span class="card-status"><?= ucfirst($artikel['status']) ?></span> |
              <span class="card-date"><?= explode(' ', $artikel['tanggal_posting'])[0] ?></span>
            </div>
            <div class="card-list-btn">
              <a href="<?= $base_url ?>/admin/artikel/edit/<?= $artikel['id'] ?>">
                Edit 
              </a> |
              
              <form action="<?= $base_url ?>/admin/artikel/delete/<?= $artikel['id'] ?>" method="POST" onsubmit="return confirm('Yakin ingin menghapus artikel ini?')">
                <button type="submit" class="card-footer-hapus">Hapus</button>
              </form>
            </div>
          </div>

        </a>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p>Tidak ada artikel.</p>
  <?php endif; ?>
</div>

<?php if (!empty($artikels)): ?>
  <div class="pagination">
    <?php if ($currentPage > 1): ?>
      <a class="prev" href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>&kategori_id=<?= $kategori_id ?>">« Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
      <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&kategori_id=<?= $kategori_id ?>"
         class="<?= $i == $currentPage ? 'active' : '' ?>">
        <?= $i ?>
      </a>
    <?php endfor; ?>

    <?php if ($currentPage < $totalPages): ?>
      <a class="next" href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>&kategori_id=<?= $kategori_id ?>">Next »</a>
    <?php endif; ?>
  </div>
<?php endif; ?>
