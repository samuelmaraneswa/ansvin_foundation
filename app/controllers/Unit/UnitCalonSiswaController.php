<?php

namespace App\Controllers\Unit;

use App\Core\AdminController;
use App\Models\CalonSiswa;
use App\Core\Config;
use App\Core\Auth;
use App\Models\UnitSekolah;

class UnitCalonSiswaController extends AdminController
{
  private CalonSiswa $calonModel;
  private UnitSekolah $unitModel;

  public function __construct()
  {
    parent::__construct();

    // 🔒 Filter khusus halaman pegawai
    Auth::allowRoles(['admin_unit']);

    $this->calonModel = $this->model('calonSiswa');
    // $this-> = $this->model('calonSiswa');
  }

  // Halaman utama
  public function index(): void
  {
    $this->view('layouts/admin_main', [
      'title' => 'Data Calon Siswa',
      'content' => 'unit/pendaftaran/index', // view utama di folder admin/pendaftaran
      'base_url' => Config::get('base_url'),
      'page' => 'calon_siswa'
    ]);
  }

  // Ambil semua data calon siswa (JSON)
  public function fetchAll(): void
  {
    header('Content-Type: application/json');

    try {
      // Ambil unit_id dari session admin_unit yang login
      $unit_id = $_SESSION['user']['unit_id'] ?? null;
      if (!$unit_id) {
          throw new \Exception('Unit tidak ditemukan pada sesi login.');
      }

      $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
      $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
      $offset = ($page - 1) * $limit;

      $totalData = $this->calonModel->countByUnit($unit_id);
      $totalPages = ceil($totalData / $limit);

      // Ambil data calon siswa hanya untuk unit ini
      $data = $this->calonModel->getAllWithBillingByUnit($unit_id, $limit, $offset);

      echo json_encode([
          'status' => 'success',
          'data' => $data,
          'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total_data' => $totalData,
            'total_pages' => $totalPages
          ]
      ]);
    } catch (\Exception $e) {
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
      ]);
    }
  }

  public function updateStatus(): void
  {
    header('Content-Type: application/json');

    try {
      // --- Validasi dasar
      $id = $_POST['id'] ?? null;
      $statusBayar = strtoupper(trim($_POST['status_bayar'] ?? ''));
      $nominalBayar = (float) ($_POST['nominal_bayar'] ?? 0);
      $unit_id = $_SESSION['user']['unit_id'] ?? null;

      if (!$id || !$statusBayar || !$unit_id) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        return;
      }

      // --- Pastikan calon siswa memang milik unit admin yang login
      $calon = $this->calonModel->getById($id);
      if (!$calon || (int)$calon['unit_id'] !== (int)$unit_id) {
        echo json_encode(['status' => 'error', 'message' => 'Anda tidak berhak mengubah data ini.']);
        return;
      }

      // --- Ambil data billing
      $billing = $this->calonModel->getBillingByCalonId($id);
      if (!$billing) {
        echo json_encode(['status' => 'error', 'message' => 'Data billing tidak ditemukan.']);
        return;
      }

      $totalTagihan = (float) $billing['total_tagihan'];
      $totalBayarLama = (float) $billing['total_bayar'];
      $totalBayarBaru = $totalBayarLama + $nominalBayar;
      $sisaTagihan = max(0, $totalTagihan - $totalBayarBaru);

      // --- Tentukan status akhir
      if ($statusBayar === 'LUNAS') {
        $statusFinal = 'LUNAS';
        $totalBayarBaru = $totalTagihan;
        $sisaTagihan = 0;
      } elseif ($statusBayar === 'CICIL') {
        $statusFinal = ($sisaTagihan <= 0) ? 'LUNAS' : 'CICIL';
      } else {
        $statusFinal = 'BELUM';
      }

      // --- Update billing_pendaftaran
      $this->calonModel->updateBillingStatus($id, [
        'status_bayar' => $statusFinal,
        'total_bayar' => $totalBayarBaru,
        'sisa_tagihan' => $sisaTagihan,
        'tanggal_bayar' => date('Y-m-d H:i:s')
      ]);

      // --- Jika CICIL atau LUNAS, aktifkan siswa
      if (in_array($statusFinal, ['CICIL', 'LUNAS'])) {
        $this->calonModel->updateStatusPendaftaran($id, 'DITERIMA');
        $this->calonModel->aktivasiSiswaBaru($id);
      }

      echo json_encode([
        'status' => 'success',
        'message' => "Status pembayaran berhasil diperbarui menjadi {$statusFinal}.",
        'data' => [
          'total_bayar' => $totalBayarBaru,
          'sisa_tagihan' => $sisaTagihan,
          'status' => $statusFinal
        ]
      ]);
    } catch (\Exception $e) {
      echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
  }

  public function search(): void
  {
    header('Content-Type: application/json');

    $keyword = $_GET['keyword'] ?? '';
    $unit_id = $_SESSION['user']['unit_id'] ?? null;

    $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $offset = ($page - 1) * $limit;

    try{
      if(!$unit_id){
        throw new \Exception("Unit tidak di temukan.");
      }

      $totalData = $this->calonModel->countSearchByUnit($unit_id, $keyword);
      $totalPages = ceil($totalData / $limit);

      $result = $this->calonModel->searchByUnit($unit_id, $keyword, $limit, $offset);

      echo json_encode([
        'status' => 'success',
        'data' => $result,
        'pagination' => [
          'page' => $page,
          'limit' => $limit,
          'total_data' => $totalData,
          'total_pages' => $totalPages
        ]
      ]);

    }catch(\Exception $e){
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
      ]);
    }
  }
}