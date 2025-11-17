<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class UnitSekolah
{
  protected $table = 'unit_sekolah';
  private PDO $db;

  public function __construct()
  {
    $this->db = Database::connect();
  }

  public function getAll(): array
  {
    $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY nama ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function find($id)
  { 
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
  }

}
