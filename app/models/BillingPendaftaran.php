<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class BillingPendaftaran
{
  protected $db;
  protected $table = 'billing_pendaftaran';

  public function __construct()
  {
    $this->db = Database::connect();
  }

  /**
   * Insert data billing pendaftaran calon siswa
   */
  public function insert(array $data): int
  {
    $stmt = $this->db->prepare("
      INSERT INTO {$this->table} 
        (calon_siswa_id, total_tagihan, total_bayar, sisa_tagihan, status_bayar, tanggal_bayar)
      VALUES 
        (:calon_siswa_id, :total_tagihan, :total_bayar, :sisa_tagihan, :status_bayar, :tanggal_bayar)
    ");
    
    $stmt->execute([
      ':calon_siswa_id' => $data['calon_siswa_id'],
      ':total_tagihan'  => $data['total_tagihan'],
      ':total_bayar'    => $data['total_bayar'] ?? 0,
      ':sisa_tagihan'   => $data['sisa_tagihan'] ?? 0,
      ':status_bayar'   => $data['status_bayar'] ?? 'BELUM',
      ':tanggal_bayar'  => null,
    ]);

    return (int) $this->db->lastInsertId();
  }

  /**
   * Ambil total tagihan calon siswa
   */
  public function getTotalByCalonSiswa(int $calon_id): float
  {
    $stmt = $this->db->prepare("SELECT total_tagihan FROM {$this->table} WHERE calon_siswa_id = :id LIMIT 1");
    $stmt->execute([':id' => $calon_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return (float) ($result['total_tagihan'] ?? 0);
  }

  /**
   * Ambil data billing lengkap (untuk halaman sukses atau PDF)
   */
  public function getByCalonSiswa(int $calon_id): ?array
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE calon_siswa_id = :id LIMIT 1");
    $stmt->execute([':id' => $calon_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
  }
}
