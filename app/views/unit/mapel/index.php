<div class="mapel-container">

  <div class="mapel-top"> 

    <div class="mapel-top-kiri">
      <h2><?= $title ?></h2>
      <button id="btnMapelAdd"><i class="fa-solid fa-plus"></i></button>
    </div>

    <div class="mapel-top-kanan">
      <form class="search-form-mapel" method="get" autocomplete="off">
        <div class="input-wrapper-mapel">
          <input type="text" name="searchMapel" placeholder="Cari mapel..." id="searchInputMapel" value="">
          <i id="searchIconMapel" class="fa fa-search"></i>
          <div id="suggestionsMapel" class="suggestionsMapel"></div>
        </div>

        <button type="submit">Search</button>
      </form>
    </div>

  </div>

  <table id="tableMapel" class="table-mapel">
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Mapel</th>
        <th>Kode</th>
        <th>Tingkat Min</th>
        <th>Tingkat Max</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <tr class="empty">
        <td colspan="6">Memuat data...</td>
      </tr>
    </tbody>
  </table>

  <div id="paginationMapel" class="pagination-mapel"></div>

  <!-- Modal -->
  <div id="mapelModal" class="modal-mapel">
    <div class="modal-content-mapel">

      <div class="modal-mapel-top">
        <h3 id="modalTitle"></h3>
        <i class="fas fa-x"></i>
      </div>

      <form id="formMapel">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
        <input type="hidden" name="id" id="mapelId">

        <label>Nama Mapel</label>
        <input type="text" id="namaMapel" name="nama_mapel" class="input-text" autocomplete="off">
        <small class="error-text" id="err-nama_mapel"></small>

        <label>Kode Mapel </label>
        <input type="text" id="kodeMapel" name="kode_mapel" class="input-text" autocomplete="off">
        <small class="error-text" id="err-kode_mapel"></small>

        <label>Tingkat Min</label>
        <input type="number" id="tingkatMin" name="tingkat_min" class="input-text" autocomplete="off">
        <small class="error-text" id="err-tingkat_min"></small>

        <label>Tingkat Max</label>
        <input type="number" id="tingkatMax" name="tingkat_max" class="input-text" autocomplete="off">
        <small class="error-text" id="err-tingkat_max"></small>

        <button type="submit" id="btnSaveMapel">Simpan</button>

      </form>

    </div>
  </div>