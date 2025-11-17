<?php
use App\Core\FlashMessage;

// Pastikan variabel $page tersedia di semua include
if (!isset($page)) $page = null;

include __DIR__ . '/admin_header.php';
?>

<!-- Main content area -->
<div class="admin-content">
  <div class="admin-container">
    <?php
      // tampilkan flashmessage jika ada
      if(class_exists('FlashMessage')){
        FlashMessage::show();
      }
 
      // tampilkan konten halaman dinamis
      if(!empty($content)){
        $viewPath = __DIR__ . '/../' . $content . '.php';
        
        if(file_exists($viewPath)){
          if(method_exists($this, 'includeView')){
            $this->includeView($content);
          } else {
            include $viewPath;
          }
        }else{
          echo "<p>View <strong>{$content}</strong> tidak ditemukan.</p>";
        }
      }else{
        echo "<p class='text-muted'>Tidak ada konten untuk ditampilkan.</p>";
      }
    ?>
  </div>
</div>

<?php include __DIR__ . '/admin_footer.php' ?>