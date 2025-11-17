  <!-- Footer Start -->
  <footer class="footer">
    <div class="footer-top">
      <!-- Kiri: Lokasi -->
      <div class="footer-col footer-left">
        <h4>Location</h4>
        <p>Jl. Raja Haji Fisabillilah no.31H,</p>
        <p>Batam Center</p>
        
      </div>

      <!-- Tengah: Nama Sekolah + Logo -->
      <div class="footer-col footer-center">
        <h3>Ansvin School</h3>
        <img src="<?=$base?>/assets/img/logo.png" alt="Ansvin School Logo" class="footer-logo">
      </div>

      <!-- Kanan: Get in Touch -->
      <div class="footer-col footer-right">
        <h4>Get in Touch</h4>
        <p><i class="fa-brands fa-whatsapp"></i> <a href="https://wa.me/6281268189699" target="_blank">081268189699</a></p>
        <p><i class="fa-solid fa-phone"></i> 081268189699</p>
        <div class="footer-socials">
          <i class="fa-brands fa-instagram"></i>
          <i class="fa-brands fa-youtube"></i>
          <i class="fa-brands fa-tiktok"></i>
          <i class="fa-brands fa-facebook"></i>
        </div>
      </div>
    </div>

    <!-- Google Maps Embed -->
    <div class="map-container">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.0400431128132!2d104.03956207398167!3d1.1317103622402394!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31d9890f6415edd9%3A0xb76890527bf3e1f7!2sAnsvin%20Academy%20%2F%20Ansvin%20School!5e0!3m2!1sid!2sid!4v1761471421403!5m2!1sid!2sid"
        width="100%"
        height="180"
        style="border:0;"
        allowfullscreen=""
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade">
      </iframe>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
      <p>Copyright © 2025 The President and Fellows of Ansvin School</p>
    </div>
  </footer>
  <!-- Footer End -->

  <script src="<?= App\Core\Config::get('base_url') ?>/assets/js/carousel.js"></script>
  <script src="<?= App\Core\Config::get('base_url') ?>/assets/js/on_page_load.js"></script>
  
</body>
</html>