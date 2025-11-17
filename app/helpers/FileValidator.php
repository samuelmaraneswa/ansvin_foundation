<?php
namespace App\Helpers;

class FileValidator {
  public static function checkImage(?array $file, int $maxSize = 6291456): array {
    $errors = [];

    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
      $errors[] = "Terjadi kesalahan saat upload file.";
      return $errors;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExt = ['jpg','jpeg','png','gif','webp'];
    if (!in_array($ext, $allowedExt)) {
      $errors[] = "Format file tidak diperbolehkan! (JPG, PNG, GIF, WEBP)";
    }

    if ($file['size'] > $maxSize) {
      $errors[] = "Ukuran file melebihi " . ($maxSize / 1024 / 1024) . "MB!";
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMime = ['image/jpeg','image/png','image/gif','image/webp'];

    // fallback dengan getimagesize
    if (!in_array($mime, $allowedMime)) {
      $imgInfo = @getimagesize($file['tmp_name']);
      if ($imgInfo && isset($imgInfo['mime'])) {
        $mime = $imgInfo['mime'];
      }
    }
    
    if (!in_array($mime, $allowedMime)) {
      $errors[] = "File bukan gambar yang valid!";
    }

    return $errors;
  }
}
