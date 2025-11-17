<div class="main-content">
  <?php
  use App\Core\FlashMessage;
  FlashMessage::show();
  ?>

  <div class="dashboard-cards">
    <!-- Card Artikel -->
    <div class="card">
      <div class="card-icon"> 
        <i class="fa-solid fa-newspaper"></i>
      </div>
      <div class="card-content">
        <h3>Total Artikel</h3>
        <p class="card-value"><?= htmlspecialchars($articleCount ?? '0') ?></p>
        <p class="card-subtext">
          Artikel terbaru: <b><?= htmlspecialchars($latestArticle['judul'] ?? '-') ?></b>
        </p>
        <a href="<?= $base ?>/admin/artikel" class="card-link">Lihat Artikel</a>
      </div>
    </div>
    
    <div class="card">
      <div class="card-icon">
        <i class="fa-solid fa-newspaper"></i>
      </div>
      <div class="card-content">
        <h3>Total Artikel</h3>
        <p class="card-value"><?= htmlspecialchars($articleCount ?? '0') ?></p>
        <p class="card-subtext">
          Artikel terbaru: <b><?= htmlspecialchars($latestArticle['judul'] ?? '-') ?></b>
        </p>
        <a href="<?= $base ?>/admin/artikel" class="card-link">Lihat Artikel</a>
      </div>
    </div>
    
    <div class="card">
      <div class="card-icon">
        <i class="fa-solid fa-newspaper"></i>
      </div>
      <div class="card-content">
        <h3>Total Artikel</h3>
        <p class="card-value"><?= htmlspecialchars($articleCount ?? '0') ?></p>
        <p class="card-subtext">
          Artikel terbaru: <b><?= htmlspecialchars($latestArticle['judul'] ?? '-') ?></b>
        </p>
        <a href="<?= $base ?>/admin/artikel" class="card-link">Lihat Artikel</a>
      </div>
    </div>
    
    <div class="card">
      <div class="card-icon">
        <i class="fa-solid fa-newspaper"></i>
      </div>
      <div class="card-content">
        <h3>Total Artikel</h3>
        <p class="card-value"><?= htmlspecialchars($articleCount ?? '0') ?></p>
        <p class="card-subtext">
          Artikel terbaru: <b><?= htmlspecialchars($latestArticle['judul'] ?? '-') ?></b>
        </p>
        <a href="<?= $base ?>/admin/artikel" class="card-link">Lihat Artikel</a>
      </div>
    </div>
  </div>
</div>
