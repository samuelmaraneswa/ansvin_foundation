<div class="slug-container">
  <div class="slug-container-top">

    <div class="slug-container-top-kiri">
      <h2><?= $title ?></h2>
      <button id="btnUnitSlugAdd"><i class="fa-solid fa-plus"></i></button>
    </div>

    <div class="slug-container-top-kanan">
      <form class="search-form-unitSlug" method="get" autocomplete="off">
        <div class="input-wrapper-slug">
          <input type="text" name="searchGuruMapel" placeholder="Cari guru atau mapel..." id="searchInputGuruMapel" value="">
          <i id="searchIconGuruMapel" class="fa fa-search"></i>
          <div id="suggestionsGuruMapel" class="suggestionsSlug"></div>
        </div>

        <button type="submit">Search</button>
      </form>
    </div>

  </div>

  <table class="table-slug" id="unitGuruMapelTable">
    <thead>
      <tr>
        <th>No</th>
        <th>Guru</th>
        <th>Mata Pelajaran</th>
        <th>Tahun Ajaran</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <tr class="empty">
        <td colspan="5" class="text-center">Memuat data...</td>
      </tr>
    </tbody>
  </table>

  <div id="paginationUnitSlug" class="pagination"></div>
</div>

<div id="unitGuruMapelModal" class="unit-modal-slug">
  <div class="modal-slug-content">

    <div class="modal-slug-top">
      <h3 id="modal-title"></h3>
      <i class="fas fa-x"></i>
    </div>

    <form id="formGuruMapel">
      <!-- CSRF Token -->
      <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

      <!-- Hidden input untuk edit mode -->
      <input type="hidden" id="id" name="guru-mapel-id" value="">

      <!-- Guru -->
      <label>Guru</label>
      <select name="guru">
        <option value="">- Pilih Guru -</option>
      </select>
      <small class="error-text" id="err-guru"></small>

      <!-- Mapel -->
      <label>Mata Pelajaran</label>
      <select name="mapel">
        <option value="">- Pilih Mapel -</option>
      </select>
      <small class="error-text" id="err-mapel"></small>

      <!-- Tahun ajaran -->
      <label>Tahun Ajaran</label>
      <select name="tahun_ajaran">
        <option value="">- Pilih Tahun Ajaran -</option>
      </select>
      <small class="error-text" id="err-tahun_ajaran"></small>

      <button type="submit">Simpan</button>
    </form>
  </div>
</div>