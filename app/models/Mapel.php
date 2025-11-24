<?php
namespace App\Models;

use App\Core\Database;

class Mapel{
  private $db;

  public function __construct()
  {
    $this->db = Database::connect();
  }

  public function fetchAll()
  {
    $stmt = $this->db->query("select * from mata_pelajaran");
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function insert(array $data)
  {
    $stmt = $this->db->prepare("insert into mata_pelajaran (nama_mapel, kode_mapel, tingkat_min, tingkat_max, unit_id, status_aktif, created_at, updated_at) values (:nama_mapel,:kode_mapel,:tingkat_min,:tingkat_max, :unit_id, :status_aktif, NOW(), NOW())");
    $stmt->execute([
      ':unit_id' => $data['unit_id'],
      ':nama_mapel' => $data['nama_mapel'],
      ':kode_mapel' => $data['kode_mapel'],
      ':tingkat_min' => $data['tingkat_min'],
      ':tingkat_max' => $data['tingkat_max'],
      ':status_aktif' => 1,
    ]);

    $lastId = $this->db->lastInsertId();
    return $lastId;
  }

  function getById($id, $unit_id){
    $stmt = $this->db->prepare("select * from mata_pelajaran where id = :id && unit_id = :unit_id");
    $stmt->execute([':id' => $id, ':unit_id' => $unit_id]);

    return $stmt->fetch(\PDO::FETCH_ASSOC);
  }

  public function update($id, $data){
    $stmt = $this->db->prepare("update mata_pelajaran set nama_mapel = :nama_mapel, kode_mapel = :kode_mapel, tingkat_min = :tingkat_min, tingkat_max = :tingkat_max where id = :id && unit_id = :unit_id");
    
    return $stmt->execute([
      'id' => $id,
      'unit_id' => $data['unit_id'],
      ':nama_mapel' => $data['nama_mapel'],
      ':kode_mapel' => $data['kode_mapel'],
      ':tingkat_min' => $data['tingkat_min'],
      ':tingkat_max' => $data['tingkat_max'],
    ]);
  }

  public function delete($id, $unit_id){
    $stmt = $this->db->prepare("delete from mata_pelajaran where id = :id AND unit_id = :unit_id");
    $stmt->execute([
      ':id' => $id,
      ':unit_id' => $unit_id
    ]);

    return $stmt->rowCount();
  }

  public function search($keyword, $unit_id){
    $stmt = $this->db->prepare("select * from mata_pelajaran where unit_id = :unit_id AND (nama_mapel LIKE :keyword1 OR kode_mapel LIKE :keyword2) order by nama_mapel asc");

    $stmt->execute([':unit_id' => $unit_id, ':keyword1' => "%{$keyword}%", ':keyword2' => "%{$keyword}%"]);

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function countByUnit($unit_id){
    $stmt = $this->db->prepare("select count(*) as total from mata_pelajaran where unit_id = :unit_id");
    $stmt->execute([':unit_id' => $unit_id]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    return (int)$row["total"];
  }

  public function getByUnitPaginated($unit_id, $limit, $offset){
    $stmt = $this->db->prepare("select * from mata_pelajaran where unit_id = :unit_id order by id asc limit :limit offset :offset");
    $stmt->bindValue(':unit_id', $unit_id, \PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function countSearch($keyword, $unit_id){
    $stmt = $this->db->prepare("select count(*) as total from mata_pelajaran where unit_id = :unit_id and (nama_mapel like :kw1 or kode_mapel like :kw2)");
    $stmt->execute([
      ':unit_id' => $unit_id,
      ':kw1' => "%{$keyword}%",
      ':kw2' => "%{$keyword}%",
    ]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    return (int)$row['total'];
  }

  public function searchPaginated($keyword, $unit_id, $limit, $offset){
    $stmt = $this->db->prepare("select * from mata_pelajaran where unit_id = :unit_id and (nama_mapel like :kw1 or kode_mapel like :kw2) order by id asc limit :limit offset :offset");

    $stmt->bindValue(':unit_id', $unit_id, \PDO::PARAM_INT);
    $stmt->bindValue(':kw1', "%{$keyword}%", \PDO::PARAM_STR);
    $stmt->bindValue(':kw2', "%{$keyword}%", \PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }
}