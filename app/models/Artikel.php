<?php
namespace App\Models;

use App\Core\Database; 
use PDO;
use Exception;
use App\Helpers\FileValidator;

class Artikel {
  private PDO $db;

  public function __construct()
  {
    $this->db = Database::connect();
  }

  // Ambil semua artikel
  public function getAll()
  {
    $sql = "
      SELECT a.*, ai.path AS thumbnail, ka.nama AS kategori_nama
      FROM artikel a
      LEFT JOIN artikel_images ai
        ON ai.artikel_id = a.id AND ai.is_thumbnail = 1
      LEFT JOIN kategori_artikel ka
        ON ka.id = a.kategori_id
      ORDER BY a.tanggal_posting DESC
    ";

    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  // Ambil 1 artikel
  public function getById($id)
  {
    $stmt = $this->db->prepare("
      SELECT a.*, ka.nama AS kategori_nama
      FROM artikel a
      LEFT JOIN kategori_artikel ka ON ka.id = a.kategori_id
      WHERE a.id = :id
      LIMIT 1
    ");
    $stmt->execute(['id' => $id]);
    $artikel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$artikel) return false;

    // Ambil semua gambar tambahan + caption
    $stmtImg = $this->db->prepare("
      SELECT id, path, caption, is_thumbnail 
      FROM artikel_images 
      WHERE artikel_id = :id
    ");
    $stmtImg->execute(['id' => $id]);
    $artikel['images'] = $stmtImg->fetchAll(PDO::FETCH_ASSOC);

    return $artikel;
  }

  // Insert artikel baru
  public function insert(array $data): int
  {

    $query = "INSERT INTO artikel 
      (judul, kategori_id, isi, author_id, unit_id, status, tanggal_posting, created_at, updated_at)
      VALUES 
      (:judul, :kategori_id, :isi, :author_id, :unit_id, :status, :tanggal_posting, NOW(), NOW())";

    $stmt = $this->db->prepare($query);
    $stmt->execute([
      ':judul' => $data['judul'],
      ':kategori_id' => $data['kategori_id'] ?? null,
      ':isi' => $data['isi'],
      ':author_id' => $data['author_id'],
      ':unit_id' => $data['unit_id'],
      ':status' => $data['status'] ?? 'draft',
      ':tanggal_posting' => $data['tanggal_posting'] ?? date('Y-m-d H:i:s'),
    ]);
 
    $artikelId = $this->db->lastInsertId();

    // Gambar di TinyMCE tidak disimpan ke DB — hanya dibiarkan di folder uploads/artikel_ID/
    
    return $artikelId;
  }

  /**
   * Upload satu file gambar
   * @param int $artikelId
   * @param array $file ($_FILES)
   * @param bool $isThumbnail
   * @param string|null $targetDir
   * @param bool $isInline true = gambar dari TinyMCE, tidak disimpan di DB
   */
  public function uploadImage(int $artikelId, array $file, bool $isThumbnail = false, ?string $targetDir = null)
  {
    $uploadDir = $targetDir ?? (__DIR__ . '/../../public/assets/img/uploads/');
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = uniqid('img_') . '.' . $ext;
    $path = $uploadDir . $newName;

    if (move_uploaded_file($file['tmp_name'], $path)) {

      // Simpan ke tabel artikel_images (versi full)
      $stmt = $this->db->prepare("INSERT INTO artikel_images 
        (artikel_id, path, caption, is_thumbnail, created_at) 
        VALUES (:artikel_id, :path, :caption, :is_thumbnail, NOW())");
      $stmt->execute([
        ':artikel_id' => $artikelId,
        ':path' => "assets/img/uploads/artikel_{$artikelId}/$newName",
        ':caption' => $file['caption'] ?? null,
        ':is_thumbnail' => $isThumbnail ? 1 : 0
      ]);

      // 🔹 Jika ini gambar utama (thumbnail artikel), buat versi kecil (400px)
      if ($isThumbnail) {
        $thumbName = 'thumb_' . $newName;
        $thumbPath = $uploadDir . $thumbName;

        // ✅ Tambah thumbnail
        $this->makeThumbnail($path, $thumbPath, 650);
      }
    }
  }

  public function uploadUpdateImage(int $imageId, int $artikelId, array $file, bool $isThumbnail = false, ?string $targetDir = null)
  {
    $uploadDir = $targetDir ?? (__DIR__ . '/../../public/assets/img/uploads/artikel_' . $artikelId . '/');
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    // Ambil data gambar lama
    $stmtOld = $this->db->prepare("SELECT path FROM artikel_images WHERE id = :id LIMIT 1");
    $stmtOld->execute([':id' => $imageId]);
    $oldImage = $stmtOld->fetch(PDO::FETCH_ASSOC);

    $newPath = $oldImage['path']; // default: path lama
    $hasNewFile = isset($file['tmp_name']) && !empty($file['tmp_name']);

    // Jika ada file baru, pindahkan ke folder dan hapus lama
    if ($hasNewFile && file_exists($file['tmp_name'])) {
      // Hapus file lama (termasuk thumbnail WebP versi lama jika ada)
      if ($oldImage && file_exists(__DIR__ . '/../../public/' . $oldImage['path'])) {
        unlink(__DIR__ . '/../../public/' . $oldImage['path']);

        // hapus thumbnail kecil lama
        $thumbOld = preg_replace('/\/img_/', '/thumb_img_', $oldImage['path']);
        if (file_exists(__DIR__ . '/../../public/' . $thumbOld)) unlink(__DIR__ . '/../../public/' . $thumbOld);

        // hapus WebP lama
        $webpOld = preg_replace('/\.\w+$/', '.webp', $oldImage['path']);
        if (file_exists(__DIR__ . '/../../public/' . $webpOld)) unlink(__DIR__ . '/../../public/' . $webpOld);
      }

      $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
      $newName = uniqid('img_') . '.' . $ext;
      $finalPath = $uploadDir . $newName;

      if (move_uploaded_file($file['tmp_name'], $finalPath)) {
        $newPath = "assets/img/uploads/artikel_{$artikelId}/$newName";
        // 🔹 Jika ini thumbnail, buat versi kecil 650px
        if ($isThumbnail) {
            $thumbPath = $uploadDir . 'thumb_' . $newName;
            $this->makeThumbnail($finalPath, $thumbPath, 650);
            // 🔹 makeThumbnail juga otomatis membuat WebP versi kecil
        }
      }
    }

    // Update record di DB
    $stmt = $this->db->prepare("UPDATE artikel_images 
      SET path = :path, caption = :caption, is_thumbnail = :is_thumbnail 
      WHERE id = :id");
    $stmt->execute([
      ':path' => $newPath,
      ':caption' => $file['caption'] ?? null,
      ':is_thumbnail' => $isThumbnail ? 1 : 0,
      ':id' => $imageId
    ]);
  }

  // Update artikel
  public function update(int $id, array $data): bool
  {
    $fields = [];
    $params = [':id' => $id];

    foreach ($data as $key => $value) {
      $fields[] = "$key = :$key";
      $params[":$key"] = $value;
    }

    $sql = "UPDATE artikel SET " . implode(', ', $fields) . " WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute($params);
  }

  private function makeThumbnail(string $sourcePath, string $targetPath, int $width = 650): void
  {
    if (!file_exists($sourcePath)) return;

    [$origWidth, $origHeight, $type] = getimagesize($sourcePath);
    $ratio = $origHeight / $origWidth;
    $height = $width * $ratio;

    // 🔹 Buat image resource berdasarkan tipe file
    switch ($type) {
      case IMAGETYPE_JPEG:
        $srcImg = imagecreatefromjpeg($sourcePath);
        break;
      case IMAGETYPE_PNG:
        $srcImg = imagecreatefrompng($sourcePath);
        imagepalettetotruecolor($srcImg);
        imagealphablending($srcImg, true);
        imagesavealpha($srcImg, true);
        break;
      case IMAGETYPE_WEBP:
        $srcImg = imagecreatefromwebp($sourcePath);
        break;
      default:
        return; // jika format tidak dikenal
    }

    $thumbImg = imagecreatetruecolor($width, $height);

    // 🔹 Jika PNG/WebP: dukung transparansi
    if (in_array($type, [IMAGETYPE_PNG, IMAGETYPE_WEBP])) {
      imagealphablending($thumbImg, false);
      imagesavealpha($thumbImg, true);
    }

    imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);

    // 🔹 Simpan thumbnail sesuai tipe aslinya
    switch ($type) {
      case IMAGETYPE_JPEG:
        imagejpeg($thumbImg, $targetPath, 85);
        break;
      case IMAGETYPE_PNG:
        imagepng($thumbImg, $targetPath, 6);
        break;
      case IMAGETYPE_WEBP:
        imagewebp($thumbImg, $targetPath, 80);
        break;
    }

    imagedestroy($srcImg);
    imagedestroy($thumbImg);

    // 🔹 Optional: buat versi WebP juga (untuk browser modern)
    if (!str_ends_with($targetPath, '.webp')) {
      $webpPath = preg_replace('/\.\w+$/', '.webp', $targetPath);
      imagewebp($thumbImg, $webpPath, 80);
    }
  }

  public function getImageById(int $id){
    $stmt = $this->db->prepare("select * from artikel_images where artikel_id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function deleteImageById(int $id): bool
  {
    // Ambil path file dari DB
    $stmt = $this->db->prepare("SELECT path FROM artikel_images WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($image && !empty($image['path'])) {
      $filePath = __DIR__ . '/../../public/' . $image['path'];

      // Hapus file fisik jika ada
      if (file_exists($filePath)) {
        unlink($filePath);
      }else {
        error_log("File tidak ditemukan: " . $filePath);
      }
    }

    $stmtDel = $this->db->prepare("delete from artikel_images where id = :id");
    return $stmtDel->execute([':id' => $id]);
  }

  public function updateImageCaption(int $imageId, ?string $caption): bool
  {
    $stmt = $this->db->prepare("update artikel_images set caption = :caption where id = :id");
    return $stmt->execute([':caption' => $caption, ':id' => $imageId]);
  }

  public function delete($id){
    $stmt = $this->db->prepare("delete from artikel where id = :id");
    $stmt->execute([':id' => $id]);
  }

  public function deleteImagesByArtikelId($artikelId){
    $stmt = $this->db->prepare("delete from artikel_images where artikel_id = :id");
    $stmt->execute([':id' => $artikelId]);
  }

  public function searchSuggest($keyword, $kategoriId = null)
  {
    $sql = "SELECT a.judul, k.nama AS kategori
            FROM artikel a
            LEFT JOIN kategori_artikel k ON a.kategori_id = k.id
            WHERE a.judul LIKE :keyword";

    $params = [':keyword' => "%$keyword%"];

    if (!empty($kategoriId)) {
      $sql .= " AND a.kategori_id = :kategori";
      $params[':kategori'] = $kategoriId;
    }

    $sql .= " ORDER BY a.tanggal_posting DESC LIMIT 10";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function getFiltered($search = '', $kategori_id = '', $limit = 5, $offset = 0)
  {
    $sql = "
      SELECT a.*, ai.path AS thumbnail, k.nama AS kategori_nama
      FROM artikel a
      LEFT JOIN artikel_images ai
        ON ai.artikel_id = a.id AND ai.is_thumbnail = 1
      LEFT JOIN kategori_artikel k
        ON a.kategori_id = k.id
      WHERE 1=1
    ";

    $params = [];

    if (!empty($search)) {
      $sql .= " AND a.judul LIKE :search";
      $params[':search'] = "%{$search}%";
    }

    if (!empty($kategori_id)) {
      $sql .= " AND a.kategori_id = :kategori_id";
      $params[':kategori_id'] = $kategori_id;
    }

    $sql .= " ORDER BY a.tanggal_posting DESC LIMIT :limit OFFSET :offset";

    $stmt = $this->db->prepare($sql);
    foreach($params as $key => $val){
      $stmt->bindValue($key, $val);
    }
    
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  public function countFiltered($search = '', $kategori_id = '')
  {
    $sql = "SELECT COUNT(*) FROM artikel WHERE 1=1";
    $params = [];

    if (!empty($search)) {
      $sql .= " AND judul LIKE :search";
      $params[':search'] = "%{$search}%";
    }

    if (!empty($kategori_id)) {
      $sql .= " AND kategori_id = :kategori_id";
      $params[':kategori_id'] = $kategori_id;
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
  }

  // public function getByCategories(array $slugs, int $limit = 3)
  // {
  //   $placeholders = implode(',', array_fill(0, count($slugs), '?'));
  //   $query = "
  //       SELECT a.*, ai.path AS thumbnail
  //       FROM artikel a
  //       LEFT JOIN artikel_images ai
  //           ON ai.artikel_id = a.id AND ai.is_thumbnail = 1
  //       JOIN kategori_artikel k
  //           ON k.id = a.kategori_id
  //       WHERE a.status = 'publish' AND k.slug IN ($placeholders)
  //       ORDER BY a.tanggal_posting DESC
  //       LIMIT $limit
  //   ";
  //   $stmt = $this->db->prepare($query);
  //   $stmt->execute($slugs); // hanya positional parameter
  //   return $stmt->fetchAll(PDO::FETCH_ASSOC);
  // }

  // public function getByCategories(array $slugs, int $limit = 3)
  // {
  //   if (empty($slugs)) return [];

  //   // Buat named placeholders dinamis
  //   $params = [];
  //   $placeholders = [];

  //   foreach ($slugs as $i => $slug) {
  //       $key = ":slug$i";
  //       $placeholders[] = $key;
  //       $params[$key] = $slug;
  //   }

  //   $query = "
  //       SELECT 
  //           a.id, 
  //           a.judul, 
  //           a.tanggal_posting,
  //           SUBSTRING(a.isi, 1, 150) AS excerpt,
  //           ai.path AS thumbnail,
  //           k.slug AS kategori_slug
  //       FROM artikel a
  //       JOIN kategori_artikel k ON k.id = a.kategori_id
  //       LEFT JOIN artikel_images ai 
  //           ON ai.artikel_id = a.id AND ai.is_thumbnail = 1
  //       WHERE a.status = 'publish' 
  //         AND k.slug IN (" . implode(',', $placeholders) . ")
  //       ORDER BY a.tanggal_posting DESC
  //       LIMIT :limit
  //   ";

  //   $stmt = $this->db->prepare($query);

  //   // Bind slug satu per satu
  //   foreach ($params as $key => $value) {
  //       $stmt->bindValue($key, $value, PDO::PARAM_STR);
  //   }

  //   // Bind limit (harus integer, tidak bisa langsung di query)
  //   $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

  //   $stmt->execute();

  //   return $stmt->fetchAll(PDO::FETCH_ASSOC);
  // }

  // public function getExceptCategories(array $excludedSlugs, int $limit = 6)
  // {
  //   // Buat placeholder dinamis untuk excluded slug
  //   $placeholders = [];
  //   $params = [];

  //   foreach ($excludedSlugs as $i => $slug) {
  //       $key = ":slug{$i}";
  //       $placeholders[] = $key;
  //       $params[$key] = $slug;
  //   }

  //   // Query ambil artikel dengan kategori selain slug tertentu
  //   $query = "
  //       SELECT 
  //           a.id, 
  //           a.judul, 
  //           a.tanggal_posting,
  //           SUBSTRING(a.isi, 1, 150) AS excerpt,
  //           ai.path AS thumbnail,
  //           k.slug AS kategori_slug
  //       FROM artikel a
  //       JOIN kategori_artikel k ON k.id = a.kategori_id
  //       LEFT JOIN artikel_images ai 
  //           ON ai.artikel_id = a.id AND ai.is_thumbnail = 1
  //       WHERE a.status = 'publish' 
  //         AND k.slug NOT IN (" . implode(',', $placeholders) . ")
  //       ORDER BY a.tanggal_posting DESC
  //       LIMIT $limit
  //   ";

  //   $stmt = $this->db->prepare($query);
  //   foreach ($params as $key => $value) {
  //       $stmt->bindValue($key, $value, PDO::PARAM_STR);
  //   }

  //   $stmt->execute();
  //   return $stmt->fetchAll(PDO::FETCH_ASSOC);
  // }

  public function getArticlesByCategory(array $slugs = [], int $limit = 6, bool $exclude = false)
  {
    // Jika slug kosong dan bukan mode exclude → ambil semua artikel publish
    if (empty($slugs) && !$exclude) {
        $query = "
            SELECT 
                a.id, 
                a.judul, 
                a.tanggal_posting,
                SUBSTRING(a.isi, 1, 150) AS excerpt,
                ai.path AS thumbnail,
                k.slug AS kategori_slug
            FROM artikel a
            JOIN kategori_artikel k ON k.id = a.kategori_id
            LEFT JOIN artikel_images ai 
                ON ai.artikel_id = a.id AND ai.is_thumbnail = 1
            WHERE a.status = 'publish'
            ORDER BY a.tanggal_posting DESC
            LIMIT :limit
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buat placeholder dinamis
    $placeholders = [];
    $params = [];

    foreach ($slugs as $i => $slug) {
        $key = ":slug{$i}";
        $placeholders[] = $key;
        $params[$key] = $slug;
    }

    // Tentukan operator berdasarkan mode
    $operator = $exclude ? 'NOT IN' : 'IN';

    // Query utama
    $query = "
        SELECT 
            a.id, 
            a.judul, 
            a.tanggal_posting,
            SUBSTRING(a.isi, 1, 150) AS excerpt,
            ai.path AS thumbnail,
            k.slug AS kategori_slug
        FROM artikel a
        JOIN kategori_artikel k ON k.id = a.kategori_id
        LEFT JOIN artikel_images ai 
            ON ai.artikel_id = a.id AND ai.is_thumbnail = 1
        WHERE a.status = 'publish' 
          AND k.slug $operator (" . implode(',', $placeholders) . ")
        ORDER BY a.tanggal_posting DESC
        LIMIT :limit
    ";

    $stmt = $this->db->prepare($query);

    // Bind slug & limit
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

}
