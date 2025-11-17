<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Helpers\FileValidator;
use App\Core\Config;

class UploadController extends Controller
{
  public function imageTemp()
  {
    header('Content-Type: application/json'); 

    if (!isset($_FILES['file'])) {
      http_response_code(400);
      echo json_encode(['error' => 'Tidak ada file yang dikirim']);
      exit;
    }

    $file = $_FILES['file'];
    $errors = FileValidator::checkImage($file);
    if (!empty($errors)) {
      http_response_code(400);
      echo json_encode(['error' => implode(", ", $errors)]);
      exit;
    }

    $targetDir = __DIR__ . '/../../../public/assets/img/uploads/tmp/';
    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

    $fileName = uniqid() . "_" . basename($file['name']);
    $targetPath = $targetDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
      echo json_encode([
        'location' => Config::get('base_url') . '/assets/img/uploads/tmp/' . $fileName,
        'tmp_name' => $fileName,
        'user_id' => $_SESSION['user']['id'] ?? null // tambahan debug
      ]);
    } else {
      http_response_code(500);
      echo json_encode(['error' => 'Upload gagal']);
    }
  }
}