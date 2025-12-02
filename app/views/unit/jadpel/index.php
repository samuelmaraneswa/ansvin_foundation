<div class="slug-container jadpel">
  <div class="slug-container-top">

    <div class="slug-container-top-kiri">
      <h2><?= $title ?></h2>
      <button id="btnJadpelAdd"><i class="fa-solid fa-plus"></i></button>
    </div>

    <div class="slug-container-top-kanan">
      <form class="search-form-unitSlug" method="get" autocomplete="off">
        <div class="input-wrapper-slug">
          <input type="text" name="searchJadpel" placeholder="Cari hari atau kelas atau guru atau mapel..." id="searchInputJadpel" value="">
          <i id="searchIconJadpel" class="fa fa-search"></i>
          <div id="suggestionsJadpel" class="suggestionsSlug"></div>
        </div>

        <button type="submit">Search</button>
      </form>
    </div>

  </div>

  <div class="table-wrapper-slug">
    <table class="table-slug" id="unitJadpelTable">
      <thead>
        <tr>
          <th>No</th>
          <th>Hari</th>
          <th>Kelas</th>
          <th>Jam Mulai</th>
          <th>Jam Selesai</th>
          <th>Mata Pelajaran</th>
          <th>Guru</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>

  <div id="paginationJadpel" class="pagination"></div>
</div>

<div id="unitJadpelModal" class="unit-modal-slug">
  <div class="modal-slug-content">

    <div class="modal-slug-top">
      <h3 id="modal-jadpel-title"></h3>
      <i class="fas fa-x"></i>
    </div>

    <form id="formJadpel">
      <!-- CSRF Token -->
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

      <!-- Hidden input untuk edit mode -->
      <input type="hidden" id="jadpel-id" name="jadpel-id" value="">

      <!-- Hari -->
      <label>Hari</label>
      <select name="hari">
        <option value="">- Pilih Hari -</option>
        <option value="Senin">Senin</option>
        <option value="Selasa">Selasa</option>
        <option value="Rabu">Rabu</option>
        <option value="Kamis">Kamis</option>
        <option value="Jumat">Jumat</option>
        <option value="Sabtu">Sabtu</option>
      </select>
      <small class="error-text" id="err-hari"></small>

      <!-- Kelas -->
      <label>Kelas</label>
      <select name="kelas_id" id="selectKelas">
        <option value="">- Pilih Kelas -</option>
      </select>
      <small class="error-text" id="err-kelas"></small>

      <!-- Jam Mulai -->
      <label>Jam Mulai</label>
      <input type="text" name="jam_mulai" placeholder="HH:MM" autocomplete="off">
      <small class="error-text" id="err-jam-mulai"></small>

      <!-- Jam Selesai -->
      <label>Jam Selesai</label>
      <input type="text" name="jam_selesai" placeholder="HH:MM" autocomplete="off">
      <small class="error-text" id="err-jam-selesai"></small>

      <!-- Guru Mapel -->
      <label>Guru & Mapel</label>
      <select name="guru_mapel_id" id="selectGuruMapel">
        <option value="">- Pilih Guru & Mapel -</option>
      </select>
      <small class="error-text" id="err-guru-mapel"></small>

      <button type="submit">Simpan</button>
    </form>
  </div>
</div>