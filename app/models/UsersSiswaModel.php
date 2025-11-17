<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class UsersSiswaModel{
  private string $table = 'users_siswa';

  /**
   * cari siswa berdasarkan nis
   */
  public function findByNIS(string $nis): ?array
  {
    $db = Database::connect();

    $stmt = $db->prepare("select us.*, s.nama as nama_siswa, s.kelas_id, s.unit_id, s.foto, k.nama as nama_kelas from {$this->table} us join siswa s on us.siswa_id = s.id left join kelas k on s.kelas_id = k.id where s.nis = :nis limit 1");
    $stmt->bindValue(':nis', $nis);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
  }

  /*
   * Verifikasi password siswa
   */
  public function verifyPassword(array $user, string $password): bool
  {
    return password_verify($password, $user['password']);
  }

  /**
   * Ambil data siswa berdasarkan ID users_siswa
   */
  public function findById(int $id): ?array
  {
    $db = Database::connect();

    $stmt = $db->prepare("
      SELECT us.*, s.nama AS nama_siswa, s.nis, s.kelas_id, s.unit_id, s.foto, k.nama AS nama_kelas
      FROM {$this->table} us
      JOIN siswa s ON us.siswa_id = s.id
      LEFT JOIN kelas k ON s.kelas_id = k.id
      WHERE us.id = :id
      LIMIT 1
    ");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ?: null;
  }

  /**
   * Tambahkan user siswa baru (opsional)
   */
  public function insert(array $data): bool
  {
    $db = Database::connect();

    $stmt = $db->prepare("
      INSERT INTO {$this->table} (siswa_id, password, role)
      VALUES (:siswa_id, :password, :role)
    ");
    $stmt->bindValue(':siswa_id', $data['siswa_id']);
    $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
    $stmt->bindValue(':role', $data['role'] ?? 'siswa');
    return $stmt->execute();
  }
}