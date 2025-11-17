<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class UsersPegawaiModel{
  protected $table = 'users_pegawai';
  private PDO $db;

  public function __construct()
  {
    $this->db = Database::connect();
  }

  /**
   * ambil data user pegawai berdasarkan username
   */
  public function findByNIP(string $nip): ?array
  {
    $db = Database::connect();
    $stmt = $db->prepare("select up.*, p.nama as nama_pegawai, p.unit_id, p.nip , p.foto from {$this->table} up join pegawai p on up.pegawai_id = p.id where p.nip = :nip limit 1");
    $stmt->bindValue(':nip', $nip);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
  }

  public function insert(array $data)
  {
    $columns = array_keys($data);
    $placeholders = array_map(fn($col) => ':' . $col, $columns);

    $sql = "INSERT INTO {$this->table} (" . implode(',', $columns) . ")
            VALUES (" . implode(',', $placeholders) . ")";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($data);

    return $this->db->lastInsertId();
  }

  public function findById($id){
    $sql = "SELECT p.*, u.role 
        FROM pegawai p 
        LEFT JOIN users_pegawai u ON u.pegawai_id = p.id 
        WHERE p.id = :pegawai_id";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['pegawai_id' => $id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
     return $data ?: null;
  }

  public function updateByPegawaiId(int $pegawai_id, array $data)
  {
    $fields = [];
    foreach($data as $k => $v){
      $fields[] = "$k = :$k";
    }
    $sql = "UPDATE users_pegawai SET " . implode(',', $fields) . " WHERE pegawai_id = :pegawai_id";
    $stmt = $this->db->prepare($sql);
    $data['pegawai_id'] = $pegawai_id;
    return $stmt->execute($data);
  }

  public function deleteByPegawaiId($pegawai_id)
  {
    $stmt = $this->db->prepare("DELETE FROM users_pegawai WHERE pegawai_id = ?");
    return $stmt->execute([$pegawai_id]);
  }

}