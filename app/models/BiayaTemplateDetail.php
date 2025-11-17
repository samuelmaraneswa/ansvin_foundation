<?php
namespace App\Models;

use App\Core\Database;

class BiayaTemplateDetail extends Database
{
  protected $db;
  protected $table = 'biaya_template_detail';

  public function __construct()
  {
    $this->db = Database::connect();
  }

  /**
   * Ambil semua item biaya berdasarkan template_id
   */
  public function getByTemplate(int $template_id): array 
  {
    $stmt = $this->db->prepare("
      SELECT * FROM {$this->table}
      WHERE template_id = :template_id
      ORDER BY urutan ASC
    ");
    $stmt->execute(['template_id' => $template_id]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Tambahkan item biaya baru (opsional)
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
