<?php
namespace App\Middleware;

class Middleware
{
  public static function handle(array $middlewares): void
  {
    foreach ($middlewares as $middleware) {
      switch ($middleware) {
        // cek login umum
        case 'auth':
          \App\Middleware\AuthMiddleware::handle();
          break;

        // akses ke dashboard admin (super_admin & admin_unit)
        case 'admin':
          \App\Middleware\RoleMiddleware::handle(['super_admin', 'admin_unit']);
          break;

        // akses khusus guru
        case 'guru':
          \App\Middleware\RoleMiddleware::handle(['guru']);
          break;

        // akses khusus pegawai non-guru
        case 'pegawai':
          \App\Middleware\RoleMiddleware::handle(['pegawai']);
          break;

        // akses khusus siswa
        case 'siswa':
          \App\Middleware\RoleMiddleware::handle(['siswa']);
          break;

        default:
          throw new \Exception("Middleware $middleware tidak ditemukan");
      }
    }
  }
}
