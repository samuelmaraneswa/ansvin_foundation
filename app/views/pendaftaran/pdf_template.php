<?php
$base_url = \App\Core\Config::get('base_url');
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <style>
    <?= file_get_contents(__DIR__ . '/../../../public/assets/css/pendaftaran.css') ?>
  </style>

</head>
<body>
  <div class="pdf-body">

    <div class="header">
      <h2>Yayasan Ansvin Foundation</h2>
      <h3><?= htmlspecialchars($dataCalon['nama_unit'] ?? 'SMP Ansvin') ?></h3>
      <h4>Billing Pendaftaran Siswa Baru</h4>
    </div>

    <div class="info">
      <p><strong>Nomor Pendaftaran:</strong> <?= htmlspecialchars($dataCalon['no_pendaftaran']) ?></p>
      <p><strong>Nama Calon Siswa:</strong> <?= htmlspecialchars($dataCalon['nama_lengkap']) ?></p>
      <p><strong>Tahun Ajaran:</strong> <?= htmlspecialchars($tahunAjaran['nama_tahun'] ?? '-') ?></p>
    </div>

    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Rincian Biaya</th>
          <th>Nominal (Rp)</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; foreach ($items as $i): ?>
          <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($i['item']) ?></td>
            <td style="text-align:right;"><?= number_format($i['nominal'], 0, ',', '.') ?></td>
          </tr>
        <?php endforeach; ?>
        <tr>
          <td colspan="2" class="total">Total Tagihan</td>
          <td style="text-align:right; font-weight:bold;"><?= number_format($totalTagihan, 0, ',', '.') ?></td>
        </tr>
      </tbody>
    </table>

    <div class="footer">
      <p>Silakan melakukan pembayaran ke rekening berikut:</p>
      <p><strong><?= $rekening['nama_bank'] ?? '-' ?> - <?= $rekening['no_rekening'] ?? '-' ?></strong></p>
      <p>a.n. <?= $rekening['nama_pemilik'] ?? '-' ?></p>
      <p style="margin-top:20px;">Dicetak pada: <?= date('d F Y') ?></p>
    </div>

  </div>
</body>

</html>
