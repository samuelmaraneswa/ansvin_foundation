<?php
namespace App\Core;

class FlashMessage{
  public static function set(string $type, string $message): void{
    $_SESSION['flash'] = [
      'type' => $type,
      'message' => $message,
    ];
  }

  public static function get(): ?array{
    if(isset($_SESSION['flash'])){
      $flash = $_SESSION['flash'];
      unset($_SESSION['flash']);
      return $flash;
    }
    return null;
  }

  public static function show(): void{
    $flash = self::get();
    if($flash){
      $color = 'lightgray';
      if($flash['type'] === 'success') $color = 'lightgreen';
      if($flash['type'] === 'error') $color = 'salmon';
      if($flash['type'] === 'warning') $color = 'khaki';
      echo "<div style='background-color:{$color};padding:5px'>" . $flash['message'] . "</div>";
    }
  }
}