<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Config;
use App\Core\FlashMessage;
use App\Services\AuthService;

class AuthController extends Controller{
  private $authService;

  public function __construct()
  {
    $this->authService = new AuthService();
  }

  /**
   * tampilkan form login
   */
  public function showLogin(){
    $this->view('layouts/public_main', [
      'title' => 'Login Form',
      'content' => 'auth/login',
    ]);
  }

  /**
   * proses login
   */
  public function loginProcess()
  {
    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
      header("Location:" . Config::get('base_url') . "auth/login");
      exit;
    }
    
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if(empty($username) || empty($password)){
      FlashMessage::set('error', 'Username atau password wajib diisi');
      header("Location:" . Config::get('base_url') . "auth/login");
      exit;
    }

    $result = $this->authService->login($username, $password);

    if($result['success']){
      FlashMessage::set('success', $result['message']);
      header("Location:" . $result['redirect']);
      exit;
    }else{
      FlashMessage::set('error', $result['message']);
      header("Location: " . Config::get('base_url') . "/auth/login");
      exit;
    }
  }

  /**
   * logout user
   */
  public function logout()
  {
    $this->authService->logout();
    FlashMessage::set('success', 'Anda telah logout.');
    header("Location: " . Config::get('base_url') . "/auth/login");
    exit;
  }
}