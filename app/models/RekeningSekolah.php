<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;
use PDO;

class RekeningSekolah
{
  protected $db;
  protected $table = 'rekening_sekolah';

  public function __construct()
  {
    $this->db = Database::connect();
  }

  /**
   * Ambil semua rekening sekolah (opsional: per unit)
   */
  public function getAll(?int $unit_id = null): array
  {
    if ($unit_id) {
      $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE unit_id = :unit_id ORDER BY id DESC");
      $stmt->execute(['unit_id' => $unit_id]);
    } else {
      $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY unit_id ASC");
    }

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * Ambil rekening aktif berdasarkan unit sekolah
   */
  public function getActiveByUnit(int $unit_id): ?array
  {
    $stmt = $this->db->prepare("
      SELECT * FROM {$this->table}
      WHERE unit_id = :unit_id AND aktif = 1
      ORDER BY updated_at DESC
      LIMIT 1
    ");
    $stmt->execute(['unit_id' => $unit_id]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
  }

  /**
   * Tambahkan rekening baru
   */
  public function insert(array $data): int
  {
    $stmt = $this->db->prepare("
      INSERT INTO {$this->table}
      (unit_id, nama_bank, no_rekening, nama_pemilik, aktif)
      VALUES (:unit_id, :nama_bank, :no_rekening, :nama_pemilik, :aktif)
    ");
    $stmt->execute([
      ':unit_id' => $data['unit_id'],
      ':nama_bank' => $data['nama_bank'],
      ':no_rekening' => $data['no_rekening'],
      ':nama_pemilik' => $data['nama_pemilik'],
      ':aktif' => $data['aktif'] ?? 1,
    ]);

    return (int) $this->db->lastInsertId();
  }

  /**
   * Nonaktifkan rekening lama pada unit tertentu
   */
  public function deactivateOld(int $unit_id): void
  {
    $stmt = $this->db->prepare("UPDATE {$this->table} SET aktif = 0 WHERE unit_id = :unit_id");
    $stmt->execute(['unit_id' => $unit_id]);
  }

  /**
   * Update data rekening
   */
  public function updateRekening(int $id, array $data): bool
  {
    $stmt = $this->db->prepare("
      UPDATE {$this->table}
      SET nama_bank = :nama_bank,
          no_rekening = :no_rekening,
          nama_pemilik = :nama_pemilik,
          aktif = :aktif,
          updated_at = NOW()
      WHERE id = :id
    ");
    return $stmt->execute([
      ':id' => $id,
      ':nama_bank' => $data['nama_bank'],
      ':no_rekening' => $data['no_rekening'],
      ':nama_pemilik' => $data['nama_pemilik'],
      ':aktif' => $data['aktif'] ?? 1,
    ]);
  }
}
