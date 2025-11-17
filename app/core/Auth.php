<?php
namespace App\Core;

class Auth
{
  public static function check(): bool
  {
    return isset($_SESSION['user']);
  }

  public static function user(): ?array
  {
    return $_SESSION['user'] ?? null;
  }

  public static function id(){
    return $_SESSION['user']['id'] ?? null;
  }

  public static function role(){
    return $_SESSION['user']['role'] ?? null;
  }

  public static function hasRole($roles = []){
    if(!self::check()) return false;
    return in_array($_SESSION['user']['role'], $roles);
  }

  public static function login(array $user): void
  {
    $_SESSION['user'] = [
      'id'       => $user['id'],
      'username' => $user['nip'] ?? $user['nis'],  // identitas login
      'name'     => $user['nama_pegawai'] ?? $user['nama_siswa'], // nama lengkap
      'role'     => $user['role'],
      'foto'     => $user['foto'],
      'unit_id'  => $user['unit_id'] ?? null,
    ];
    
     // CSRF token untuk session user
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    session_regenerate_id(true);
  }

  public static function logout(): void
  {
    unset($_SESSION['user']);
    session_destroy();
  }

  public static function allowRoles(array $roles = []) {
    if (!in_array(Auth::role(), $roles)) {
        http_response_code(403);
        die("Akses ditolak. Anda tidak memiliki izin ke halaman ini.");
    }
  }
}
