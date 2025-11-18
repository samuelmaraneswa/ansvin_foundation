<?php

namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Models\CalonSiswa;
use App\Core\Config;
use App\Core\Auth;

class CalonSiswaController extends AdminController
{
  private CalonSiswa $calonModel;

  public function __construct()
  {
    parent::__construct();

    // 🔒 Filter khusus halaman pegawai
    Auth::allowRoles(['super_admin']);

    $this->calonModel = $this->model('calonSiswa');
  }

  // Halaman utama
  public function index(): void
  {
    $this->view('layouts/admin_main', [
      'title' => 'Data Calon Siswa',
      'content' => 'admin/pendaftaran/index', // view utama di folder admin/pendaftaran
      'base_url' => Config::get('base_url'),
      'page' => 'calon_siswa'
    ]);
  }

  // Ambil semua data calon siswa (JSON)
  public function fetchAll(): void
  {
    header('Content-Type: application/json');

    try {
      $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
      $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
      $offset = ($page - 1) * $limit;

      $data = $this->calonModel->getAllWithBilling($limit, $offset);
      $total = $this->calonModel->countAll();

      echo json_encode([
        'status' => 'success',
        'data' => $data,
        'pagination' => [
          'page' => $page,
          'limit' => $limit,
          'total' => $total,
          'total_pages' => ceil($total / $limit)
        ]
      ]);
    } catch (\Exception $e) {
      echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
  }

  public function updateStatus(): void
  {
    header('Content-Type: application/json');

    try {
      $id = $_POST['id'] ?? null;
      $statusBayar = strtoupper(trim($_POST['status_bayar'] ?? ''));
      $nominalBayar = (float) ($_POST['nominal_bayar'] ?? 0);

      if (!$id || !$statusBayar) { 
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap.']);
        return;
      }

      // Ambil data billing calon siswa
      $billing = $this->calonModel->getBillingByCalonId($id);
      if (!$billing) {
        echo json_encode(['status' => 'error', 'message' => 'Data billing tidak ditemukan.']);
        return;
      }

      $totalTagihan = (float) $billing['total_tagihan'];
      $totalBayarLama = (float) $billing['total_bayar'];
      $totalBayarBaru = $totalBayarLama + $nominalBayar;
      $sisaTagihan = max(0, $totalTagihan - $totalBayarBaru);

      // Tentukan status akhir
      if ($statusBayar === 'LUNAS') {
        $statusFinal = 'LUNAS';
        $totalBayarBaru = $totalTagihan; // full paid
        $sisaTagihan = 0;
      } elseif ($statusBayar === 'CICIL') {
        $statusFinal = ($sisaTagihan <= 0) ? 'LUNAS' : 'CICIL';
      } else {
        $statusFinal = 'BELUM';
      }

      // Update tabel billing_pendaftaran
      $this->calonModel->updateBillingStatus($id, [
        'status_bayar' => $statusFinal,
        'total_bayar' => $totalBayarBaru,
        'sisa_tagihan' => $sisaTagihan,
        'tanggal_bayar' => date('Y-m-d H:i:s')
      ]);

      // Jika LUNAS → ubah status calon siswa dan aktifkan siswa baru
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

  // public function search(): void
  // {
  //   header('Content-Type: application/json');

  //   try {
  //     $keyword = $_GET['keyword'] ?? '';

  //     if (trim($keyword) === '') {
  //       echo json_encode([
  //         'status' => 'error',
  //         'message' => 'Keyword pencarian kosong.',
  //         'data' => []
  //       ]);
  //       return; 
  //     }

  //     // panggil model untuk mencari calon siswa berdasarkan keyword
  //     $data = $this->calonModel->search($keyword);

  //     echo json_encode([
  //       'status' => 'success',
  //       'data' => $data
  //     ]);
  //   } catch (\Exception $e) {
  //     echo json_encode([
  //       'status' => 'error',
  //       'message' => $e->getMessage()
  //     ]);
  //   }
  // }

  public function search(): void
  {
    header('Content-Type: application/json');

    try {
      $keyword = $_GET['keyword'] ?? '';
      $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
      $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
      $offset = ($page - 1) * $limit;

      if (trim($keyword) === '') {
        echo json_encode([
          'status' => 'error',
          'message' => 'Keyword pencarian kosong.', 
          'data' => []
        ]);
        return;
      }

      // 🔹 ambil data hasil search dengan pagination
      $data = $this->calonModel->search($keyword, $limit, $offset);
      // echo json_encode([
      //     'data' => $data,
      //     ]);
      //   return;
      $total = $this->calonModel->countSearch($keyword);

      echo json_encode([
        'status' => 'success',
        'data' => $data,
        'pagination' => [
          'page' => $page,
          'limit' => $limit,
          'total' => $total,
          'offset' => $offset,
          'total_pages' => ceil($total / $limit)
        ]
      ]);
    } catch (\Exception $e) {
      echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
      ]);
    }
  }

}
