<?php
namespace App\Core;

use App\Middleware\Middleware;

abstract class AdminController extends Controller{
  protected array $sharedData = [];
  protected ?array $user = null;

  public function __construct()
  {
    Middleware::handle(['auth', 'admin']);

    // Simpan user login ke properti
    $this->user = Auth::user();

    // Bisa juga langsung masukkan ke shared data untuk dikirim otomatis ke semua view
    $this->sharedData['user'] = $this->user;
  }

  public function view(string $view, array $data = []): void
  {
    $data['user'] = $this->user;
    
    // Simpan data untuk dipakai di semua include
    $this->sharedData = array_merge($this->sharedData, $data);

    // Jalankan view lewat includeView supaya tetap dalam konteks $this
    $this->includeView($view);
  }

  // Helper baru untuk include view tambahan dengan data yang sama
  protected function includeView(string $path)
  {
    $fullPath = "../app/views/" . $path . ".php";
    if (file_exists($fullPath)) {
        extract($this->sharedData);
        include $fullPath;
    } else {
        echo "View tidak ditemukan: $fullPath";
    }
  }
}