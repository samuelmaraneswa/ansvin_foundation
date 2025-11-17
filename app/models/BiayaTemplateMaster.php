<?php
namespace App\Models;

use App\Core\Database;

class BiayaTemplateMaster extends Database 
{
  protected $db;
  protected $table = 'biaya_template_master';

  public function __construct()
  { 
    $this->db = Database::connect();
  }

  /**
   * Ambil template biaya aktif untuk unit & tahun ajaran tertentu
   */
  public function getActiveByUnit(int $unit_id, int $tahun_ajaran_id): ?array
  {
    $stmt = $this->db->prepare("
      SELECT * FROM {$this->table}
      WHERE unit_id = :unit_id
        AND tahun_ajaran_id = :tahun_ajaran_id
        AND status = 1
      LIMIT 1
    ");
    $stmt->execute([
      'unit_id' => $unit_id,
      'tahun_ajaran_id' => $tahun_ajaran_id
    ]);
    return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
  }

  /**
   * Buat template baru (opsional untuk admin keuangan)
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
}
