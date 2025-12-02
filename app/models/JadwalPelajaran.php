<?php
namespace App\Models;

use App\Core\Database;

class JadwalPelajaran{
  private $db;

  public function __construct()
  {
    $this->db = Database::connect();
  }

  public function fetchAllPaginated($unit_id, $limit, $offset){
    $stmt = $this->db->prepare("
      select
        jp.id,
        jp.hari,
        jp.jam_mulai,
        jp.jam_selesai,
        k.nama as kelas,
        m.nama_mapel as mapel,
        p.nama as guru
      from jadwal_pelajaran jp
      join guru_mapel gm on gm.id = jp.guru_mapel_id
      join kelas k on k.id = jp.kelas_id
      join mata_pelajaran m on m.id = gm.mapel_id
      join pegawai p on p.id = gm.pegawai_id
      where gm.unit_id = :unit_id
      order by jp.hari, k.nama, jp.jam_mulai
      limit :limit offset :offset
    ");

    $stmt->bindValue(':unit_id', $unit_id, \PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function countByUnit($unit_id){
    $stmt = $this->db->prepare("select count(*) as total from jadwal_pelajaran jp join guru_mapel gm on gm.id = jp.guru_mapel_id where gm.unit_id = :unit_id");

    $stmt->execute([':unit_id' => $unit_id]);
    return $stmt->fetch(\PDO::FETCH_ASSOC)["total"];
  }

  public function store($data){
    $stmt = $this->db->prepare("insert into jadwal_pelajaran (hari, kelas_id, guru_mapel_id, jam_mulai, jam_selesai) values (:hari, :kelas_id, :guru_mapel_id, :jam_mulai, :jam_selesai)");

    return $stmt->execute([
      ':hari' => $data['hari'],
      ':kelas_id' => $data['kelas_id'],
      ':guru_mapel_id' => $data['guru_mapel_id'],
      ':jam_mulai' => $data['jam_mulai'],
      ':jam_selesai' => $data['jam_selesai'],
    ]);
  }

  public function getById($id){
    $stmt = $this->db->prepare("
      select
        jp.id,
        jp.hari,
        jp.kelas_id,
        jp.guru_mapel_id,
        jp.jam_mulai,
        jp.jam_selesai
      from jadwal_pelajaran jp
      where jp.id = :id
      limit 1
    ");

    $stmt->execute([':id' => $id]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
  }

  public function update($data){
    $stmt = $this->db->prepare("update jadwal_pelajaran set
      hari = :hari, kelas_id = :kelas_id, guru_mapel_id = :guru_mapel_id, jam_mulai = :jam_mulai, jam_selesai = :jam_selesai where id = :id
    ");

    return $stmt->execute([
      ':id' => $data['id'],
      ':hari' => $data['hari'],
      ':kelas_id' => $data['kelas_id'],
      ':guru_mapel_id' => $data['guru_mapel_id'],
      ':jam_mulai' => $data['jam_mulai'],
      ':jam_selesai' => $data['jam_selesai'],
    ]);
  }

  public function delete($id){
    $stmt = $this->db->prepare("delete from jadwal_pelajaran where id = :id");

    return $stmt->execute([':id' => $id]);
  }

  public function getRowForTable($id)
  {
    $stmt = $this->db->prepare("
    SELECT
      jp.id,
      jp.hari,
      k.nama AS kelas,
      jp.jam_mulai,
      jp.jam_selesai,
      m.nama_mapel AS mapel,
      p.nama AS guru
    FROM jadwal_pelajaran jp
    JOIN guru_mapel gm ON gm.id = jp.guru_mapel_id
    JOIN pegawai p ON p.id = gm.pegawai_id
    JOIN mata_pelajaran m ON m.id = gm.mapel_id
    JOIN kelas k ON k.id = jp.kelas_id
    WHERE jp.id = :id
  ");
    $stmt->execute([":id" => $id]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
  }

  public function searchPaginated($unit_id, $keyword, $limit, $offset)
  {
    if (strlen(trim($keyword)) === 0) {
      return $this->fetchAllPaginated($unit_id, $limit, $offset);
    }

    $words = preg_split('/\s+/', trim($keyword));
    $words = array_values(array_filter($words, fn($w) => strlen($w) > 0));

    $sql = "SELECT
      jp.id, jp.hari, jp.jam_mulai, jp.jam_selesai,
      k.nama AS kelas,
      m.nama_mapel AS mapel,
      p.nama AS guru
    FROM jadwal_pelajaran jp
    JOIN guru_mapel gm ON gm.id = jp.guru_mapel_id
    JOIN pegawai p ON p.id = gm.pegawai_id
    JOIN mata_pelajaran m ON m.id = gm.mapel_id
    JOIN kelas k ON k.id = jp.kelas_id
    WHERE gm.unit_id = :unit_id ";

    foreach ($words as $index => $word) {
      $sql .= " AND (
      p.nama LIKE :p{$index}
      OR m.nama_mapel LIKE :m{$index}
      OR k.nama LIKE :k{$index}
      OR jp.hari LIKE :h{$index}
    ) ";
    }

    $sql .= " ORDER BY jp.hari, k.nama, jp.jam_mulai LIMIT $limit OFFSET $offset";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(":unit_id", $unit_id);

    foreach ($words as $index => $word) {
      $like = "%{$word}%";
      $stmt->bindValue(":p{$index}", $like);
      $stmt->bindValue(":m{$index}", $like);
      $stmt->bindValue(":k{$index}", $like);
      $stmt->bindValue(":h{$index}", $like);
    }

    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function countSearch($unit_id, $keyword)
  {
    $words = preg_split('/\s+/', trim($keyword));
    $words = array_values(array_filter($words, fn($w) => strlen($w) > 0));

    $sql = "SELECT COUNT(*) AS total
    FROM jadwal_pelajaran jp
    JOIN guru_mapel gm ON gm.id = jp.guru_mapel_id
    JOIN pegawai p ON p.id = gm.pegawai_id
    JOIN mata_pelajaran m ON m.id = gm.mapel_id
    JOIN kelas k ON k.id = jp.kelas_id
    WHERE gm.unit_id = :unit_id ";

    foreach ($words as $index => $word) {
      $sql .= " AND (
      p.nama LIKE :p{$index}
      OR m.nama_mapel LIKE :m{$index}
      OR k.nama LIKE :k{$index}
      OR jp.hari LIKE :h{$index}
    ) ";
    }

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(":unit_id", $unit_id);

    foreach ($words as $index => $word) {
      $like = "%{$word}%";
      $stmt->bindValue(":p{$index}", $like);
      $stmt->bindValue(":m{$index}", $like);
      $stmt->bindValue(":k{$index}", $like);
      $stmt->bindValue(":h{$index}", $like);
    }

    $stmt->execute();
    return $stmt->fetch(\PDO::FETCH_ASSOC)["total"];
  }
}