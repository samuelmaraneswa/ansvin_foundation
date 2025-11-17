<?php
namespace App\Core;

class App{
  private object $controller;
  private string $method = 'index';
  private array $params = [];

  public function __construct()
  {
    $url = $this->parseURL();
    
    // 1. tentukan controler
    $controllerName = isset($url[0]) ? ucfirst($url[0]) . 'Controller' : 'HomeController';
    $controllerClass = "App\\Controllers\\$controllerName";

    if(class_exists($controllerClass)){
      $this->controller = new $controllerClass();
      unset($url[0]);
    }else{
      throw new \Exception("Controller '$controllerClass' tidak ditemukan.");
    }

    // 2. tentukan method
    if(isset($url[1]) && method_exists($this->controller, $url[1])) {
      $this->method = $url[1];
      unset($url[1]);
    }

    // 3. ambil parameter url
    $this->params = $url ? array_values($url) : [];

    // 4. jalankan controller & method
    call_user_func_array([$this->controller, $this->method], $this->params);
  }

  /**
   * memecah url menjadi array
   */
  private function parseURL(): array
  {
    if(isset($_GET['url'])){
      $url = trim($_GET['url'], '/');
      $url = filter_var($url, FILTER_SANITIZE_URL);
      return explode('/', $url);
    }
    return [];
  }
}