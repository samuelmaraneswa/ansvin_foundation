<?php
namespace App\Controllers\Unit;

use App\Core\AdminController;
use App\Core\Auth;
use App\Core\Config;
use App\Core\Database;
use App\Helpers\Validator;
use Exception;
use PDO;

class UnitPegawaiController extends AdminController
{
  protected $modelPegawai;
  protected $modelUsersPegawai;
  protected $jabatan;
  protected $unit;
  protected PDO $db;

  public function __construct()
  {
    parent::__construct();

    // 🔒 Filter khusus halaman pegawai
    Auth::allowRoles(['admin_unit']);

    // Inisialisasi model dan koneksi database
    $this->modelPegawai = $this->model('Pegawai');
    $this->modelUsersPegawai = $this->model('UsersPegawaiModel');
    $this->jabatan = $this->model('Jabatan');
    $this->unit = $this->model('UnitSekolah');
    $this->db = Database::connect();
  }

  /**
   * Halaman utama pegawai (untuk unit terkait)
   */
  public function index()
  {
    $unitId = $this->user['unit_id'];

    // Ambil semua pegawai milik unit login
    $pegawai = $this->modelPegawai->getByUnit($unitId);
    $jabatan = $this->jabatan->getAll();

    $this->view('layouts/admin_main', [
      'title' => 'Pegawai',
      'content' => 'unit/pegawai/index',
      'page' => 'pegawai',
      'pegawai' => $pegawai,
      'base_url' => Config::get('base_url'),
      'jabatan' => $jabatan,
    ]);
  }

  /**
   * AJAX: ambil semua pegawai milik unit user
   */
  public function fetchAll()
  {
    try{
      header('Content-Type: application/json');

      $unitId = $this->user['unit_id'];

      // ambil parameter pagination
      $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
      $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
      $offset = ($page - 1) * $limit;

      // Ambil semua pegawai dari unit login
      $pegawai = $this->modelPegawai->getByUnitWithJabatanPaginated($unitId, $limit, $offset);

      $total = $this->modelPegawai->countByUnit($unitId);

      echo json_encode([
        'status' => 'success',
        'data' => $pegawai,
        'total' => (int) $total,
        'page' => (int) $page,
        'limit' => (int) $limit,
      ]);

    }catch (\Throwable $e) {
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
      ]);
    }
  }

  public function generateNIP($slug)
  {
    header('Content-Type: application/json');

    try {
      // Pastikan user login dan punya unit_id
      $unitId = $_SESSION['user']['unit_id'] ?? null;
      if (!$unitId) {
        echo json_encode([
          'success' => false,
          'nip' => null,
          'message' => 'Unit tidak ditemukan pada session user.'
        ]);
        return;
      }

      // Ambil data unit berdasarkan unit_id dari session
      $unit = $this->unit->find($unitId);
      if (!$unit) {
        echo json_encode([
          'success' => false,
          'nip' => null,
          'message' => 'Data unit tidak ditemukan di database.'
        ]);
        return;
      }

      // Gunakan fungsi universal generateNIP() dari model pegawai
      $nip = $this->modelPegawai->generateNIP($unit['nama']);

      echo json_encode([
        'success' => true,
        'nip' => $nip,
        'unit' => $unit['nama']
      ]);
    } catch (\Throwable $e) {
      echo json_encode([
        'success' => false,
        'nip' => null,
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
      ]);
    }
  }

  public function store($slug)
  {
    header('Content-Type: application/json');

    try {
        // ====== CSRF CHECK ======
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new \Exception('CSRF token tidak valid!');
        }

        // ====== VALIDASI INPUT ======
        $validator = new Validator($_POST);
        $validator->required(['namaUnit', 'jabatan_id_unit']);
        if (empty($_POST['pegawaiUnit_id'])) { // tambah mode
            $validator->required(['password_unit']);
        }
        $validator->minLength('namaUnit', 3)
                  ->maxLength('namaUnit', 100)
                  ->image('foto_unit');

        if ($validator->hasErrors()) {
          echo json_encode([
            'status' => 'error',
            'errors' => $validator->getErrors()
          ]);
          return;
        }

        // ====== SANITASI INPUT ======
        $pegawaiId     = $_POST['pegawaiUnit_id'] ?? null;
        $nama          = $validator->sanitize('namaUnit');
        $nip           = $validator->sanitize('nipUnit');
        $jabatan_id    = (int)$_POST['jabatan_id_unit'];
        $email         = $validator->sanitize('email_unit');
        $telepon       = $validator->sanitize('telepon_unit');
        $tanggal_lahir = $_POST['tanggal_lahir_unit'] ?? null;
        $alamat        = $validator->sanitize('alamat_unit');
        $status_aktif  = $_POST['status_aktif_unit'] ?? 1;
        $password      = !empty($_POST['password_unit']) ? password_hash($_POST['password_unit'], PASSWORD_DEFAULT) : null;
        $role          = 'pegawai'; // otomatis

        // Ambil unit dari session
        $unit_id = $_SESSION['user']['unit_id'] ?? null;
        if (!$unit_id) {
          throw new \Exception('Unit tidak ditemukan di session.');
        }

        // ====== HANDLE FOTO ======
        $fotoPath = null;
        if (!empty($_FILES['foto_unit']['name'])) {
          if ($pegawaiId) {
            // hapus foto lama jika ada
            $oldFoto = $this->modelPegawai->getById($pegawaiId)['foto'] ?? null;
            if ($oldFoto && $oldFoto !== 'uploads/pegawai/default_img.jpg' &&
              file_exists(__DIR__ . '/../../../public/' . $oldFoto)) {
              unlink(__DIR__ . '/../../../public/' . $oldFoto);
            }
          }

          $uploadDir = __DIR__ . '/../../../public/uploads/pegawai/';
          if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);

          $filename = uniqid('pgw_') . '_' . basename($_FILES['foto_unit']['name']);
          $target = $uploadDir . $filename;

          if (move_uploaded_file($_FILES['foto_unit']['tmp_name'], $target)) {
            $fotoPath = 'uploads/pegawai/' . $filename;
          }
        }

        $this->db->beginTransaction();

        // ====== TAMBAH atau EDIT ======
        if ($pegawaiId) {
          // ----- EDIT MODE -----
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
          if ($fotoPath) $updateData['foto'] = $fotoPath;

          $this->modelPegawai->update($pegawaiId, $updateData);
          $jabatanData = $this->jabatan->find($jabatan_id);
          
          if ($password) {
            $this->modelUsersPegawai->updateByPegawaiId($pegawaiId, ['password' => $password]);
          }

          echo json_encode([
            'status' => 'success',
            'message' => 'Data pegawai berhasil diperbarui.',
            'data' => [
              'id' => $pegawaiId,
              'nama' => $nama,
              'nip' => $nip,
              'nama_jabatan' => $jabatanData['nama'],
            ]
          ]);
        } else {
          // ----- TAMBAH MODE -----
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

          $this->modelUsersPegawai->insert([
            'pegawai_id' => $pegawaiId,
            'nip'        => $nip,
            'password'   => $password,
            'role'       => $role,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
          ]);

          echo json_encode([
            'status' => 'success',
            'message' => 'Data pegawai berhasil ditambahkan.',
            'data' => [
              'id' => $pegawaiId,
              'nama' => $nama,
              'nip' => $nip,
            ]
          ]);
        }

        $this->db->commit();

    } catch (\Exception $e) {
      $this->db->rollBack();
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
      ]);
    }
  }

  public function getById($slug, $id)
  {
    header('Content-Type: application/json');
    try {
        $pegawai = $this->modelPegawai->findByIdAndUnit($id, $this->user['unit_id']);

        if ($pegawai) {
            echo json_encode(['status' => 'success', 'data' => $pegawai]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Pegawai tidak ditemukan.']);
        }
    } catch (\Throwable $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
  }

  public function delete($slug, $id)
  {
    header('Content-Type: application/json');

    try {
      $pegawai = $this->modelPegawai->findByIdAndUnit($id, $this->user['unit_id']);
      if (!$pegawai) {
        echo json_encode(['status' => 'error', 'message' => 'Data pegawai tidak ditemukan atau tidak dapat diakses.']);
        return;
      }

      // Hapus foto jika bukan default
      if (!empty($pegawai['foto']) && $pegawai['foto'] !== 'uploads/pegawai/default_img.jpg') {
        $fotoPath = __DIR__ . '/../../../public/' . $pegawai['foto'];
        if (file_exists($fotoPath)) unlink($fotoPath);
      }

      // Hapus data pegawai
      $this->modelPegawai->delete($id);

      echo json_encode([
        'status' => 'success',
        'message' => 'Data pegawai berhasil dihapus.'
      ]);
    } catch (\Throwable $e) {
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
      ]);
    }
  }

  public function search($slug)
  {
    header('Content-Type: application/json');
    $keyword = $_GET['keyword'] ?? '';
    $unit_id = $this->user['unit_id'];

    // Tambahkan pagination
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;
    $offset = ($page - 1) * $limit;

    $data = $this->modelPegawai->searchByUnitPaginated($unit_id, $keyword, $limit, $offset);
    $total = $this->modelPegawai->countSearchByUnit($unit_id, $keyword);

    echo json_encode([
        'status' => 'success',
        'data'   => $data,
        'total' => (int) $total,
        'page' => (int) $page,
        'limit' => (int) $limit,
    ]);
  }

}
