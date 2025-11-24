<div class="pegawai-container">
  <div class="pegawai-container-top">

    <div class="pegawai-container-top-kiri">
      <h2><?=$title?> SMP Ansvin</h2>
      <button id="btn-unitPegawai-Add"><i class="fa-solid fa-plus"></i></button>
    </div>

    <div class="pegawai-container-top-kanan">
      <form class="search-form-unitPegawai" method="get" action="<?=$base_url?>/unit/{slug}/pegawai" autocomplete="off">
        <div class="input-wrapper-pegawai">
          <input type="text" name="searchPegawai" placeholder="Cari pegawai..." id="searchInputUnitPegawai" value="">
          <i id="searchIconUnitPegawai" class="fa fa-search"></i>
          <div id="suggestionsUnitPegawai" class="suggestionsPegawai"></div>
        </div>

        <button type="submit">Search</button>
      </form>
    </div>

  </div>

  <table class="table-pegawai">
    <thead>
      <tr>
        <th>No</th>
        <th>NIP</th>
        <th>Nama</th>
        <th>Jabatan</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody id="unitPegawaiTableBody">
      <tr><td colspan="5" class="text-center">Memuat data...</td></tr>
    </tbody>
  </table>

  <!-- 🔹 Tambahkan ini -->
  <div id="paginationUnitPegawai" class="pagination"></div>
</div>

<div id="adminUnitModalDiv" class="unitModal" style="display: none;">
  <div class="modalUnit-content">
    <div class="modalUnit-inner">
      <span class="closeUnit">&times;</span>
      <h2 class="titlePegawai"></h2>

      <form id="formAddUnitPegawai" enctype="multipart/form-data" method="POST" action="<?= $base_url ?>/unit/{slug}/pegawai/store">
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <!-- Hidden input untuk edit mode -->
        <input type="hidden" name="pegawaiUnit_id" value=""> 

        <!-- NIP -->
        <label>NIP</label>
        <input class="nip-input" type="text" name="nipUnit" value="<?= htmlspecialchars($generatedNIP ?? ($old['nipUnit'] ?? '')) ?>" readonly required>
        <?php if (!empty($errors['nipUnit'])): ?>
          <small class="error-msg-unit"><?= htmlspecialchars($errors['nipUnit']) ?></small>
        <?php endif; ?>
        
        <!-- Nama -->
        <label>Nama</label>
        <input type="text" name="namaUnit" value="<?= htmlspecialchars($old['namaUnit'] ?? '') ?>">

        <!-- Jabatan -->
        <label>Jabatan</label>
        <select name="jabatan_id_unit">
          <option value="">- Pilih Jabatan -</option>
          <?php foreach($jabatan as $jab): ?>
            <option value="<?= $jab['id'] ?>" <?= isset($old['jabatan_id_unit']) && $old['jabatan_id_unit'] == $jab['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($jab['nama']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <!-- Email -->
        <label>Email</label>
        <input type="email" name="email_unit" value="<?= htmlspecialchars($old['email_unit'] ?? '') ?>">

        <!-- Telepon -->
        <label>Telepon</label>
        <input type="text" name="telepon_unit" value="<?= htmlspecialchars($old['telepon_unit'] ?? '') ?>">
        <?php if (!empty($errors['telepon'])): ?>
          <small class="error-msg-unit"><?= htmlspecialchars($errors['telepon_unit']) ?></small>
        <?php endif; ?>

        <!-- Tanggal Lahir -->
        <label>Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir_unit" value="<?= htmlspecialchars($old['tanggal_lahir_unit'] ?? '') ?>">
        <?php if (!empty($errors['tanggal_lahir_unit'])): ?>
          <small class="error-msg-unit"><?= htmlspecialchars($errors['tanggal_lahir_unit']) ?></small>
        <?php endif; ?>

        <!-- Alamat -->
        <label>Alamat</label>
        <textarea name="alamat_unit"><?= htmlspecialchars($old['alamat_unit'] ?? '') ?></textarea>
        <?php if (!empty($errors['alamat_unit'])): ?>
          <small class="error-msg-unit"><?= htmlspecialchars($errors['alamat_unit']) ?></small>
        <?php endif; ?>

        <!-- Status Aktif -->
        <label>Status Aktif</label>
        <select name="status_aktif_unit">
          <option value="1" <?= (!isset($old['status_aktif_unit']) || $old['status_aktif_unit'] == '1') ? 'selected' : '' ?>>Aktif</option>
          <option value="0" <?= (isset($old['status_aktif_unit']) && $old['status_aktif_unit'] == '0') ? 'selected' : '' ?>>Tidak Aktif</option>
        </select>
        <?php if (!empty($errors['status_aktif_unit'])): ?>
          <small class="error-msg-unit"><?= htmlspecialchars($errors['status_aktif_unit']) ?></small>
        <?php endif; ?>

        <!-- Foto -->
        <label>Foto</label>
        <input type="file" name="foto_unit" accept="image/*">
        <img id="foto-preview_unit" src="<?=$base_url?>/uploads/pegawai/default_img.jpg" style="width:100px; margin-top:5px;">
        <?php if (!empty($errors['foto_unit'])): ?>
          <small class="error-msg-unit"><?= htmlspecialchars($errors['foto_unit']) ?></small>
        <?php endif; ?>

        <!-- Password -->
        <label>Password</label>
        <input type="password" name="password_unit" readonly>
        <?php if (!empty($errors['password_unit'])): ?>
          <small class="error-msg-unit"><?= htmlspecialchars($errors['password_unit']) ?></small>
        <?php endif; ?>

        <button type="submit" class="btn-simpan">Simpan</button>
      </form>
    </div>
  </div>

</div>