<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Kategori
{
  private PDO $db;

  public function __construct()
  {
    $this->db = Database::connect();
  }

  // Ambil semua kategori
  public function getAll(): array
  {
    $stmt = $this->db->query("SELECT * FROM kategori_artikel ORDER BY nama ASC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // (Opsional) Ambil kategori berdasarkan ID
  public function find(int $id): ?array
  {
    $stmt = $this->db->prepare("SELECT * FROM kategori_artikel WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
  }
}
