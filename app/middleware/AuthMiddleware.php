<?php
namespace App\Middleware;

use App\Core\Config;

class AuthMiddleware
{
  public static function handle(): void
  {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Jika belum login
    if (empty($_SESSION['user'])) {
        header("Location: " . Config::get('base_url') . "/auth/login");
        exit;
    }
  }
}
