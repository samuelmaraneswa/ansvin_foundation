<div class="main-content">
  <?php

  use App\Core\FlashMessage;

  FlashMessage::show();
  ?>

  <div class="dashboard-cards">
    <!-- Card Artikel -->
    <a href="<?= $base_url ?>/admin/artikel">
      <div class="card">
        <div class="card-icon">
          <i class="fa-solid fa-newspaper"></i>
        </div>
        <div class="card-content">
          <h2>Artikel</h2>
          <p class="card-value">Total: 1000 </p>
        </div>
      </div>
    </a>

    <a href="">
      <div class="card">
        <div class="card-icon icon-fasilitas">
          <i class="fa fa-building icon-building"></i>
        </div>
        <div class="card-content">
          <h2>Fasilitas</h2>
          <p class="card-value">Total: 1000 </p>
        </div>
      </div>
    </a>

    <a href="<?= $base_url ?>/admin/pegawai">
      <div class="card">
        <div class="card-icon icon-pegawai">
          <i class="fa-solid fa-user-group"></i>
        </div>
        <div class="card-content">
          <h2>Pegawai</h2>
          <p class="card-value">Total: 100 </p>
        </div>
      </div>
    </a>

    <a href="<?= $base_url ?>">
      <div class="card">
        <div class="card-icon icon-siswa">
          <i class="fas fa-user-graduate"></i>
        </div>
        <div class="card-content">
          <h2>Siswa</h2>
          <p class="card-value">Total: 1000 </p>
        </div>
      </div>
    </a>

  </div>
</div>