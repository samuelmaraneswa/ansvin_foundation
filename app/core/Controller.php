<?php
namespace App\Core;

class Controller{
  /**
   * memanggil model tertentu
   */
  protected function model(string $model)
  {
    $class = "App\\Models\\$model";
    if(class_exists($class)){
      return new $class;
    }
    throw new \Exception("Model '$model' tidak ditemukan.");
  }

  /**
   * menampilkan view
   */
  protected function view(string $view, array $data = []): void
  {
    $viewPath = "../app/views/" . $view . ".php";
    $headerPath = "../app/views/layouts/header.php"; 
    $footerPath = "../app/views/layouts/footer.php";

    if(!file_exists($viewPath)){
      throw new \Exception("View '$view' tidak ditemukan di $viewPath");
    }

    extract($data);
    if(file_exists($headerPath)) require_once $headerPath;
    require_once $viewPath;
    if(file_exists($footerPath)) require_once $footerPath;
  }

  /**
   * Menampilkan view tanpa header/footer (opsional)
   */
  protected function viewRaw(string $view, array $data = []): void
  {
      $viewPath = "../app/views/" . $view . ".php";
      if (!file_exists($viewPath)) {
          throw new \Exception("View '$view' tidak ditemukan.");
      }

      extract($data);
      require $viewPath;
  }
}