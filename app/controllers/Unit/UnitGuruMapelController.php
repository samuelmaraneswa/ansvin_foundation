<?php
namespace App\Controllers\Unit;

use App\Core\AdminController;
use App\Core\Config;
use App\Core\Database;
use App\Core\Auth;
use App\Helpers\Validator;

class UnitGuruMapelController extends AdminController{ 
  private $db;
  protected $guru_mapel;

  public function __construct()
  {
    parent::__construct();

    Auth::allowRoles(['admin_unit']);

    $this->db = Database::connect();
    $this->guru_mapel = $this->model('GuruMapel');
  }

  public function index(){
    $this->view('layouts/admin_main', [
      'title' => 'Guru MaPel',
      'page' => 'guru_mapel',
      'content' => 'unit/guru_mapel/index',
      'base_url' => Config::get('base_url'),
    ]);
  }

  public function fetchAll(){
    $unit_id = $this->user['unit_id'];

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $offset = ($page - 1) * $limit;
    if ($page < 1) $page = 1;
    if ($limit < 1) $limit = 5;

    $data = $this->guru_mapel->fetchAll($unit_id, $limit, $offset);
    $total = $this->guru_mapel->countAll($unit_id);
    $total_pages = ceil($total / $limit);

    echo json_encode([
      'status' => 'success',
      'data' => $data,
      'pagination' => [
        'page' => $page,
        'total_pages' => $total_pages,
        'total' => $total,
        'limit' => $limit,
      ]
    ]);
  }

  public function dropdown($unit_id){
    $unit_id = $this->user['unit_id'];
    $guru = $this->guru_mapel->getGuru($unit_id);
    $mapel = $this->guru_mapel->getMapel($unit_id);
    $tahun = $this->guru_mapel->getTahunAjaranActive();
    
    echo json_encode([
      'status' => 'success',
      'guru' => $guru,
      'mapel' => $mapel,
      'tahun' => $tahun,
    ]);
  }

  public function store(){

    $unit_id = $this->user['unit_id'];

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
      throw new \Exception('CSRF token tidak valid!');
    }

    $validator = new Validator($_POST);
    $validator->required(['csrf_token','guru','mapel','tahun_ajaran']);

    if($validator->hasErrors()){
      echo json_encode([
        'status' => 'error',
        'errors' => $validator->getErrors()
      ]);
      return;
    }

    $guru_id = $validator->sanitize('guru');
    $mapel_id = $validator->sanitize('mapel');
    $tahun_ajaran_id = $validator->sanitize('tahun_ajaran');
    
    try{
      $this->db->beginTransaction();

      $insert = $this->guru_mapel->insert($unit_id, $guru_id, $mapel_id, $tahun_ajaran_id);

      $this->db->commit();

      echo json_encode([
        'status' => 'success',
        'message' => 'Data berhasil ditambahkan.'
      ]);

    }catch(\Exception $e){
      $this->db->rollBack();

      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menambah data.'
      ]);
    }
  }

  public function get($slug, $id){
    $data = $this->guru_mapel->findById($id);
    
    if(!$data){
      echo json_encode([
        'status' => 'error',
        'message' => 'Data tidak ditemukan.'
      ]);
    }

    echo json_encode([
      'status' => 'success',
      'data' => $data
    ]);
  }

  public function update($slug, $id){

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
      throw new \Exception('CSRF token tidak valid!');
    }

    $validator = new Validator($_POST);
    $validator->required(['csrf_token', 'guru', 'mapel', 'tahun_ajaran']);

    if ($validator->hasErrors()) {
      echo json_encode([
        'status' => 'error',
        'errors' => $validator->getErrors()
      ]);
      return;
    }

    $guru_id = $validator->sanitize('guru');
    $mapel_id = $validator->sanitize('mapel');
    $tahun_ajaran_id = $validator->sanitize('tahun_ajaran');

    try {
      $this->db->beginTransaction();

      $update = $this->guru_mapel->update($id, $guru_id, $mapel_id, $tahun_ajaran_id);
      $data = $this->guru_mapel->findById($id);

      $this->db->commit();

      echo json_encode([
        'status' => 'success',
        'message' => 'Data berhasil diubah.',
        'data' => $data,
      ]);
    } catch (\Exception $e) {
      $this->db->rollBack();

      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal mengubah data.'
      ]);
    }
  }

  public function delete($slug, $id){
    try{
      $this->db->beginTransaction();

      $deleted = $this->guru_mapel->delete($id);

      $this->db->commit();

      echo json_encode([
        'status' => 'success',
        'message' => 'Data berhasil di hapus',
        'id' => $id,
      ]);

    }catch(\Exception $e){
      $this->db->rollBack();

      echo json_encode([
        'status' => 'error',
        'message' => 'Gagal menghapus data',
      ]);
    }
  }

  public function search($slug){
    $keyword = $_GET['keyword'] ?? '';
    $unit_id = $this->user['unit_id'];

    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 5;
    if ($page < 1) $page = 1;
    if ($limit < 1) $limit = 5;
    
    $offset = ($page - 1) * $limit;
    $total = $this->guru_mapel->countSearch($unit_id, $keyword);
    $total_pages = $total > 0 ? ceil($total / $limit) : 1;

    $results = $this->guru_mapel->search($unit_id, $keyword, $limit, $offset);

    echo json_encode([
      'status' => 'success',
      'data' => $results,
      'pagination' => [
        'page' => $page,
        'total' => $total,
        'total_pages' => $total_pages,
        'limit' => $limit,
      ]
    ]);
  }
}