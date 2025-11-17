<div class="form-container">
  <h1 class="form-title">Formulir Pendaftaran SMP Ansvin</h1>
  <p class="form-subtitle">Isi data singkat di bawah ini untuk melakukan pendaftaran siswa baru.</p>

  <form id="formPendaftaranSMP" action="<?= $base_url ?>/pendaftaran/smp/store" method="POST" class="form-card">
    <div class="form-group">
      <label for="nama_lengkap">Nama Lengkap</label>
      <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap">
    </div>

    <div class="form-group">
      <label for="tanggal_lahir">Tanggal Lahir</label>
      <input type="date" id="tanggal_lahir" name="tanggal_lahir">
    </div>

    <div class="form-group">
      <label for="alamat">Alamat</label>
      <textarea id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap"></textarea>
    </div>

    <div class="form-footer">
      <button type="submit" class="btn-daftar">Daftar Sekarang</button>
    </div>
  </form>
</div>


