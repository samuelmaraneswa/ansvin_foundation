<?php
namespace App\Core;

use App\Middleware\Middleware;

class Route{
  private static array $routes = [];

  // =======================
  // MENDAFTARKAN ROUTE
  // =======================
  public static function get(string $path, string $action, array $middlewares = []): void
  {
    self::$routes['GET'][$path] = ['action' => $action, 'middlewares' => $middlewares];
  }

  public static function post(string $path, string $action, array $middlewares = []): void
  {
    self::$routes['POST'][$path] = ['action' => $action, 'middlewares' => $middlewares];
  }

  public static function delete(string $path, string $action, array $middlewares = []): void
  {
    self::$routes['DELETE'][$path] = ['action' => $action, 'middlewares' => $middlewares];
  }

  // =======================
  // MENJALANKAN ROUTE 
  // =======================
  public static function dispatch(): void
  {
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);
    $basePath = rtrim($scriptName, '/');

    // Hapus base path
    $uri = preg_replace('#^' . preg_quote($basePath) . '#', '', $uri);

    // Normalisasi slash: hapus slash di awal/akhir & ganti double slash dengan satu
    $uri = '/' . trim($uri, '/');
    $uri = preg_replace('#/+#', '/', $uri); // ubah semua double slash jadi satu
    $uri = $uri === '/' ? '/' : $uri;

    $routes = self::$routes[$method] ?? [];

    foreach($routes as $path => $route){
      // ubah {param} jadi regex pencocokan segmen
      $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $path);
      $pattern = '#^' . $pattern . '$#';

      if (preg_match($pattern, $uri, $matches)) {
        array_shift($matches); // hapus full match

        // Middleware check
        if (!empty($route['middlewares'])) {
          Middleware::handle($route['middlewares']);
        }

        [$controller, $methodName] = explode('@', $route['action']);
        $controllerClass = "App\\Controllers\\$controller";

        if (!class_exists($controllerClass)) {
          throw new \Exception("Controller $controllerClass tidak ditemukan.");
        }

        $controllerObj = new $controllerClass();

        if (!method_exists($controllerObj, $methodName)) {
          throw new \Exception("Method $methodName tidak ditemukan di $controllerClass");
        }

        // Kirim parameter ke controller (misalnya id)
        $controllerObj->$methodName(...$matches);
        return;
      }
    }

    // jika tidak ada route cocok
    http_response_code(404);
    echo "<h1>404 - Halaman tidak ditemukan</h1>";
  }
}