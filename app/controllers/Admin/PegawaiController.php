<?php
namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Database;
use App\Core\Config;
use App\Helpers\Validator;
use PDO;
use Exception;
use App\Core\Auth;

class PegawaiController extends AdminController
{
  protected $modelPegawai;
  protected $modelUsersPegawai;
  protected $unit;
  private PDO $db;
  protected $jabatan;

  public function __construct()
  {
    parent::__construct();

    // 🔒 Filter khusus halaman pegawai
    Auth::allowRoles(['super_admin']);

    $this->modelPegawai = $this->model('Pegawai');
    $this->modelUsersPegawai = $this->model('UsersPegawaiModel');
    $this->unit = $this->model('UnitSekolah');
    $this->jabatan = $this->model('Jabatan');
    $this->db = Database::connect();
  }

  public function index()
  {
    $page = $_GET['page'] ?? 1;
    $limit = 5;
    $offset = ($page - 1) * $limit;

    // Ambil data paginated
    $pegawai = $this->modelPegawai->getAllPaginated($limit, $offset);
    $total = $this->modelPegawai->countPegawai();

    // Data tambahan tetap sama
    $units = $this->unit->getAll();
    $nipBaru = $this->modelPegawai->generateNIP('UNIT');
    $jabatan = $this->jabatan->getAll();

    // Kirim ke view
    $this->view('layouts/admin_main', [
        'title' => 'Pegawai',
        'content' => 'admin/pegawai/index',
        'base_url' => Config::get('base_url'),
        'page' => 'pegawai',
        'units' => $units,
        'nipBaru' => $nipBaru,
        'jabatan' => $jabatan,
        'pegawai' => $pegawai,
        'total' => $total,
        'currentPage' => $page,
        'limit' => $limit,
    ]);
  }

  public function searchTable()
  {
    $keyword = $_GET['searchPegawai'] ?? '';
    $unit_id = $_GET['unit_id_sekolah'] ?? '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 5;
    $offset = ($page - 1) * $limit;

    if (!empty($keyword) || !empty($unit_id)) {
      $pegawai = $this->modelPegawai->searchPegawai($keyword, $unit_id, $limit, $offset);
      $total = $this->modelPegawai->countPegawai($keyword, $unit_id);
    } else {
      $pegawai = $this->modelPegawai->getAllPaginated($limit, $offset);
      $total = $this->modelPegawai->countPegawai();
    }

    // $debug = [
    //   'keyword' => $keyword,
    //   'unit_id' => $unit_id,
    //   'total_count' => $total,
    // ];

    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'success',
        'data' => $pegawai,
        'total' => $total,
        'page' => $page,
        'limit' => $limit,
        // 'debug' => $debug,
    ]);
    exit;
  }

  public function generateNIP()
  { 
    header('Content-Type: application/json');

    try {
      $unitId = isset($_GET['unit_id']) ? (int)$_GET['unit_id'] : null;
      if (!$unitId) {
        echo json_encode(['success' => false, 'nip' => null, 'message' => 'Unit tidak ditemukan']);
        return;
      }

      $unit = $this->unit->find($unitId); // Ambil data unit_sekolah
      if (!$unit) {
        echo json_encode(['success' => false, 'nip' => null, 'message' => 'Unit tidak ada']);
        return;
      }

      $nip = $this->modelPegawai->generateNIP($unit['nama']);

      echo json_encode(['success' => true, 'nip' => $nip, 'unit' => $unit]);
    } catch (\Throwable $e) {
      echo json_encode([
        'success' => false,
        'nip' => null,
        'message' => $e->getMessage()
      ]);
    }
  }

  public function store()
  {
    header('Content-Type: application/json');

    try {
      // ====== CSRF CHECK ======
      if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new \Exception('CSRF token tidak valid!');
      }

      // ====== VALIDASI INPUT ======
      $validator = new Validator($_POST);
      $validator->required(['unit_id','nama','jabatan_id','role']);
      if(empty($_POST['pegawai_id'])){ // EDIT MODE: jangan wajibkan password
          $validator->required(['password']);
      }
      $validator->minLength('nama', 3)
                ->maxLength('nama', 100)
                ->inList('role', ['super_admin','admin_unit','pegawai','siswa'])
                ->image('foto');

      if ($validator->hasErrors()) {
        $errors = $validator->getErrors();
        // kirim error ke front-end untuk ditampilkan di bawah field
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        return;
      }

      // ====== SANITASI INPUT ======
      $pegawai_id    = $_POST['pegawai_id'] ?? null; // EDIT MODE: ada saat edit
      $nama         = $validator->sanitize('nama');
      $unit_id      = (int)$_POST['unit_id'];
      $nip          = $validator->sanitize('nip');
      $jabatan_id   = (int)$_POST['jabatan_id'];
      $email        = $validator->sanitize('email');
      $telepon      = $validator->sanitize('telepon');
      $tanggal_lahir= $_POST['tanggal_lahir'] ?? null;
      $alamat       = $validator->sanitize('alamat');
      $status_aktif = $_POST['status_aktif'] ?? 1;
      $role         = $validator->sanitize('role');
      $password      = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

      // ====== HANDLE FOTO ======
      $fotoPath = null;

      if (!empty($_FILES['foto']['name'])) {
        // Jika edit mode, hapus foto lama
        if ($pegawai_id) {
          // Hapus file lama jika bukan default
          $oldFoto = $this->modelPegawai->getById($pegawai_id)['foto'] ?? null;
          if($oldFoto && $oldFoto !== 'uploads/pegawai/default_img.jpg' && file_exists(__DIR__ . '/../../../public/' . $oldFoto)){
            unlink(__DIR__ . '/../../../public/' . $oldFoto);
          }
        }

        // Pastikan folder ada
        $uploadDir = __DIR__ . '/../../../public/uploads/pegawai/';
        if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

        // Upload file baru
        $filename = uniqid('pgw_') . '_' . basename($_FILES['foto']['name']);
        $target = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
            $fotoPath = 'uploads/pegawai/' . $filename; // foto baru
        }
        // Jangan set default di sini! Kalau upload gagal, biarkan null agar foto lama tetap
      }

      $this->db->beginTransaction();

      // ====== INSERT atau UPDATE ======
      if ($pegawai_id) {
        // ====== EDIT MODE ======
        $updateData = [
          'nama' => $nama,
          'nip' => $nip,
          'unit_id' => $unit_id,
          'jabatan_id' => $jabatan_id,
          'email' => $email,
          'telepon' => $telepon,
          'tanggal_lahir' => $tanggal_lahir,
          'alamat' => $alamat,
          'status_aktif' => $status_aktif,
        ];

        if($fotoPath) $updateData['foto'] = $fotoPath;

        $this->modelPegawai->update($pegawai_id, $updateData); // EDIT MODE: update pegawai
        $this->modelUsersPegawai->updateByPegawaiId($pegawai_id, ['role' => $role]); // EDIT MODE: update role
        if($password){ // EDIT MODE: update password hanya jika diisi
          $this->modelUsersPegawai->updateByPegawaiId($pegawai_id, ['password' => $password]);
        }

        $unitData = $this->unit->find($unit_id);

        echo json_encode([
          'status' => 'success',
          'message' => 'Data pegawai berhasil diupdate.',
          'data' => [
            'id' => $pegawai_id,
            'nama' => $nama,
            'nip' => $nip,
            'nama_unit' => $unitData['nama'],
          ]
        ]);

      } else {
      // ====== TAMBAH MODE ======
      $pegawaiId = $this->modelPegawai->insert([
        'nama' => $nama,
        'nip' => $nip,
        'unit_id' => $unit_id,
        'jabatan_id' => $jabatan_id,
        'email' => $email,
        'telepon' => $telepon,
        'tanggal_lahir' => $tanggal_lahir,
        'alamat' => $alamat,
        'status_aktif' => $status_aktif,
        'foto' => $fotoPath ?: 'uploads/pegawai/default_img.jpg',
      ]);

      // Setelah $pegawaiId berhasil dibuat
      $this->modelUsersPegawai->insert([
        'pegawai_id' => $pegawaiId,
        'nip'        => $nip,
        'password'   => $password,
        'role'       => $role,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
      ]);
      
      $unitData = $this->unit->find($unit_id);
      
      echo json_encode([ 
        'status' => 'success',
        'message' => 'Data pegawai berhasil ditambahkan.',
        'data' => [
          'id' => $pegawaiId,
          'nama' => $nama,
          'nip' => $nip,
          'nama_unit' => $unitData['nama'],
          ]
        ]);
      }
      
      $this->db->commit();

    } catch (\Exception $e) {
      $this->db->rollBack();
      echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
  }

  public function get($id)
  {
    header('Content-Type: application/json');

    try {
      $pegawai = $this->modelPegawai->getById($id); 
      if (!$pegawai) {
        throw new Exception("Pegawai tidak ditemukan.");
      } 

      // Lanjutkan JSON response
      // echo json_encode([
      //     'status' => 'success',
      //     'data' => $pegawai
      // ]);
      // exit;

      // ambil nama unit
      $unitData = $this->unit->find($pegawai['unit_id']);

      echo json_encode([
        'status' => 'success',
        'data' => [
          'id' => $pegawai['id'],
          'nama' => $pegawai['nama'],
          'nip' => $pegawai['NIP'],
          'unit_id' => $pegawai['unit_id'],
          'jabatan_id' => $pegawai['jabatan_id'],
          'role' => $pegawai['role'],
          'email' => $pegawai['email'],
          'telepon' => $pegawai['telepon'],
          'tanggal_lahir' => $pegawai['tanggal_lahir'],
          'alamat' => $pegawai['alamat'],
          'status_aktif' => $pegawai['status_aktif'],
          'foto' => $pegawai['foto'] ? $pegawai['foto'] : 'uploads/pegawai/default_img.jpg',
          'nama_unit' => $unitData['nama'] ?? '',
        ]
      ]);
    } catch (Exception $e) {
      echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
  }

  public function delete($id)
  {
    header('Content-Type: application/json');

    try {
      // Pastikan ID valid
      if (empty($id)) {
        throw new \Exception('ID pegawai tidak valid.');
      }

      // Ambil data pegawai untuk cek foto lama
      $pegawai = $this->modelPegawai->getById($id);
      if (!$pegawai) {
        throw new \Exception('Data pegawai tidak ditemukan.');
      }

      $this->db->beginTransaction();

      // Hapus foto lama jika bukan default
      if (!empty($pegawai['foto']) && $pegawai['foto'] !== 'uploads/pegawai/default_img.jpg') {
        $fotoPath = __DIR__ . '/../../../public/' . $pegawai['foto'];
        if (file_exists($fotoPath)) {
          unlink($fotoPath);
        }
      }

      // Hapus data terkait di users_pegawai
      $this->modelUsersPegawai->deleteByPegawaiId($id);

      // Hapus data pegawai
      $this->modelPegawai->delete($id);

      $this->db->commit();

      echo json_encode([
        'status' => 'success',
        'message' => 'Data pegawai berhasil dihapus.'
      ]);
    } catch (\Exception $e) {
      $this->db->rollBack();
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
      ]);
    }
  }

}
