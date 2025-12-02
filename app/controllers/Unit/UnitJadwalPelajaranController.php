<?php
namespace App\Controllers\Unit;

use App\Core\AdminController;
use App\Core\Auth;
use App\Core\Config;
use App\Core\Database;
use App\Helpers\Validator;
use Exception;

class UnitJadwalPelajaranController extends AdminController{
  private $db;
  protected $jadpel;
  protected $guru_mapel;
  protected $kelas;

  public function __construct()
  {
    parent::__construct();

    Auth::allowRoles(['admin_unit']);

    $this->db = Database::connect();
    $this->jadpel = $this->model("JadwalPelajaran");
    $this->guru_mapel = $this->model("GuruMapel");
    $this->kelas = $this->model("Kelas");
  }

  public function index(){
    $this->view('layouts/admin_main', [
      'title' => 'Jadwal Pelajaran',
      'page' => 'jadpel',
      'base_url' => Config::get('base_url'),
      'content' => 'unit/jadpel/index',
    ]);
  }

  public function getGuruMapelOptions(){
    $unit_id = $this->user['unit_id'];

    $rows = $this->guru_mapel->getByUnit($unit_id);
    $options = [];
    foreach($rows as $row){
      $options[] = [
        'id' => $row['id'],
        'text' => $row['guru'] . ' - ' . $row['mapel']
      ];
    }
    
    echo json_encode([
      'status' => 'success',
      'data' => $options,
    ]);
  }

  public function getKelasOptions(){
    $unit_id = $this->user['unit_id'];

    $rows = $this->kelas->getByUnit($unit_id);
    
    $options = [];
    foreach($rows as $row){
      $options[] = [
        'id' => $row['id'],
        'text' => $row['nama'],
      ];
    }
    
    echo json_encode([
      'status' => 'success',
      'data' => $options,
    ]);
  }

  public function fetchAll(){
    $unit_id = $this->user['unit_id'];

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
    $offset = ($page - 1) * $limit;

    $rows = $this->jadpel->fetchAllPaginated($unit_id, $limit, $offset);
    $total = $this->jadpel->countByUnit($unit_id);
    $total_pages = ceil($total / $limit);

    echo json_encode([
      'status' => 'success',
      'data' => $rows,
      'pagination' => [
        'page' => $page,
        'total' => $total,
        'total_pages' => $total_pages,
      ]
    ]);
  }

  public function store(){

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
      throw new \Exception('CSRF token tidak valid!');
    }

    $validator = new Validator($_POST);
    $validator->required(['csrf_token','hari','kelas_id','guru_mapel_id','jam_mulai','jam_selesai']);
    if($validator->hasErrors()){
      echo json_encode([
        'status' => 'error',
        'errors' => $validator->getErrors()
      ]);
      return;
    }

    $hari = $validator->sanitize('hari');
    $kelas_id = $validator->sanitize('kelas_id');
    $guru_mapel_id = $validator->sanitize('guru_mapel_id');
    $jam_mulai = $validator->sanitize('jam_mulai');
    $jam_selesai = $validator->sanitize('jam_selesai');

    try{
      $this->db->beginTransaction();

      $insert = $this->jadpel->store([
        'hari' => $hari,
        'kelas_id' => $kelas_id,
        'guru_mapel_id' => $guru_mapel_id,
        'jam_mulai' => $jam_mulai,
        'jam_selesai' => $jam_selesai,
      ]);

      $this->db->commit();

      if($insert){
        echo json_encode([
          'status' => 'success',
          'message' => 'Berhasil menambahkan data'
        ]);
      }

    }catch(Exception $e){
      $this->db->rollBack();
      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menambah data',
      ]);
    }
  }

  public function getDetail($slug, $id){
    $row = $this->jadpel->getById($id);

    if($row){
      echo json_encode([
        'status' => 'success',
        'data' => $row,
      ]);
    }else{
      echo json_encode([
        "status" => "error",
        "message" => "Data tidak ditemukan"
      ]);
    }
    exit;
  }

  public function update($slug, $id){
   
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
      throw new \Exception('CSRF token tidak valid!');
    }

    $validator = new Validator($_POST);
    $validator->required(['csrf_token', 'jadpel-id', 'hari', 'kelas_id', 'guru_mapel_id', 'jam_mulai', 'jam_selesai']);
    if ($validator->hasErrors()) {
      echo json_encode([
        'status' => 'error',
        'errors' => $validator->getErrors()
      ]);
      return;
    }

    $id = $validator->sanitize('jadpel-id');
    $hari = $validator->sanitize('hari');
    $kelas_id = $validator->sanitize('kelas_id');
    $guru_mapel_id = $validator->sanitize('guru_mapel_id');
    $jam_mulai = $validator->sanitize('jam_mulai');
    $jam_selesai = $validator->sanitize('jam_selesai');

    try {
      $this->db->beginTransaction();

      $update = $this->jadpel->update([
        'id' => $id,
        'hari' => $hari,
        'kelas_id' => $kelas_id,
        'guru_mapel_id' => $guru_mapel_id,
        'jam_mulai' => $jam_mulai,
        'jam_selesai' => $jam_selesai,
      ]);

      $this->db->commit();

      if ($update) {
        $updatedRow = $this->jadpel->getRowForTable($id);

        echo json_encode([
          'status' => 'success',
          'message' => 'Berhasil memperbaharui data',
          'data' => $updatedRow,
        ]);
      }
    } catch (Exception $e) {
      $this->db->rollBack();
      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal memperbaharui data',
      ]);
    }
  }

  public function delete($slug, $id){
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
      echo json_encode([
        "status" => "error",
        "message" => "CSRF token tidak valid."
      ]);
      exit;
    }

    if(!$id){
      echo json_encode([
        "status" => "error",
        "message" => "ID tidak ditemukan."
      ]);
      exit;
    }

    $deleted = $this->jadpel->delete($id);

    if($deleted){
      echo json_encode([
        "status" => "success",
        "message" => "Jadwal berhasil dihapus."
      ]);
    } else {
      echo json_encode([
        "status" => "error",
        "message" => "Gagal menghapus jadwal."
      ]);
    }
  }

  public function search($slug){
    $unit_id = $this->user['unit_id'];
    $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : "";

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

    $offset = ($page - 1) * $limit;

    $rows = $this->jadpel->searchPaginated($unit_id, $keyword, $limit, $offset);
    $total = $this->jadpel->countSearch($unit_id, $keyword);
    $total_pages = ceil($total / $limit);

    echo json_encode([
      "status" => "success",
      "data" => $rows,
      "pagination" => [
        "page" => $page,
        "total" => $total,
        "total_pages" => $total_pages,
      ]
    ]);
  }
}