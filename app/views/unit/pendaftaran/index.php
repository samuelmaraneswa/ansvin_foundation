<div class="calon-siswa-container">
  <h4>Data Calon Siswa</h4>

  <!-- Tabel -->
  <div class="table-responsive">
    <table class="table table-bordered table-striped" id="table-calon-siswa">
      <thead class="table-light">
        <tr>
          <th>No</th>
          <th>Kode</th>
          <th>Nama</th>
          <th>Status Pendaftaran</th>
          <th>Status Pembayaran</th>
          <th>Total Tagihan</th>
          <th>Total Dibayar</th>
          <th>Sisa Tagihan</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td colspan="8" class="text-center text-muted">Memuat data...</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Ubah Status (tanpa Bootstrap) -->
<div id="modal-status" class="modal-overlay" style="display: none;">
  <div class="modal-box">
    <div class="modal-header">
      <h5>Ubah Status Pembayaran</h5>
      <button class="modal-close" id="close-modal">&times;</button>
    </div>

    <div class="modal-body">
      <form id="form-ubah-status"> 
        <input type="hidden" id="status-id" name="id">

        <label for="status-bayar">Status Pembayaran:</label>
        <select id="status-bayar" name="status_bayar" required>
          <option value="BELUM">BELUM</option>
          <option value="CICIL">CICIL</option>
          <option value="LUNAS">LUNAS</option>
        </select> 

        <div id="input-nominal" style="display:none;">
          <label for="nominal-bayar">Nominal Dibayar (Rp)</label>
          <input type="text" id="nominal-bayar" name="nominal_bayar"  placeholder="0" />
        </div>

        <div class="modal-actions">
          <button type="button" class="btn-cancel" id="cancel-modal">Batal</button>
          <button type="submit" class="btn-save">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
