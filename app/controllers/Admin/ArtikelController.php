<?php
namespace App\Controllers\Admin;

use App\Core\AdminController;
use App\Core\Auth;
use App\Core\FlashMessage;
use App\Core\Config;
use App\Core\Database;
use Exception;
use App\Helpers\Validator;
use App\Helpers\FileHelper;
use PDO;

class ArtikelController extends AdminController
{
  private $artikelModel;
  private $kategoriModel;
  private PDO $db;

  public function __construct()
  {
    parent::__construct();

    // 🔒 Filter khusus halaman pegawai
    Auth::allowRoles(['super_admin']);
    
    $this->artikelModel = $this->model('Artikel');
    $this->kategoriModel = $this->model('Kategori');
    $this->db = Database::connect();
  }

  // GET /artikel
  public function index()
  {
    $search = $_GET['search'] ?? '';
    $kategori_id = $_GET['kategori_id'] ?? '';
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    $limit = 5;
    $offset = ($currentPage - 1) * $limit;

    $totalData = $this->artikelModel->countFiltered($search, $kategori_id);
    $totalPages = ceil($totalData / $limit);

    $artikels = $this->artikelModel->getFiltered($search, $kategori_id, $limit, $offset);
    $kategoriList = $this->kategoriModel->getAll();

    // Kirim ke layout utama
    $this->view('layouts/admin_main', [
      'title' => 'Daftar Artikel',
      'content' => 'admin/artikel/index',
      'base_url' => Config::get('base_url'),
      'artikels' => $artikels,
      'kategoriList' => $kategoriList,
      'search' => $search,
      'kategori_id' => $kategori_id,
      'currentPage' => $currentPage,
      'totalPages' => $totalPages,
      'page' => 'artikel'
    ]);
  }

  // GET /artikel/tambah
  public function create()
  {
    // Ambil daftar kategori dari model (jika sudah pakai tabel kategori)
    $kategori = $this->kategoriModel->getAll();

    $user = $_SESSION['user'] ?? null;
    // Kirim ke layout utama
    $this->view('layouts/admin_main', [
      'title' => 'Tambah Artikel',
      'content' => 'admin/artikel/tambah',
      'user' => $user,
      'kategori' => $kategori,
      'page' => 'artikel'
    ]);
  }

  public function store()
  {

    $uploadBase = __DIR__ . '/../../../public/assets/img/uploads/';
    $tmpDir     = $uploadBase . 'tmp/';

    try {
      if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        FlashMessage::set('error', 'CSRF token tidak valid!');
        header("Location: " . Config::get('base_url') . "/admin/artikel/tambah");
        exit;
      }

      // ====== VALIDATOR ======
      $validator = new Validator($_POST);
      $validator->required(['judul','isi','kategori_id','status'])
                ->maxLength('judul', 255)
                ->minLength('isi', 10)
                ->inList('status', ['draft','publish','archived'])
                ->image('thumbnail')
                ->multipleImages('images');

      if ($validator->hasErrors()) {
        $errors = $validator->getErrors();
        $messages = implode('<br>', array_map(fn($e) => implode(', ', $e), $errors));
        throw new Exception($messages);
      }

      // ====== SANITASI INPUT ======
      $judul = $validator->sanitize('judul');
      $kategoriId = $_POST['kategori_id'];
      $status     = $_POST['status'];
      $tanggalPosting = $_POST['tanggal_posting'];

      $isi = $_POST['isi'];

      // --- Mulai transaksi DB ---
      $this->db->beginTransaction();

      // --- Insert artikel dulu tanpa gambar ---
      $artikelId = $this->artikelModel->insert([
        'judul'           => $judul,
        'kategori_id'     => $kategoriId,
        'isi'             => $isi,
        'author_id'       => $_SESSION['user']['id'],
        'unit_id'         => $_SESSION['user']['unit_id'],
        'status'          => $status,
        'tanggal_posting' => $tanggalPosting,
      ]);

      // --- Buat folder artikel khusus ---
      $artikelFolder = $uploadBase . "artikel_{$artikelId}/";
      if (!is_dir($artikelFolder)) mkdir($artikelFolder, 0777, true);

      // --- Proses gambar inline (TinyMCE) ---
      $baseUrl = Config::get('base_url');

      // Hapus path aneh yang disisipkan TinyMCE
      $isi = str_replace(
        ['../../' . $baseUrl, '../..' . $baseUrl, '../../', $baseUrl . '/admin'],
        '',
        $isi
      );

      // Tangkap semua gambar dari folder tmp/
      preg_match_all('/assets\/img\/uploads\/tmp\/([\w\-_\.]+)/', $isi, $matches);

      if (!empty($matches[1])) {
        foreach ($matches[1] as $fileName) {
          $tmpPath   = $tmpDir . $fileName;
          $finalPath = $artikelFolder . $fileName;

          if (file_exists($tmpPath)) {
            // Pindahkan gambar ke folder artikel
            rename($tmpPath, $finalPath);

            $newRelPath = "assets/img/uploads/artikel_{$artikelId}/$fileName";
            $publicPath = $baseUrl . '/' . $newRelPath;

            // Ganti path lama di konten dengan path baru
            $isi = str_replace(
              ["assets/img/uploads/tmp/$fileName", "/assets/img/uploads/tmp/$fileName"],
              $publicPath,
              $isi
            );

            // ⛔ Tidak perlu uploadImage() — tidak disimpan ke tabel
          }
        }
      } 

      // --- Update isi artikel dengan path final ---
      $this->artikelModel->update($artikelId, ['isi' => $isi]);

      // --- Upload thumbnail ---
      if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $this->artikelModel->uploadImage($artikelId, $_FILES['thumbnail'], true, $artikelFolder);
      }

      // --- Upload multiple images tambahan + caption ---
      if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $captions = $_POST['captions'] ?? [];
        $images = $_FILES['images'];

        foreach($images['name'] as $i => $name){
          if($images['error'][$i] !== UPLOAD_ERR_OK) continue;

          $file = [
            'name' => $images['name'][$i],
            'tmp_name' => $images['tmp_name'][$i],
            'caption' => $captions[$i] ?? null,
          ];

          $this->artikelModel->uploadImage($artikelId, $file, false, $artikelFolder);          
        }
      }

      // --- Semua sukses ---
      $this->db->commit();

      FlashMessage::set('success', 'Artikel berhasil ditambahkan!');
      header("Location: " . Config::get('base_url') . "/admin/artikel");
      exit;

    } catch (\Exception $e) {
      // Rollback DB bila ada transaksi
      if ($this->db->inTransaction()) $this->db->rollBack();

      // Hapus folder artikel jika sudah sempat dibuat
      if (isset($artikelFolder) && is_dir($artikelFolder)) {
        FileHelper::deleteDir($artikelFolder);
      }

      // simpan input user sementara
      $_SESSION['old'] = $_POST;

      FlashMessage::set('error', $e->getMessage());
      header("Location: " . Config::get('base_url') . "/admin/artikel/tambah");
      exit;
    }finally{
      $this->clearTmpFolder($tmpDir);
    }
  }

  // Fungsi bantu untuk bersihkan folder tmp
  private function clearTmpFolder($tmpDir)
  {
    if (is_dir($tmpDir)) {
      $files = glob($tmpDir . '*'); // semua file
      foreach ($files as $file) {
        if (is_file($file)) unlink($file);
      }
    }
  }

  public function detail($id)
  {
    // Ambil artikel beserta kategori dan semua gambar
    $artikel = $this->artikelModel->getById($id);
    if(!$artikel){
      // jika tidak ditemukan, redirect atau tampilkan 404
      FlashMessage::set('error', 'Artikel tidak ditemukan');
      header("Location: " . Config::get('base_url') . "/admin/artikel");
      exit;
    }

    $this->view('layouts/admin_main', [
      'title' => 'Detail Artikel',
      'content' => 'admin/artikel/detail',
      'artikel' => $artikel,
      'base_url' => Config::get('base_url'),
      'page' => 'artikel',
    ]); 
  }

  public function edit($id)
  {
    // Ambil artikel berdasarkan ID
    $artikel = $this->artikelModel->getById($id);
    if (!$artikel) {
      FlashMessage::set('error', 'Artikel tidak ditemukan.');
      header('Location: ' . Config::get('base_url') . '/admin/artikel');
      exit;
    }

    // Ambil daftar kategori
    $kategori = $this->kategoriModel->getAll();
    $user = $_SESSION['user'] ?? null;

    // Kirim ke view
    $this->view('layouts/admin_main', [
      'title' => 'Edit Artikel',
      'content' => 'admin/artikel/edit',
      'artikel' => $artikel,
      'kategori' => $kategori,
      'page' => 'artikel',
    ]);
  }

  // public function deleteImage()
  // {
  //   header('Content-Type: application/json');

  //   try{
  //     // Ambil json body
  //     $data = json_decode(file_get_contents('php://input'), true);
  //     if(empty($data['image_id'])){
  //       throw new Exception('Image ID tidak dikirim');
  //     }

  //     $imageId = (int) $data['image_id'];

  //     // ambil image via model
  //     $image = $this->artikelModel->getImageById($imageId);
  //     if(!$image) throw new Exception('Gambar tidak ditemukan');

  //     // hapus file fisik
  //     $filePath = __DIR__ . '/../../../public/' . $image['path'];
  //     if(file_exists($filePath)) unlink($filePath);

  //     // hapus record db via model
  //     $this->artikelModel->deleteImageById($imageId);
  //     echo json_encode(['success' => true]);
  //   }catch(Exception $e){
  //     echo json_encode(['success' => false, 'message' => $e->getMessage()]);
  //   }
  // }

  public function update($id)
  {
    $uploadBase = __DIR__ . '/../../../public/assets/img/uploads/';
    $tmpDir     = $uploadBase . 'tmp/';
    $baseUrl    = Config::get('base_url');

    try {
      // --- Cek CSRF Token ---
      if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        throw new \Exception('CSRF token tidak valid!');
      }

      // Ambil artikel
      $artikel = $this->artikelModel->getById($id);
      if (!$artikel) throw new \Exception('Artikel tidak ditemukan.');

      $validator = new Validator($_POST);
      $validator->required(['judul','isi','kategori_id','status'])
                ->maxLength('judul', 255)
                ->minLength('isi', 10)
                ->inList('status', ['draft','publish','archived'])
                ->image('thumbnail')
                ->multipleImages('new_images');

      if ($validator->hasErrors()) {
        $errors = $validator->getErrors();
        $messages = implode('<br>', array_map(fn($e) => implode(', ', $e), $errors));
        throw new Exception($messages);
        // throw new \Exception(implode(', ', array_merge(...array_values($validator->getErrors()))));
      }

      // ====== SANITASI INPUT ======
      $judul = $validator->sanitize('judul');
      $kategoriId = $_POST['kategori_id'];
      $status     = $_POST['status'];
      $tanggalPosting = $_POST['tanggal_posting'];

      $isi = $_POST['isi'];

      // --- Buat folder artikel jika belum ada ---
      $artikelFolder = $uploadBase . "artikel_{$id}/";
      if (!is_dir($artikelFolder)) mkdir($artikelFolder, 0777, true);

      // --- Mulai transaksi ---
      $this->db->beginTransaction();

      // --- Tangkap semua tag <img> di isi ---
      preg_match_all('/<img.*?src=["\'](.*?)["\']/', $isi, $matches);
      $currentImages = $matches[1] ?? [];

      // ambil daftar gambar tambahan dari DB (bukan thumbnail)
      $extraImages = $this->artikelModel->getImageById($id);
      $protectedFiles = [];
      foreach($extraImages as $img){
        $protectedFiles[] = basename($img['path']);
      }

      // hapus file lama yang tidak muncul di isi baru
      $existingFiles = glob($artikelFolder . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
      foreach($existingFiles as $filePath){
        $fileName = basename($filePath);
        $relPath = "assets/img/uploads/artikel_{$id}/$fileName";
        $foundIsi = false;

        foreach($currentImages as $src){
          if(strpos($src, $relPath) !== false || strpos($src, $fileName) !== false){
            $foundIsi = true;
            break;
          }
        }

        if(!$foundIsi && !in_array($fileName, $protectedFiles)){
          unlink($filePath); // hapus file yang sudah tidak digunakan
        }
      }
      
      // --- Pindahkan file baru dari tmp ke folder artikel ---
      foreach ($currentImages as $src) {
        // Abaikan jika sudah absolute URL
        if (strpos($src, 'http') === 0) continue;

        $fileName = basename($src);

        // Path tmp
        $tmpPath = $tmpDir . $fileName;
        $finalPath = $artikelFolder . $fileName;

        // Pindahkan file jika ada di tmp
        if (file_exists($tmpPath)) {
          rename($tmpPath, $finalPath);
        }

        // Path relatif + path publik untuk HTML
        $newRelPath = "assets/img/uploads/artikel_{$id}/$fileName";
        $publicPath = $baseUrl . '/' . $newRelPath;

        // Ganti semua src di isi
        $isi = str_replace($src, $publicPath, $isi);
      }

      // --- Update data artikel utama ---
      $this->artikelModel->update($id, [
        'judul' => $judul,
        'kategori_id' => $kategoriId,
        'isi' => $isi,
        'status' => $status,
        'tanggal_posting' => $tanggalPosting,
      ]);

      // --- Upload thumbnail ---
      $thumb = null;
      foreach($artikel['images'] as $img){
        if($img['is_thumbnail']){
          $thumb = $img;
          break;
        }
      }

      if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        if ($thumb) {
          // Update thumbnail lama
          $this->artikelModel->uploadUpdateImage($thumb['id'], $artikel['id'], $_FILES['thumbnail'], true, $artikelFolder);
        } else {
          // Upload thumbnail baru (tidak ada record sebelumnya)
          $this->artikelModel->uploadImage($artikel['id'], [
              'name' => $_FILES['thumbnail']['name'],
              'tmp_name' => $_FILES['thumbnail']['tmp_name'],
              'size' => $_FILES['thumbnail']['size'],
              'error' => $_FILES['thumbnail']['error'],
              'caption' => $_POST['thumbnail_caption'] ?? null,
          ], true, $artikelFolder);
        }
       }


      // hapus gambar lama yang dihapus
      $deletedJson = $_POST['deleted_image_ids'][0] ?? '[]';
      $deleted = json_decode($deletedJson, true);
      if(!empty($deleted)){
        foreach($deleted as $imageId){
          $this->artikelModel->deleteImageById($imageId);
        }
      }
      
      // update caption gambar lama
      $oldIds = $_POST['old_image_id'] ?? [];
      $oldCaptions = $_POST['old_captions'] ?? [];
      foreach($oldIds as $i => $id){
        $caption = $oldCaptions[$i] ?? null;
        $this->artikelModel->updateImageCaption($id, $caption);
      }
      
      // --- Upload multiple images tambahan + caption ---
      if (isset($_FILES['new_images']) && !empty($_FILES['new_images']['name'][0])) {
        $captions = $_POST['captions'] ?? [];
        $images = $_FILES['new_images'];
        foreach($images['name'] as $i => $name){
          if($images['error'][$i] !== UPLOAD_ERR_OK) continue;

          $file = [
            'name' => $images['name'][$i],
            'tmp_name' => $images['tmp_name'][$i],
            'caption' => $captions[$i] ?? null,
          ];

          $this->artikelModel->uploadImage($artikel['id'], $file, false, $artikelFolder);          
        }
      }

      // --- Commit transaksi ---
      $this->db->commit();

      // --- Bersihkan folder tmp ---
      $this->clearTmpFolder($tmpDir);

      FlashMessage::set('success', 'Artikel berhasil diperbaharui!');
      header("Location: " . Config::get('base_url') . "/admin/artikel/detail/" . $artikel['id']);
      exit;

    } catch (\Exception $e) {
      // rollback transaksi jika error
      if ($this->db->inTransaction()) {
        $this->db->rollBack();
      }

      // simpan input user sementara
      $_SESSION['old'] = $_POST;
      
      FlashMessage::set('error', $e->getMessage());
      header("Location: " . Config::get('base_url') . "/admin/artikel/edit/" . $artikel['id']);
      exit;
    }finally{
      $this->clearTmpFolder($tmpDir);
    }
  }

  public function delete($id)
  {
    try{
      $artikel = $this->artikelModel->getById($id);
      if(!$artikel) throw new Exception('Artikel tidak ditemukan.');

      // path folder artikel
      $artikelFolder = __DIR__ . "/../../../public/assets/img/uploads/artikel_{$id}/";

      // hapus folder dan isinya
      if(is_dir($artikelFolder)){
        FileHelper::deleteDir($artikelFolder);
      }

      // hapus gambar di DB
      $this->artikelModel->deleteImagesByArtikelId($id);

      // hapus artikel utama
      $this->artikelModel->delete($id);

      FlashMessage::set('success', 'Artikel berhasil dihapus.');
    }catch(Exception $e){
      FlashMessage::set('error', $e->getMessage());
    }

    header("Location:" . Config::get('base_url') . '/admin/artikel');
    exit;
  }

  public function searchSuggest()
  {
    $search = $_GET['search'] ?? '';
    $kategoriId = $_GET['kategori_id'] ?? '';

    $results = $this->artikelModel->searchSuggest($search, $kategoriId);

    header('Content-Type: application/json');
    echo json_encode($results);
    exit;
  }
}