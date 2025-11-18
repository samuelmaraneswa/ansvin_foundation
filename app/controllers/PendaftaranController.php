<?php
namespace App\Controllers;

use App\Core\Config;
use App\Core\Controller;
use App\Core\FlashMessage;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Helpers\Validator;

class PendaftaranController extends Controller
{
  protected $tahunModel;
  protected $calonModel;
  protected $biayaMaster;
  protected $biayaDetail;
  protected $billing;
  protected $billingPendaftaran;

  public function __construct()
  {
    $this->tahunModel = $this->model('TahunAjaran');
    $this->calonModel = $this->model('CalonSiswa');
    $this->biayaMaster = $this->model('BiayaTemplateMaster');
    $this->biayaDetail = $this->model('BiayaTemplateDetail');
    $this->billing = $this->model('BillingAssignment');
    $this->billingPendaftaran = $this->model('BillingPendaftaran');
  }

  public function index(): void
  {
    $this->view('layouts/public_main', [
      'title' => 'Form Pendaftaran',
      'content' => 'pendaftaran/index',
      'page' => 'pendaftaran',
      'base_url' => Config::get('base_url'),
    ]);
  }

  public function formSmp(): void
  {
    $this->view('layouts/public_main', [
      'title' => 'Formulir Pendaftaran SMP Ansvin',
      'content' => 'pendaftaran/form_smp',
      'page' => 'pendaftaran',
      'base_url' => Config::get('base_url'),
    ]);
  }
  
  public function formSma(): void
  {
    $this->view('layouts/public_main', [
      'title' => 'Formulir Pendaftaran SMA Ansvin',
      'content' => 'pendaftaran/form_sma',
      'page' => 'pendaftaran',
      'base_url' => Config::get('base_url'),
    ]);
  }

  /**
   * Proses penyimpanan data form SMP + generate billing otomatis
   */
  public function storeSmp(): void
  {
    try {
      // Ambil input form
      $nama = trim($_POST['nama_lengkap'] ?? '');
      $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
      $alamat = trim($_POST['alamat'] ?? '');

      // Validasi sederhana
      if (empty($nama) || empty($tanggal_lahir) || empty($alamat)) {
        echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi.']);
        return;
      }

      // Ambil tahun ajaran aktif
      $tahunAktif = $this->tahunModel->getActive(); 

      $tahun_ajaran_id = $tahunAktif['id'];
      $unit_id = 3; // SMP Ansvin

      // Generate nomor pendaftaran: ANV + 2digit tahun + kode unit + nomor urut
      $no_pendaftaran = $this->calonModel->generateNoPendaftaran($unit_id, $tahun_ajaran_id);

      // Simpan ke tabel calon_siswa (tanpa file)
      $calon_id = $this->calonModel->insert([
        'no_pendaftaran' => $no_pendaftaran,
        'nama_lengkap' => $nama,
        'tanggal_lahir' => $tanggal_lahir,
        'alamat' => $alamat,
        'unit_id' => $unit_id,
        'tahun_ajaran_id' => $tahun_ajaran_id,
        'status_pendaftaran' => 'MENUNGGU_PEMBAYARAN',
      ]);

      // Ambil template biaya aktif untuk SMP
      $template = $this->biayaMaster->getActiveByUnit($unit_id, $tahun_ajaran_id);

      // Ambil detail biaya template dan hitung total tagihan
      $items = $this->biayaDetail->getByTemplate($template['id']);
      $totalTagihan = 0;
      foreach ($items as $i) {
        $totalTagihan += (float) $i['nominal'];
      }

      // Simpan ke billing_assignment
      $this->billingPendaftaran->insert([ 
        'calon_siswa_id' => $calon_id,
        'total_tagihan' => $totalTagihan,
        'total_bayar' => 0,
        'sisa_tagihan' => $totalTagihan,
        'status_bayar' => 'BELUM'
      ]);

      // Redirect ke halaman sukses
      header("Location: " . Config::get('base_url') . "/pendaftaran/sukses?kode={$no_pendaftaran}");
      exit;

    } catch (\Exception $e) {
      FlashMessage::set('error', $e->getMessage());
    }
  }
  
  public function storeSma(): void
  {
    try {
      // Ambil input form
      $nama = trim($_POST['nama_lengkap'] ?? '');
      $tanggal_lahir = $_POST['tanggal_lahir'] ?? '';
      $alamat = trim($_POST['alamat'] ?? '');

      // Validasi sederhana
      $validator = new Validator($_POST);
      $validator->required(['nama_lengkap','tanggal_lahir','alamat'])
                ->maxLength('nama_lengkap', 100)
                ->minLength('nama_lengkap', 3);

      if($validator->hasErrors()){
        echo json_encode([
          'status' => 'error',
          'errors' => $validator->getErrors()
        ]);
        return;
      }

      //sanitasi
      $nama = $validator->sanitize('nama_lengkap');
      $tanggal_lahir = $validator->sanitize('tanggal_lahir');
      $alamat = $validator->sanitize('alamat');

      // Ambil tahun ajaran aktif
      $tahunAktif = $this->tahunModel->getActive(); 

      $tahun_ajaran_id = $tahunAktif['id'];
      $unit_id = 4; // SMA Ansvin

      // Generate nomor pendaftaran: ANV + 2digit tahun + kode unit + nomor urut
      $no_pendaftaran = $this->calonModel->generateNoPendaftaran($unit_id, $tahun_ajaran_id);

      // Simpan ke tabel calon_siswa (tanpa file)
      $calon_id = $this->calonModel->insert([
        'no_pendaftaran' => $no_pendaftaran,
        'nama_lengkap' => $nama,
        'tanggal_lahir' => $tanggal_lahir,
        'alamat' => $alamat,
        'unit_id' => $unit_id,
        'tahun_ajaran_id' => $tahun_ajaran_id,
        'status_pendaftaran' => 'MENUNGGU_PEMBAYARAN',
      ]);

      // Ambil template biaya aktif untuk SMA
      $template = $this->biayaMaster->getActiveByUnit($unit_id, $tahun_ajaran_id);

      // Ambil detail biaya template dan hitung total tagihan
      $items = $this->biayaDetail->getByTemplate($template['id']);
      $totalTagihan = 0;
      foreach ($items as $i) {
        $totalTagihan += (float) $i['nominal'];
      }

      // Simpan ke billing_assignment
      $this->billingPendaftaran->insert([ 
        'calon_siswa_id' => $calon_id,
        'total_tagihan' => $totalTagihan,
        'total_bayar' => 0,
        'sisa_tagihan' => $totalTagihan,
        'status_bayar' => 'BELUM'
      ]);

      // Redirect ke halaman sukses
      header("Location: " . Config::get('base_url') . "/pendaftaran/sukses?kode={$no_pendaftaran}");
      exit;

    } catch (\Exception $e) {
      FlashMessage::set('error', $e->getMessage());
    }
  }


  public function sukses(): void
  {
    $kode = $_GET['kode'] ?? null;
    if (!$kode) {
      header("Location: " . Config::get('base_url') . "/pendaftaran");
      exit;
    }

    $data = $this->calonModel->getByNo($kode);

    $total = $this->billingPendaftaran->getTotalByCalonSiswa($data['id']);

    $this->view('layouts/public_main', [
      'title' => 'Pendaftaran Berhasil',
      'content' => 'pendaftaran/sukses',
      'kode' => $kode,
      'total_tagihan' => $total,
      'base_url' => Config::get('base_url'),
    ]);
  }

  public function pdf(): void
  {
    $kode = $_GET['kode'] ?? null;
    if (!$kode) {
      header("Location: " . Config::get('base_url') . "/pendaftaran");
      exit;
    }

    $dataCalon = $this->calonModel->getByNo($kode);

    if (!$dataCalon) {
      die("Data pendaftaran tidak ditemukan.");
    }

    $unit_id = $dataCalon['unit_id'];
    $tahunAjaran = $this->tahunModel->getById($dataCalon['tahun_ajaran_id']);

    // 2️⃣ Ambil total tagihan & rincian biaya
    $template = $this->biayaMaster->getActiveByUnit($unit_id, $dataCalon['tahun_ajaran_id']);
    $items = $this->biayaDetail->getByTemplate($template['id']) ?? [];

    $totalTagihan = 0;
    foreach ($items as $i) {
      $totalTagihan += (float) $i['nominal'];
    }

    // 3️⃣ Ambil rekening sekolah
    $rekeningModel = $this->model('RekeningSekolah');
    $rekening = $rekeningModel->getActiveByUnit($unit_id);

    // 4️⃣ Siapkan HTML template
    ob_start();
    include __DIR__ . '/../views/pendaftaran/pdf_template.php';
    $html = ob_get_clean();

    // 5️⃣ Generate PDF
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // 6️⃣ Output PDF ke browser atau unduh langsung
    $filename = "Billing_{$dataCalon['nama_lengkap']}_{$kode}.pdf";
    $filename = preg_replace('/\s+/', '_', $filename); // hilangkan spasi

    $download = isset($_GET['download']); // jika URL mengandung ?download=1

    $dompdf->stream($filename, ["Attachment" => $download]);
  }

  public function detail($id)
  {
    header('Content-Type: application/json');

    try {
        $data = $this->calonModel->findById($id);
        if (!$data) {
            echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
            return;
        }

        echo json_encode($data);
    } catch (\Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
  }

}