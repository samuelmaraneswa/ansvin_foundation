<?php
namespace App\Core;

class Config{
  /** @var array<string, mixed> */
  private static array $config = [
    'app_name' => 'Ansvin Foundation',
    'base_url' => 'http://localhost/ansvin_foundation/public',
    'db' => [
      'host' => 'localhost',
      'name' => 'ansvin_foundation',
      'user' => 'root',
      'pass' => '',
      'charset' => 'utf8mb4',
    ],
    'timezone' => 'Asia/Jakarta'
  ];

  /**
   * Mengambil nilai konfigurasi berdasarkan key.
   * contoh: Config::get('base_url')
   */
  public static function get(string $key, mixed $default = null): mixed
  {
    return self::$config[$key] ?? $default;
  }

  /**
   * Mengatur atau menimpa konfigurasi
   * contoh: Config::set('debug',true)
   */
  public static function set(string $key, mixed $value):void
  {
    self::$config[$key] = $value;
  }

  /**
   * Mengambil konfigurasi database secara lengkap
   */
  public static function getDatabaseConfig():array
  {
    return self::$config['db'];
  }
}