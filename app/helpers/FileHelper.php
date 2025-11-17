<?php

namespace App\Helpers;

class FileHelper
{
  public static function deleteDir($dir)
  {
    if (!is_dir($dir)) return;
    foreach (glob($dir . '/*') as $file) {
      is_dir($file) ? self::deleteDir($file) : unlink($file);
    }
    rmdir($dir);
  }
}
