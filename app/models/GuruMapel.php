<?php
namespace App\Models;

use App\Core\Database;

class GuruMapel{
  private $db;

  public function __construct()
  {
    $this->db = Database::connect();
  }

  public function fetchAll($unit_id, $limit, $offset){
    $stmt = $this->db->prepare("
      select gm.id,
            p.nama as guru,
            m.nama_mapel as mapel,
            t.nama_tahun as tahun
      from guru_mapel gm 
      join pegawai p on gm.pegawai_id = p.id
      join mata_pelajaran m on gm.mapel_id = m.id
      join tahun_ajaran t on gm.tahun_ajaran_id = t.id
      where gm.unit_id = :unit_id 
      order by gm.id asc
      limit :limit offset :offset
    ");
    
    $stmt->bindValue(':unit_id', $unit_id, \PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function countAll($unit_id){
    $stmt = $this->db->prepare("
      select count(*) as total
      from guru_mapel
      where unit_id = :unit_id
    ");
    $stmt->execute([':unit_id' => $unit_id]);
    return $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
  }

  public function getGuru($unit_id){
    $stmt = $this->db->prepare("
      select p.id, p.nama 
      from pegawai p
      join jabatan j on p.jabatan_id = j.id
      where p.unit_id = :unit_id and j.nama = 'guru'
      order by p.id asc
    ");
    $stmt->execute(['unit_id' => $unit_id]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getMapel($unit_id){
    $stmt = $this->db->prepare("
      select id, nama_mapel from mata_pelajaran where unit_id = :unit_id order by id asc
    ");
    $stmt->execute([':unit_id' => $unit_id]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }

  public function getTahunAjaranActive() {
    $stmt = $this->db->prepare("select id, nama_tahun from tahun_ajaran where aktif = 1 limit 1");
    $stmt->execute();
    return $stmt->fetch(\PDO::FETCH_ASSOC);
  }

  public function insert($unit_id, $guru_id, $mapel_id, $tahun_ajaran_id){
    $stmt = $this->db->prepare("insert into guru_mapel (unit_id, pegawai_id, mapel_id, tahun_ajaran_id) values (:unit_id, :guru_id, :mapel_id, :tahun_ajaran_id)");

    return $stmt->execute([
      ':unit_id' => $unit_id,
      ':guru_id' => $guru_id,
      ':mapel_id' => $mapel_id,
      ':tahun_ajaran_id' => $tahun_ajaran_id,
    ]);
  }

  public function findById($id){
    $stmt = $this->db->prepare("
        SELECT 
        gm.id,
        gm.pegawai_id,
        p.nama AS guru,
        gm.mapel_id,
        m.nama_mapel AS mapel,
        gm.tahun_ajaran_id,
        t.nama_tahun AS tahun
      FROM guru_mapel gm
      JOIN pegawai p ON p.id = gm.pegawai_id
      JOIN mata_pelajaran m ON m.id = gm.mapel_id
      JOIN tahun_ajaran t ON t.id = gm.tahun_ajaran_id
      WHERE gm.id = :id
      LIMIT 1
    ");

    $stmt->execute([':id' => $id]);
    return $stmt->fetch();
  }

  public function update($id, $guru_id, $mapel_id, $tahun_ajaran_id){
    $stmt = $this->db->prepare("update guru_mapel set pegawai_id = :guru, mapel_id = :mapel, tahun_ajaran_id = :tahun where id = :id");

    $stmt->execute([
      ':guru' => $guru_id,
      ':mapel' => $mapel_id,
      ':tahun' => $tahun_ajaran_id,
      ':id' => $id,
    ]);

    return $stmt->rowCount();
  }

  public function delete($id){
    $stmt = $this->db->prepare("delete from guru_mapel where id = :id");
    return $stmt->execute([':id' => $id]);
  }

  public function search($unit_id, $keyword, $limit, $offset){
    $stmt = $this->db->prepare("
      select
        gm.id,
        p.nama as guru,
        m.nama_mapel as mapel,
        t.nama_tahun as tahun
      from guru_mapel gm
      join pegawai p on p.id = gm.pegawai_id
      join mata_pelajaran m on m.id = gm.mapel_id
      join tahun_ajaran t on t.id = gm.tahun_ajaran_id
      where gm.unit_id = :unit_id 
        and (p.nama like :kw1 or m.nama_mapel like :kw2)
      order by gm.id asc
      limit :limit offset :offset 
    ");

    $stmt->bindValue(':unit_id', $unit_id, \PDO::PARAM_INT);
    $stmt->bindValue(':kw1', "%$keyword%", \PDO::PARAM_STR);
    $stmt->bindValue(':kw2', "%$keyword%", \PDO::PARAM_STR);
    $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function countSearch($unit_id, $keyword){
    $stmt = $this->db->prepare("
      select count(*) as total
      from guru_mapel gm
      join pegawai p on p.id = gm.pegawai_id
      join mata_pelajaran m on m.id = gm.mapel_id
      where gm.unit_id = :unit_id
        and (p.nama like :kw1 or m.nama_mapel like :kw2)
    ");

    $stmt->execute([
      ':unit_id' => $unit_id,
      ':kw1' => "%$keyword%",
      ':kw2' => "%$keyword%",
    ]);
    return $stmt->fetch()['total'];
  }

  public function getByUnit($unit_id){
    $stmt = $this->db->prepare("
      select 
        gm.id, 
        p.nama as guru, 
        m.nama_mapel as mapel 
        from guru_mapel gm 
        join pegawai p on p.id = gm.pegawai_id
        join mata_pelajaran m on m.id = gm.mapel_id
        where gm.unit_id = :unit_id
        order by p.nama, m.nama_mapel
    ");

    $stmt->execute([':unit_id' => $unit_id]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
  }
}