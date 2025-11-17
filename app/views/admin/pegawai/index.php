<div class="pegawai-container">
  <div class="pegawai-container-top">

    <div class="pegawai-container-top-kiri">
      <h2>Admin Unit</h2>
      <button id="btn-pegawai-Add"><i class="fa-solid fa-plus"></i></button>
    </div>

    <div class="pegawai-container-top-kanan">
      <form class="search-form-pegawai" method="get" action="<?=$base_url?>/admin/pegawai" autocomplete="off">
        <div class="input-wrapper-pegawai">
          <input type="text" name="searchPegawai" placeholder="Cari pegawai..." id="searchInputPegawai" value="">
          <i id="searchIconPegawai" class="fa fa-search"></i>
          <div id="suggestionsPegawai" class="suggestionsPegawai"></div>
        </div>
        
        <select name="unit_id_sekolah">
          <option value="">-Unit Sekolah-</option>
          <?php foreach($units as $unit): ?>
            <option value="<?= $unit['id'] ?>">
              <?= htmlspecialchars($unit['nama']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <button type="submit">Search</button>
      </form>
    </div>

  </div>

  <table id="adminUnitTable">
    <thead> 
      <tr>
        <th>No</th>
        <th>NIP</th>
        <th>Nama</th>
        <th>Unit</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody id="adminUnitTableBody">
      <?php if (!empty($pegawai)) : ?>
        <?php foreach ($pegawai as $index => $p) : ?>
          <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($p['nip']) ?></td>
            <td><?= htmlspecialchars(strtok($p['nama'], ' ')) ?></td>
            <td><?= htmlspecialchars($p['nama_unit'] ?? '-') ?></td>
            <td>
              <button class="btn-edit-pegawai" data-id="<?= $p['id'] ?>">Edit</button>
              <button class="btn-delete-pegawai" data-id="<?= $p['id'] ?>">Hapus</button>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else : ?>
      <tr class="empty">
        <td colspan="5">Belum ada data pegawai</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div id="paginationPegawai" class="pagination"></div>
</div>

<!-- Modal -->
<div id="adminUnitModal" class="modal" style="display:none;">
  <div class="modal-content">
    <div class="modal-inner">
      <span class="close">&times;</span>
      <h2 class="titlePegawai"></h2>

      <form id="formAddPegawai" enctype="multipart/form-data" method="POST" action="<?= $base_url ?>/admin/pegawai/store">
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <!-- Hidden input untuk edit mode -->
        <input type="hidden" name="pegawai_id" value="">

        <!-- Unit Sekolah -->
        <label>Unit Sekolah</label>
        <select name="unit_id">
          <option value="">- Pilih Unit Sekolah -</option>
          <?php foreach($units as $unit): ?>
            <option value="<?= $unit['id'] ?>" <?= isset($old['unit_id']) && $old['unit_id'] == $unit['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($unit['nama']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['unit_id'])): ?>
          <small class="error-msg"><?= htmlspecialchars($errors['unit_id']) ?></small>
        <?php endif; ?>

        <!-- NIP -->
        <label>NIP</label>
        <input class="nip-input" type="text" name="nip" value="<?= htmlspecialchars($generatedNIP ?? ($old['nip'] ?? '')) ?>" readonly required>
        <?php if (!empty($errors['nip'])): ?>
          <small class="error-msg"><?= htmlspecialchars($errors['nip']) ?></small>
        <?php endif; ?>

        <!-- Nama -->
        <label>Nama</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($old['nama'] ?? '') ?>">
        <?php if (!empty($errors['nama'])): ?>
          <small class="error-msg"><?= htmlspecialchars($errors['nama']) ?></small>
        <?php endif; ?>

        <!-- Jabatan -->
        <label>Jabatan</label>
        <select name="jabatan_id">
          <option value="">- Pilih Jabatan -</option>
          <?php foreach($jabatan as $jab): ?>
            <option value="<?= $jab['id'] ?>" <?= isset($old['jabatan_id']) && $old['jabatan_id'] == $jab['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($jab['nama']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (!empty($errors['jabatan_id'])): ?>
          <small class="error-msg"><?= htmlspecialchars($errors['jabatan_id']) ?></small>
        <?php endif; ?>

        <!-- Role -->
        <label>Role</label>
        <select name="role">
          <option value="">- Pilih Role -</option>
          <option value="super_admin" <?= (isset($old['role']) && $old['role'] === 'super_admin') ? 'selected' : '' ?>>Super Admin</option>
          <option value="admin_unit" <?= (isset($old['role']) && $old['role'] === 'admin_unit') ? 'selected' : '' ?>>Admin Unit</option>
          <option value="pegawai" <?= (isset($old['role']) && $old['role'] === 'pegawai') ? 'selected' : '' ?>>Pegawai</option>
          <option value="siswa" <?= (isset($old['role']) && $old['role'] === 'siswa') ? 'selected' : '' ?>>Siswa</option>
        </select>
        <?php if (!empty($errors['role'])): ?>
          <small class="error-msg"><?= htmlspecialchars($errors['role']) ?></small>
        <?php endif; ?>

        <!-- Email -->
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
        <?php if (!empty($errors['email'])): ?>
          <small class="error-msg"><?= htmlspecialchars($errors['email']) ?></small>
        <?php endif; ?>

        <!-- Telepon -->
        <label>Telepon</label>
        <input type="text" name="telepon" value="<?= htmlspecialchars($old['telepon'] ?? '') ?>">
        <?php if (!empty($errors['telepon'])): ?>
          <small class="error-msg"><?= htmlspecialchars($errors['telepon']) ?></small>
        <?php endif; ?>

        <!-- Tanggal Lahir -->
        <label>Tanggal Lahir</label>
        <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($old['tanggal_lahir'] ?? '') ?>">
        <?php if (!empty($errors['tanggal_lahir'])): ?>
          <small class="error-msg"><?= htmlspecialchars($errors['tanggal_lahir']) ?></small>
        <?php endif; ?>

        <!-- Alamat -->
        <label>Alamat</label>
        <textarea name="alamat"><?= htmlspecialchars($old['alamat'] ?? '') ?></textarea>
        <?php if (!empty($errors['alamat'])): ?>
          <small class="error-msg"><?= htmlspecialchars($errors['alamat']) ?></small>
        <?php endif; ?>

        <!-- Status Aktif -->
        <label>Status Aktif</label>
        <select name="status_aktif">
          <option value="1" <?= (!isset($old['status_aktif']) || $old['status_aktif'] == '1') ? 'selected' : '' ?>>Aktif</option>
          <option value="0" <?= (isset($old['status_aktif']) && $old['status_aktif'] == '0') ? 'selected' : '' ?>>Tidak Aktif</option>
        </select>
        <?php if (!empty($errors['status_aktif'])): ?>
          <small class="error-msg"><?= htmlspecialchars($errors['status_aktif']) ?></small>
        <?php endif; ?>

        <!-- Foto -->
        <label>Foto</label>
        <input type="file" name="foto" accept="image/*">
        <img id="foto-preview" src="<?=$base_url?>/uploads/pegawai/default_img.jpg" style="width:100px; margin-top:5px;">
        <?php if (!empty($errors['foto'])): ?>
          <small class="error-msg"><?= htmlspecialchars($errors['foto']) ?></small>
        <?php endif; ?>

        <!-- Password -->
        <label>Password</label>
        <input type="password" name="password" readonly>
        <?php if (!empty($errors['password'])): ?>
          <small class="error-msg"><?= htmlspecialchars($errors['password']) ?></small>
        <?php endif; ?>

        <button type="submit" class="btn-simpan">Simpan</button>
      </form>
    </div>
  </div>
</div>

