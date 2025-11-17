<?php
namespace App\Models;

use App\Core\Database;

class CalonSiswa extends Database
{
  protected $db;
  protected $table = 'calon_siswa';

  public function __construct()
  {
    $this->db = Database::connect();
  }
  /**
   * Menyimpan data calon siswa baru
   * @param array $data
   * @return int ID calon siswa yang baru disimpan
   */
  public function insert(array $data): int
  {
    $cols = array_keys($data);
    $placeholders = array_map(fn($col) => ':' . $col, $cols);
    $sql = "INSERT INTO {$this->table} (" . implode(',', $cols) . ") VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $this->db->prepare($sql);
    $stmt->execute($data);
    return $this->db->lastInsertId();
  }

  /**
   * Mengambil nomor urut terakhir untuk unit dan tahun ajaran tertentu
   * Lalu menghasilkan nomor pendaftaran baru
   * Format: ANV + 2digitTahun + UnitCode + NomorUrut
   * Contoh: ANV25SMP0001
   */
  public function generateNoPendaftaran(int $unit_id, int $tahun_ajaran_id): string
  {
    // Mapping kode unit
    $unitCode = match ($unit_id) { 
        1 => 'TK',
        2 => 'SD',
        3 => 'SMP',
        4 => 'SMA',
        default => 'XX'
    };

    // Ambil 2 digit terakhir dari tahun ajaran aktif
    $stmtTahun = $this->db->prepare("SELECT tahun_mulai FROM tahun_ajaran WHERE id = :id LIMIT 1");
    $stmtTahun->execute(['id' => $tahun_ajaran_id]);
    $tahun = $stmtTahun->fetchColumn();

    if (!$tahun) {
      throw new \Exception("Tahun ajaran dengan ID {$tahun_ajaran_id} tidak ditemukan");
    }

    // $tahunShort = substr((string)$tahun, -2);
    $tahunShort = $tahun ? substr((string)$tahun, -2) : date('y');

    // Ambil kode terakhir
    $stmt = $this->db->prepare("
        SELECT no_pendaftaran 
        FROM calon_siswa
        WHERE unit_id = :unit_id AND tahun_ajaran_id = :tahun_ajaran_id
        ORDER BY id DESC 
        LIMIT 1
    ");
    $stmt->execute([
        'unit_id' => $unit_id,
        'tahun_ajaran_id' => $tahun_ajaran_id
    ]);
    $lastCode = $stmt->fetchColumn();

    $nextNumber = ($lastCode && preg_match('/(\d{4})$/', $lastCode, $m))
        ? ((int)$m[1] + 1)
        : 1;

    $urutan = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    return "ANV{$tahunShort}{$unitCode}{$urutan}";
  }

  public function generateNIS(int $unit_id, int $tahun_ajaran_id): string
  {
    // Mapping kode unit
    $unitCode = match ($unit_id) { 
        1 => 'TK',
        2 => 'SD',
        3 => 'SMP',
        4 => 'SMA',
        default => 'XX'
    };

    // Ambil 2 digit terakhir dari tahun ajaran aktif
    $stmtTahun = $this->db->prepare("SELECT tahun_mulai FROM tahun_ajaran WHERE id = :id LIMIT 1");
    $stmtTahun->execute(['id' => $tahun_ajaran_id]);
    $tahun = $stmtTahun->fetchColumn();

    if (!$tahun) {
        throw new \Exception("Tahun ajaran dengan ID {$tahun_ajaran_id} tidak ditemukan");
    }

    $tahunShort = substr((string)$tahun, -2);

    // Ambil NIS terakhir
    $stmt = $this->db->prepare("
        SELECT nis
        FROM siswa
        WHERE unit_id = :unit_id AND tahun_ajaran_id = :tahun_ajaran_id
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmt->execute([
        'unit_id' => $unit_id,
        'tahun_ajaran_id' => $tahun_ajaran_id
    ]);
    $lastNis = $stmt->fetchColumn();

    // Hitung urutan berikutnya
    $nextNumber = ($lastNis && preg_match('/(\d{4})$/', $lastNis, $m))
        ? ((int)$m[1] + 1)
        : 1;

    $urutan = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    return "ANV{$tahunShort}{$unitCode}{$urutan}";
  }

  /**
   * Mendapatkan data calon siswa berdasarkan no_pendaftaran
   */ 
  public function getByNo(string $no_pendaftaran): ?array
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE no_pendaftaran = :no");
    $stmt->execute(['no' => $no_pendaftaran]);
    return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
  }

  // public function getAllWithBilling(): array 
  // {
  //   $sql = "
  //       SELECT 
  //           cs.id,
  //           cs.no_pendaftaran,
  //           cs.nama_lengkap,
  //           cs.status_pendaftaran,
  //           bp.status_bayar,
  //           bp.total_tagihan,
  //           bp.total_bayar,
  //           bp.sisa_tagihan
  //       FROM calon_siswa AS cs
  //       LEFT JOIN billing_pendaftaran AS bp 
  //           ON cs.id = bp.calon_siswa_id
  //       ORDER BY cs.id DESC
  //   ";
  //   $stmt = $this->db->query($sql);
  //   return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  // }

  public function getAllWithBilling(int $limit = 5, int $offset = 0): array 
  {
    $sql = "
        SELECT 
            cs.id,
            cs.no_pendaftaran,
            cs.nama_lengkap,
            cs.status_pendaftaran,
            bp.status_bayar,
            bp.total_tagihan,
            bp.total_bayar,
            bp.sisa_tagihan
        FROM calon_siswa AS cs
        LEFT JOIN billing_pendaftaran AS bp 
            ON cs.id = bp.calon_siswa_id
        ORDER BY cs.id DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function countAll(): int
  {
    $stmt = $this->db->query("SELECT COUNT(*) FROM calon_siswa");
    return (int) $stmt->fetchColumn();
  }

  public function getBillingByCalonId($id)
  {
    $stmt = $this->db->prepare("SELECT * FROM billing_pendaftaran WHERE calon_siswa_id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
  }

  public function updateBillingStatus($calon_id, array $data)
  {
    $sql = "UPDATE billing_pendaftaran
            SET status_bayar = :status_bayar,
                total_bayar = :total_bayar,
                sisa_tagihan = :sisa_tagihan,
                tanggal_bayar = :tanggal_bayar
            WHERE calon_siswa_id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
      ':status_bayar' => $data['status_bayar'],
      ':total_bayar' => $data['total_bayar'],
      ':sisa_tagihan' => $data['sisa_tagihan'],
      ':tanggal_bayar' => $data['tanggal_bayar'],
      ':id' => $calon_id
    ]);
  }

  public function updateStatusPendaftaran($id, $status)
  {
    $stmt = $this->db->prepare("UPDATE calon_siswa SET status_pendaftaran = :status WHERE id = :id");
    $stmt->execute([':status' => $status, ':id' => $id]);
  }

  public function aktivasiSiswaBaru($calon_id)
  {
    // 1️⃣ Ambil data calon siswa
    $stmt = $this->db->prepare("SELECT * FROM calon_siswa WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $calon_id]);
    $calon = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$calon) {
      throw new \Exception("Data calon siswa tidak ditemukan.");
    }

    $stmtBilling = $this->db->prepare("SELECT * FROM billing_pendaftaran WHERE calon_siswa_id = :id LIMIT 1");
    $stmtBilling->execute(['id' => $calon_id]);
    $billing = $stmtBilling->fetch(\PDO::FETCH_ASSOC);

    if (!$billing) {
      throw new \Exception("Data billing pendaftaran tidak ditemukan.");
    }

    // 2️⃣ Ambil tahun ajaran aktif
    $tahunAjaranStmt = $this->db->query("SELECT * FROM tahun_ajaran WHERE aktif = 1 LIMIT 1");
    $tahunAjaran = $tahunAjaranStmt->fetch(\PDO::FETCH_ASSOC);
    if (!$tahunAjaran) {
      throw new \Exception("Tahun ajaran aktif tidak ditemukan.");
    }

    $cekSiswa = $this->db->prepare("SELECT id, status_keuangan FROM siswa WHERE calon_siswa_id = :cid LIMIT 1");
    $cekSiswa->execute([':cid' => $calon_id]);
    $siswaExist = $cekSiswa->fetch(\PDO::FETCH_ASSOC);
    
    $statusKeuangan = 'MENUNGGU_PEMBAYARAN';
    if ($billing['status_bayar'] === 'CICIL') {
      $statusKeuangan = 'CICIL';
    } elseif ($billing['status_bayar'] === 'LUNAS') {
      $statusKeuangan = 'LUNAS';
    }

    if ($siswaExist) {
      $update = $this->db->prepare("
          UPDATE siswa 
          SET status_keuangan = :status_keuangan, updated_at = NOW()
          WHERE id = :id
      ");
      $update->execute([
          ':status_keuangan' => $statusKeuangan,
          ':id' => $siswaExist['id']
      ]);

      // Jika baru LUNAS → generate SPP
      if ($billing['status_bayar'] === 'LUNAS' && $siswaExist['status_keuangan'] !== 'AKTIF') {
        $this->generateSPP($siswaExist['id'], $calon['unit_id'], $tahunAjaran);
      }

      return; // ✅ selesai, tidak buat duplikat siswa
    }
    
    $nis = $this->generateNIS($calon['unit_id'], $tahunAjaran['id']);

    // 3️⃣ Pindahkan ke tabel siswa
    $insertSiswa = $this->db->prepare("
      INSERT INTO siswa (calon_siswa_id,nama_lengkap, nis, tanggal_lahir, alamat, unit_id, tahun_ajaran_id, kelas_id, foto, status, status_keuangan, created_at, updated_at)
      VALUES (:calon_siswa_id,:nama_lengkap,:nis, :tanggal_lahir, :alamat, :unit_id, :tahun_ajaran_id,:kelas_id, :foto, :status, :status_keuangan, NOW(), NOW())
    ");
    $insertSiswa->execute([
      ':calon_siswa_id' => $calon_id,
      ':nama_lengkap' => $calon['nama_lengkap'],
      ':nis' => $nis,
      ':tanggal_lahir' => $calon['tanggal_lahir'],
      ':alamat' => $calon['alamat'],
      ':unit_id' => $calon['unit_id'],
      ':tahun_ajaran_id' => $tahunAjaran['id'],
      ':kelas_id'        => null, // sementara belum ditentukan
      ':foto'            => 'default_img.jpg',
      ':status'          => 'AKTIF',
      ':status_keuangan' => $statusKeuangan,
    ]);

    $siswa_id = $this->db->lastInsertId();

    // 4️⃣ Ambil biaya SPP dari template biaya aktif
    if ($billing['status_bayar'] === 'LUNAS') {
      $this->generateSPP($siswa_id, $calon['unit_id'], $tahunAjaran);
    }
  }

  private function generateSPP($siswa_id, $unit_id, $tahunAjaran)
  {
    $sqlSPP = "
      SELECT btm.id AS master_id, btd.nominal
      FROM biaya_template_master AS btm
      JOIN biaya_template_detail AS btd ON btd.template_id = btm.id
      WHERE btm.unit_id = :unit_id 
        AND btm.tahun_ajaran_id = :tahun_ajaran_id
        AND btm.status = 1 
        AND btd.kategori = 'SPP'
      LIMIT 1
    ";
    $stmtSPP = $this->db->prepare($sqlSPP);
    $stmtSPP->execute([
      ':unit_id' => $unit_id,
      ':tahun_ajaran_id' => $tahunAjaran['id']
    ]);
    $biayaSPP = $stmtSPP->fetch(\PDO::FETCH_ASSOC);

    if (!$biayaSPP) {
      throw new \Exception("Template biaya SPP tidak ditemukan untuk unit ini.");
    }

    $nominalSPP = (float) $biayaSPP['nominal'];
    $master_id = (int) $biayaSPP['master_id'];
    $bulanMulai = 7;
    $tahunMulai = (int) $tahunAjaran['tahun_mulai'];

    for ($i = 0; $i < 12; $i++) {
      $bulan = $bulanMulai + $i;
      $tahun = $tahunMulai;
      if ($bulan > 12) { $bulan -= 12; $tahun++; }

      $status = ($i === 0) ? 'LUNAS' : 'BELUM';
      $total_bayar = ($i === 0) ? $nominalSPP : 0.00;
      $sisa_tagihan = ($i === 0) ? 0.00 : $nominalSPP;
      $namaBulan = \DateTime::createFromFormat('!m', $bulan)->format('F');
      $description = "SPP Bulan {$namaBulan}";

      $stmtInsert = $this->db->prepare("
        INSERT INTO billing_assignment_siswa 
          (billing_master_id, siswa_id, type, description, tahun_ajaran_id, periode_bulan, periode_tahun,
            total_tagihan, total_bayar, sisa_tagihan, status_bayar, is_recurring, created_at)
        VALUES
          (:master_id, :siswa_id, 'SPP', :description, :ta_id, :periode_bulan, :periode_tahun,
            :total_tagihan, :total_bayar, :sisa_tagihan, :status_bayar, 1, NOW())
      ");

      $stmtInsert->execute([
        ':master_id' => $master_id,
        ':siswa_id' => $siswa_id,
        ':description' => $description,
        ':ta_id' => $tahunAjaran['id'],
        ':periode_bulan' => $bulan,
        ':periode_tahun' => $tahun,
        ':total_tagihan' => $nominalSPP,
        ':total_bayar' => $total_bayar,
        ':sisa_tagihan' => $sisa_tagihan,
        ':status_bayar' => $status
      ]);
    }
  }

  public function findById($id): ?array
  {
    $stmt = $this->db->prepare("
        SELECT 
            cs.id,
            cs.no_pendaftaran,
            cs.nama_lengkap,
            cs.status_pendaftaran,
            b.status_bayar,
            b.total_tagihan,
            b.total_bayar,
            b.sisa_tagihan
        FROM calon_siswa AS cs
        LEFT JOIN billing_pendaftaran AS b ON b.calon_siswa_id = cs.id
        WHERE cs.id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
  }

  public function getAllWithBillingByUnit(int $unit_id): array
  {
    $stmt = $this->db->prepare("
        SELECT 
            cs.id,
            cs.no_pendaftaran,
            cs.nama_lengkap,
            cs.status_pendaftaran,
            b.status_bayar,
            b.total_tagihan,
            b.total_bayar,
            b.sisa_tagihan
        FROM calon_siswa AS cs
        LEFT JOIN billing_pendaftaran AS b 
            ON b.calon_siswa_id = cs.id
        WHERE cs.unit_id = :unit_id
        ORDER BY cs.id DESC
    ");
    $stmt->execute(['unit_id' => $unit_id]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getById(int $id): ?array
  {
    $stmt = $this->db->prepare("
        SELECT 
            cs.*,
            b.status_bayar,
            b.total_tagihan,
            b.total_bayar,
            b.sisa_tagihan
        FROM calon_siswa AS cs
        LEFT JOIN billing_pendaftaran AS b 
            ON b.calon_siswa_id = cs.id
        WHERE cs.id = :id
        LIMIT 1
    ");
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);

    return $result ?: null;
  }

  // public function search(string $keyword): array
  // {
  //   $stmt = $this->db->prepare("
  //       SELECT 
  //           cs.id,
  //           cs.no_pendaftaran,
  //           cs.nama_lengkap,
  //           cs.status_pendaftaran,
  //           u.nama AS unit,
  //           ta.nama_tahun AS tahun_ajaran,
  //           bp.status_bayar,
  //           bp.total_tagihan,
  //           bp.total_bayar,
  //           bp.sisa_tagihan
  //       FROM calon_siswa AS cs
  //       LEFT JOIN unit_sekolah AS u ON cs.unit_id = u.id
  //       LEFT JOIN tahun_ajaran AS ta ON cs.tahun_ajaran_id = ta.id
  //       LEFT JOIN billing_pendaftaran AS bp ON bp.calon_siswa_id = cs.id
  //       WHERE cs.nama_lengkap LIKE :keyword
  //       ORDER BY cs.nama_lengkap ASC
  //       LIMIT 10
  //   "); 

  //   $stmt->execute([
  //       ':keyword' => '%' . $keyword . '%'
  //   ]);

  //   return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  // }

 public function search(string $keyword, int $limit = 5, int $offset = 0): array
  {
    $stmt = $this->db->prepare("
        SELECT 
          cs.id,
          cs.no_pendaftaran,
          cs.nama_lengkap,
          cs.status_pendaftaran,
          u.nama AS unit,
          ta.nama_tahun AS tahun_ajaran,
          bp.status_bayar,
          bp.total_tagihan,
          bp.total_bayar,
          bp.sisa_tagihan
        FROM calon_siswa AS cs
        LEFT JOIN unit_sekolah AS u ON cs.unit_id = u.id
        LEFT JOIN tahun_ajaran AS ta ON cs.tahun_ajaran_id = ta.id
        LEFT JOIN billing_pendaftaran AS bp ON bp.calon_siswa_id = cs.id
        WHERE cs.nama_lengkap LIKE :keyword
        ORDER BY cs.id DESC
        LIMIT :limit OFFSET :offset
    ");

    $stmt->bindValue(':keyword', "%{$keyword}%", \PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function countSearch(string $keyword): int
  {
    $stmt = $this->db->prepare("SELECT COUNT(*) FROM calon_siswa WHERE nama_lengkap LIKE :keyword");
    $stmt->execute([':keyword' => "%{$keyword}%"]);
    return (int) $stmt->fetchColumn();
  }

}
