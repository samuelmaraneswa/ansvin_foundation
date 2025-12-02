<?php

namespace App\Models;

use App\Core\Database;

class Kelas
{
  private $db;

  public function __construct()
  {
    $this->db = Database::connect();
  }

  public function getByUnit($unit_id) {
    $stmt = $this->db->prepare("select id, nama from kelas where unit_id = :unit_id");
    $stmt->execute([':unit_id' => $unit_id]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }
}
