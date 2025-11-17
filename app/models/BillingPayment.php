<?php
namespace App\Models;

use App\Core\Database;

class BillingPayment extends Database
{
  protected $db;
  protected $table = 'billing_payment';

  public function __construct()
  {
    $this->db = Database::connect();
  }

  /**
   * Simpan pembayaran baru
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
   * Ambil semua pembayaran berdasarkan ID billing_assignment
   */
  public function getByBillingId(int $billing_assignment_id): array
  {
    $stmt = $this->db->prepare("
      SELECT * FROM {$this->table}
      WHERE billing_assignment_id = :id
      ORDER BY tanggal_bayar DESC
    ");
    $stmt->execute(['id' => $billing_assignment_id]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Ambil total pembayaran yang sudah dilakukan untuk billing tertentu
   */
  public function getTotalPaid(int $billing_assignment_id): float
  {
    $stmt = $this->db->prepare("
      SELECT SUM(jumlah_bayar) AS total FROM {$this->table}
      WHERE billing_assignment_id = :id
    ");
    $stmt->execute(['id' => $billing_assignment_id]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    return (float)($row['total'] ?? 0);
  }
}
