<?php
namespace App\Services;

use App\Core\Auth;
use App\Core\Config;
use App\Models\UsersPegawaiModel;
use App\Models\UsersSiswaModel;

class AuthService{
  private $pegawaiModel;
  private $siswaModel;

  public function __construct()
  {
    $this->pegawaiModel = new UsersPegawaiModel;
    $this->siswaModel = new UsersSiswaModel;
  }

  /**
   * proses login lintas tabel(pegawai/siswa)
   */
  public function login(string $username, string $password): array
  {
    // coba login pegawai(super_admin, admin_unit, guru, pegawai)
    $user = $this->pegawaiModel->findByNIP($username);

    if($user){
      // username ditemukan di tabel pegawai
      if(password_verify($password, $user['password'])){
        $user['role'] = $user['role'] ?? 'pegawai';
        Auth::login($user);

        return [
          'success' => true,
          'redirect' => $this->redirectByRole($user['role']),
          'message' => 'Login berhasil sebagai ' . $user['role']
        ];
      }

      // username ditemukan tapi password salah
      return [
        'success' => false,
        'message' => 'Username atau password salah.'
      ];
    }

    // coba login sebagai siswa
    $user = $this->siswaModel->findByNIS($username);
    if ($user) {
      if (password_verify($password, $user['password'])) {
        $user['role'] = 'siswa';
        Auth::login($user);

        return [
          'success' => true,
          'redirect' => $this->redirectByRole('siswa'),
          'message' => 'Login berhasil sebagai siswa'
        ];
      }

      return [
        'success' => false,
        'message' => 'Password salah untuk akun siswa.'
      ];
    }

    // 🔹 3. Jika username tidak ditemukan di kedua tabel
    return [
      'success' => false,
      'message' => 'Username tidak ditemukan.'
    ];
  }

  /**
   * logout user
   */
  public function logout(): void
  {
    Auth::logout();
  }

  /**
   * tentukan redirect berdasarkan role
   */
  private function redirectByRole(string $role): string
  {
    $base = Config::get('base_url');
    switch($role){
      case 'super_admin':
      case 'admin_unit':
        return "$base/admin/dashboard";
      case 'guru':
        return "$base/guru/dashboard";
      case 'pegawai':
        return "$base/pegawai/dashboard";
      case 'siswa':
        return "$base/siswa";
      default:
        return "$base/";
    }
  }
}