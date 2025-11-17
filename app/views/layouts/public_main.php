<?php
use App\Core\FlashMessage;
?>

<!-- Main content area -->
<div class="public-content">
  <?php
    // tampilkan flashmessage jika ada
    if(class_exists('FlashMessage')){
      FlashMessage::show();
    }

    // tampilkan konten halaman dinamis
    if(!empty($content)){
      $viewPath = __DIR__ . '/../' . $content . '.php';
      
      if(file_exists($viewPath)){
        include $viewPath;
      }else{
        echo "<p>View <strong>{$content}</strong> tidak ditemukan.</p>";
      }
    }else{
      echo "<p class='text-muted'>Tidak ada konten untuk ditampilkan.</p>";
    }
  ?>
</div>