<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Jabatan{
  private PDO $db;

  protected $table = 'jabatan';
  public function __construct()
  {
    $this->db = Database::connect();
  }

  public function getAll()
  {
    return $this->db->query("
      SELECT 
        id,
        nama
      FROM jabatan
      ORDER BY nama ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
  }

  public function find(int $id): ?array
  {
    $stmt = $this->db->prepare("SELECT * FROM jabatan WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $jabatan = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $jabatan ?: null;
  }

}