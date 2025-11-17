<?php
namespace App\Models;

use App\Core\Database;

class TahunAjaran extends Database
{
  protected $db;
  protected $table = 'tahun_ajaran';

  public function __construct()
  {
    $this->db = Database::connect(); 
  }

  /**
   * Ambil tahun ajaran yang sedang aktif
   * @return array|null
   */
  public function getActive(): ?array
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE aktif = 1 LIMIT 1");
    $stmt->execute();
    return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
  }

  /**
   * Ambil semua tahun ajaran (untuk halaman admin)
   * Urutkan terbaru di atas
   */
  public function getAll(): array
  {
    $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY tahun_mulai DESC");
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  /**
   * Nonaktifkan semua tahun ajaran (saat ingin mengaktifkan yang baru)
   */
  public function deactivateAll(): void
  {
    $this->db->query("UPDATE {$this->table} SET aktif = 0");
  }

  /**
   * Aktifkan tahun ajaran tertentu berdasarkan ID
   * @param int $id
   */
  public function setActive(int $id): void
  {
    $this->deactivateAll();
    $stmt = $this->db->prepare("UPDATE {$this->table} SET aktif = 1 WHERE id = :id");
    $stmt->execute(['id' => $id]);
  }

  /**
   * Tambahkan tahun ajaran baru
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

  public function getById(int $id): ?array
  {
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $result ?: null;
  }

}
