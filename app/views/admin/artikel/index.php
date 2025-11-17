<?php

// Ambil keyword search

use App\Core\FlashMessage;

// $search = $_GET['search'] ?? '';
// $kategori_id = $_GET['kategori_id'] ?? '';

?>

<div class="artikel-container">
  <?= FlashMessage::show();?>
  
  <div class="top-bar">
    <div class="tambah-artikel">
      <h1>Daftar Artikel</h1> 
      <a href="<?=$base_url?>/admin/artikel/tambah"><i class="fa-solid fa-plus"></i></a>
    </div>

    <form class="search-form" method="get" action="<?=$base_url?>/admin/artikel" autocomplete="off">
      <div class="input-wrapper">
        <input type="text" name="search" placeholder="Cari artikel..." id="searchInput" value="<?= htmlspecialchars($search) ?>">
        <i id="searchIcon" class="fa fa-search"></i>
        <div id="suggestions" class="suggestions"></div>
      </div>
      
      <select name="kategori_id">
        <option value="">-Kategori-</option>
        <?php foreach($kategoriList as $kat): ?>
          <option value="<?= $kat['id'] ?>" <?= $kat['id'] == $kategori_id ? 'selected' : '' ?>>
            <?= htmlspecialchars($kat['nama']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button type="submit">Search</button>
    </form>
  </div>

  <div class="artikel-list">
    <?php include '_list.php'; ?>
  </div>
</div>
