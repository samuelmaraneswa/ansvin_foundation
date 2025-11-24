<?php

namespace App\Controllers\Unit;

use App\Core\AdminController;
use App\Core\Auth;
use App\Core\Config;
use App\Core\Database;
use App\Helpers\Validator;
use Exception;
use FontLib\Table\Type\head;

class UnitMapelController extends AdminController{
  protected $mapel;
  private $db;

  public function __construct()
  {
    parent::__construct();

    // 🔒 Filter khusus halaman pegawai
    Auth::allowRoles(['admin_unit']);

    $this->mapel = $this->model("Mapel");
    $this->db = Database::connect();
  }

  public function index()
  {
    $this->view('layouts/admin_main', [
      'title' => 'Mata Pelajaran',
      'content' => 'unit/mapel/index',
      'base_url' => Config::get('base_url'),
      'page' => 'mapel'
    ]);
  }

  public function fetchAll(){
    $unit_id = $this->user['unit_id'];
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    if($page < 1) $page = 1;
    if($limit < 1) $limit = 5;

    $offset = ($page - 1) * $limit;

    $total = $this->mapel->countByUnit($unit_id);
    $data = $this->mapel->getByUnitPaginated($unit_id, $limit, $offset);
    $total_pages = $total > 0 ? ceil($total / $limit) : 1;

    echo json_encode([
      'status' => 'success',
      'data' => $data,
      'pagination' => [
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'total_pages' => $total_pages,
      ],
    ]);
  }

  public function store(){
    $unit_id = $this->user['unit_id'];

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
      throw new \Exception('CSRF token tidak valid!');
    }
    
    $validator = new Validator($_POST);
    $validator->required(['csrf_token','nama_mapel','kode_mapel','tingkat_min','tingkat_max'])
              ->minLength('nama_mapel', 3)
              ->minLength('kode_mapel', 3)
              ->maxLength('nama_mapel', 100);

    if($validator->hasErrors()){
      echo json_encode([
        'status' => 'error',
        'errors' => $validator->getErrors()
      ]);
      return;
    }

    $namaMapel = $validator->sanitize('nama_mapel');
    $kodeMapel = $validator->sanitize('kode_mapel');
    $tingkatMin = $validator->sanitize('tingkat_min');
    $tingkatMax = $validator->sanitize('tingkat_max');

    try{
      $this->db->beginTransaction();

      $mapelId = $this->mapel->insert([
        'unit_id' => $unit_id,
        'nama_mapel' => $namaMapel,
        'kode_mapel' => $kodeMapel,
        'tingkat_min' => $tingkatMin,
        'tingkat_max' => $tingkatMax,
      ]);

      $this->db->commit();

      echo json_encode([
        'status' => 'success',
        'message' => 'Mapel berhasil ditambahkan',
        'data' => $mapelId,
      ]);
    }catch(\Exception $e){
      $this->db->rollBack();
      echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
  }

  public function get($slug, $id){
    header('Content-Type: application/json');
    try{
      $unit_id = $this->user['unit_id'];

      $mapelId = $this->mapel->getById($id, $unit_id);

      if($mapelId){
        echo json_encode([
          'status' => 'success',
          'data' => $mapelId
        ]);
      }

    }catch(Exception $e){
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
      ]);
    }
  }

  public function update($slug, $id){
    header('Content-Type: application/json');

    $unit_id = $this->user['unit_id'];

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
      throw new \Exception('CSRF token tidak valid!');
    }

    $validator = new Validator($_POST);
    $validator->required(['csrf_token', 'nama_mapel', 'kode_mapel', 'tingkat_min', 'tingkat_max'])
      ->minLength('nama_mapel', 3)
      ->minLength('kode_mapel', 3)
      ->maxLength('nama_mapel', 100);

    if ($validator->hasErrors()) {
      echo json_encode([
        'status' => 'error',
        'errors' => $validator->getErrors()
      ]);
      return;
    }

    $namaMapel = $validator->sanitize('nama_mapel');
    $kodeMapel = $validator->sanitize('kode_mapel');
    $tingkatMin = $validator->sanitize('tingkat_min');
    $tingkatMax = $validator->sanitize('tingkat_max');

    try {
      $this->db->beginTransaction();

      $mapelIdUpdated = $this->mapel->update($id, [
        'unit_id' => $unit_id,
        'nama_mapel' => $namaMapel,
        'kode_mapel' => $kodeMapel,
        'tingkat_min' => $tingkatMin,
        'tingkat_max' => $tingkatMax,
      ]);

      $this->db->commit();

      echo json_encode([
        'status' => 'success',
        'message' => 'Mapel berhasil diubah',
        'data' => $mapelIdUpdated,
      ]);
    } catch (\Exception $e) {
      $this->db->rollBack();
      echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
  }

  public function delete($slug, $id){
    $unit_id = $this->user['unit_id'];
    
    try{
      $this->db->beginTransaction();

      $deletedId = $this->mapel->delete($id, $unit_id);
       if(!$deletedId){
        throw new Exception("Gagal menghapus data");
       }

      $this->db->commit();

      echo json_encode([
        'status' => 'success',
        'message' => 'Berhasil menghapus mapel'
      ]);

    }catch(Exception $e){
      $this->db->rollBack();
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
      ]);
    }
  }

  public function search($slug){
    $keyword = $_GET['keyword'];
    $unit_id = $this->user['unit_id'];

    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 5;

    if($page < 1) $page = 1;
    if($limit < 1) $limit = 5;

    $offset = ($page - 1) * $limit;

    $total = $this->mapel->countSearch($keyword, $unit_id);
    $data = $this->mapel->searchPaginated($keyword, $unit_id, $limit, $offset);
    $total_pages = $total > 0 ? ceil($total / $limit) : 1;

    echo json_encode([
      'status' => 'success',
      'data' => $data,
      'pagination' => [
        'page' => $page,
        'limit' => $limit,
        'total' => $total,
        'total_pages' => $total_pages,
      ],
    ]);
  }
}