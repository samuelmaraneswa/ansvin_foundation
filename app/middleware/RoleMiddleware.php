<?php
namespace App\Middleware;

use App\Core\Config;

class RoleMiddleware
{
  public static function handle(array $roles = []): void
  {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user = $_SESSION['user'] ?? null;

    // Jika belum login atau tidak punya role
    if (!$user || empty($user['role'])) {
        header("Location: " . Config::get('base_url') . "/auth/login");
        exit;
    }

    // Jika role user tidak termasuk dalam daftar allowed roles
    if (!in_array($user['role'], $roles)) {
        http_response_code(403);
        die("Akses ditolak. Anda tidak memiliki izin untuk membuka halaman ini.");
    }
  }
}
