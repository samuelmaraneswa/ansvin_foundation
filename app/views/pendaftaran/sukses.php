<?php

use App\Core\FlashMessage;
?>
<?=FlashMessage::show();?>

<div class="success-container">
  <div class="success-card">
    <div class="success-icon">
      <i class="fas fa-check-circle"></i>
    </div>
 
    <h1>Pendaftaran Berhasil!</h1>
    <p class="success-text">
      Terima kasih telah mendaftar di <strong>SMP Ansvin</strong>.<br>
      Berikut informasi pendaftaran Anda:
    </p>

    <div class="info-box">
      <p><strong>Nomor Pendaftaran:</strong></p>
      <h2 class="no-pendaftaran"><?= htmlspecialchars($kode ?? '-') ?></h2>

      <p><strong>Total Tagihan:</strong></p>
      <h3 class="total-tagihan">Rp <?= number_format($total_tagihan ?? 0, 0, ',', '.') ?></h3>
    </div>

    <div class="action-buttons">
      <!-- Tampil di tab baru -->
      <a href="<?= $base_url ?>/pendaftaran/pdf?kode=<?= urlencode($kode) ?>&view=1" target="_blank" class="btn-download">
        <i class="fas fa-file-pdf"></i> Lihat Billing (PDF)
      </a>

      <!-- Unduh langsung -->
      <a href="<?= $base_url ?>/pendaftaran/pdf?kode=<?= urlencode($kode) ?>&download=1" class="btn-download">
        <i class="fas fa-download"></i> Unduh Billing (PDF)
      </a>

      <a href="<?= $base_url ?>/pendaftaran" class="btn-back">
        <i class="fas fa-arrow-left"></i> Kembali ke Halaman Pendaftaran
      </a>
    </div>
  </div>
</div>
