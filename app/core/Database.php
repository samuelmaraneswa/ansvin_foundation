<?php
namespace App\Core;

use PDO;
use PDOException;

class Database{
  private static ?PDO $connection = null;

  /**
   * membuat koneksi ke database (singleton pattern)
   */
  public static function connect(): PDO
  {
    if(self::$connection === null){
      $db = Config::getDatabaseConfig();

      $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s', 
        $db['host'],
        $db['name'],
        $db['charset'],
      );

      try{
        self::$connection = new PDO($dsn, $db['user'], $db['pass'], [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
          PDO::ATTR_EMULATE_PREPARES => false,
        ]);
      }catch(PDOException $e){
        die('Database connection failed:' . $e->getMessage());
      }
    }
    return self::$connection;
  }

  public static function disconnect(): void
  {
    self::$connection = null;
  }
}