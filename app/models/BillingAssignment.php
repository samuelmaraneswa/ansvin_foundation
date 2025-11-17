<?php
namespace App\Models;

use App\Core\Database;

class BillingAssignment extends Database
{
  protected $db;
  protected $table = 'billing_assignment';

  public function __construct()
  {
    $this->db = Database::connect();
  }

  /**
   * Simpan tagihan baru untuk calon siswa
   * @param array $data
   * @return int ID billing yang baru disimpan
   */
  public function insert(array $data): int
  {
    $cols = array_keys($data);
    $placeholders = array_map(fn($col) => ':' . $col, $cols);
    $sql = "INSERT INTO {$this->table} (" . implode(',', $cols) . ")
            VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $this->db->prepare($sql);
    $stmt->execute($data);
    return $this->db->lastInsertId();
  }

  /**
   * Ambil semua tagihan calon siswa berdasarkan ID calon_siswa
   */
  public function getByCalonSiswa(int $calon_siswa_id): array
  {
    $stmt = $this->db->prepare("
      SELECT * FROM {$this->table}
      WHERE calon_siswa_id = :id
      ORDER BY created_at DESC
    ");
    $stmt->execute(['id' => $calon_siswa_id]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Ambil total tagihan calon siswa (sisa atau total penuh)
   */
  public function getTotalByCalonSiswa(int $calon_siswa_id): float
  {
    $stmt = $this->db->prepare("
      SELECT SUM(total_tagihan) AS total FROM {$this->table}
      WHERE calon_siswa_id = :id
    ");
    $stmt->execute(['id' => $calon_siswa_id]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    return (float)($row['total'] ?? 0);
  }

  /**
   * Update status pembayaran (BELUM / SUDAH)
   */
  public function updateStatus(int $id, string $status): bool
   {
    $stmt = $this->db->prepare("
      UPDATE {$this->table}
      SET status_bayar = :status,
          tanggal_bayar = CASE WHEN :status = 'SUDAH' THEN NOW() ELSE NULL END
      WHERE id = :id
    ");
    return $stmt->execute(['status' => $status, 'id' => $id]);
  }

  /**
   * Ambil billing calon siswa (dengan detail total dan status)
   */
  public function getBillingDetail(int $calon_siswa_id): ?array
  {
    $stmt = $this->db->prepare("
      SELECT 
        b.id, b.total_tagihan, b.status_bayar, b.created_at, b.tanggal_bayar
      FROM {$this->table} AS b
      WHERE b.calon_siswa_id = :id
      LIMIT 1
    ");
    $stmt->execute(['id' => $calon_siswa_id]);
    return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
  }
}
