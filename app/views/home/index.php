<!-- Carousel start -->
<section class="hero-carousel">
  <div class="carousel">
    <div class="slide active">
      <img src="<?=$base_url?>/assets/img/carousel/c1.jpg" alt="Sekolah 1" loading="lazy">
    </div>
    <div class="slide">
      <img src="<?=$base_url?>/assets/img/carousel/c2.jpg" alt="Sekolah 2" loading="lazy">
    </div>
    <div class="slide">
      <img src="<?=$base_url?>/assets/img/carousel/c3.jpg" alt="Sekolah 3" loading="lazy">
    </div>
    <div class="slide">
      <img src="<?=$base_url?>/assets/img/carousel/c4.jpg" alt="Sekolah 4" loading="lazy">
    </div>
    <div class="slide">
      <img src="<?=$base_url?>/assets/img/carousel/c5.jpg" alt="Sekolah 5" loading="lazy">
    </div>
    <div class="slide">
      <img src="<?=$base_url?>/assets/img/carousel/c6.jpg" alt="Sekolah 6" loading="lazy">
    </div>
  </div>
  
  <!-- Indicator dots -->
  <div class="carousel-dots"></div>
</section>
<!-- Carousel end -->

<div class="welcome-container">
  <div class="welcome-kiri anim-load">
    <h2>Welcome</h2>
    <span>to our official website</span>
  </div>

  <div class="welcome-mid anim-load">
    <p>#Ansvin.sch.id</p>
    <p>one of the best private schools in Batam</p>
  </div>

  <div class="welcome-kanan anim-load">
    <button>CONTACT US</button>
    <i class="fa-solid fa-arrow-up-right-from-square"></i>
  </div>
</div>

<div class="school-information">
  <div class="school-information-kiri anim-item anim-left">
    <h2>Ansvin School</h2>
    <p>Ansvin School is one of the best private schools in Batam that provides character education from an early age. The curriculum taught at Ansvin School is the National Curriculum combined with Curriculum from the United States, namely ACE (Accelerated Christian Education), also called “School of Tomorrow”. 
    </p>
    <p>Lessons at Ansvin school are in English, this aims to strengthen the child’s foreign language skills, preparing them for an increasingly globalized world economy. In addition to English, they also learn Mandarin.</p>
  </div>
  <div class="school-information-kanan anim-item anim-right">
    <img src="<?=$base_url?>/assets/img/ansvin-front.png" alt="" loading="lazy">
  </div>
</div>

<!-- join us -->
 <div class="join-us anim-scroll">
  <div class="join-us-content">
    <h2>Bergabunglah dengan Kami</h2>
    <p>Rasakan pengalaman belajar terbaik di Ansvin School dan kembangkan potensi anak Anda sejak dini.</p>
    <a href="<?= $base ?>/pendaftaran" class="btn-join">Daftar Sekarang</a>
  </div>
</div>
<!-- join us end -->

<!-- facilities start -->
 <section class="facilities anim-scroll">
  <h2>Fasilitas </h2>
  <p>Mendukung proses belajar yang nyaman dan menyenangkan.</p>

  <div class="facility-grid">
    <a href="#">
      <div class="facility-item facility-lab">
        <i class="fa-solid fa-flask"></i>
        <h3>Laboratorium Sains</h3>
        <p>Eksperimen dan pembelajaran sains interaktif.</p>
      </div>
    </a>

    <a href="#">
      <div class="facility-item facility-computer">
        <i class="fa-solid fa-computer"></i>
        <h3>Lab Komputer</h3>
        <p>Dilengkapi perangkat modern untuk mendukung literasi digital.</p>
      </div>
    </a>

    <a href="#">
      <div class="facility-item facility-footbol">
        <i class="fa-solid fa-futbol"></i>
        <h3>Lapangan Olahraga</h3>
        <p>Fasilitas olahraga lengkap untuk kegiatan fisik siswa.</p>
      </div>
    </a>
  </div>

  <div class="facility-more">
    <a href="<?= $base ?>/fasilitas" class="btn-facility">See for More<i class="fa-solid fa-arrow-right"></i></a>
  </div>
</section>
<!-- facilities end -->

<!-- kurikulum start -->
 <section class="educator-section">
  <div class="educator-container left-image">
    <div class="educator-image">
      <img src="<?=$base_url?>/assets/img/bible.jpg" alt="Guru profesional di kelas" loading="lazy">
    </div>
    <div class="educator-text">
      <h2>The Accelerated Christian Education (ACE)</h2>
      <p>
        Bible-based, K-12 program that uses self-instructional workbooks called PACEs (Packets of Accelerated Christian Education) for a mastery-based, self-paced learning approach. Students work through these bite-sized units at their own speed, mastering material before moving on to the next level, with core subjects including Math, Science, English, Social Studies, Word Building, and Literature.
      </p>
    </div>
  </div>
</section>

<section class="educator-section bagian2">
  <div class="educator-container right-image">
    <div class="educator-text">
      <p>
        An initial diagnostic test places students at the appropriate level for each subject, and an 80% score on the mastery-based PACE Test is required to progress. 
        Accelerated Christian Education is a Christian education based on the Bible. In the ACE curriculum Faith, Truth, and Biblical principles are integrated into every PACE (Packeted of Christian Education). The educational program is built on a strong Biblical foundation. This is believed to help build good character in a child’s life.
      </p>
    </div>
    <div class="educator-image">
      <img src="<?=$base_url?>/assets/img/church.jpg" alt="Guru berdedikasi dalam pelatihan" loading="lazy">
    </div>
  </div>
</section>
<!-- kurikulum end -->

<!-- event dan school environment start -->
 <section class="school-event-environment anim-scroll">
  <div class="environment-event">
    <div class="env-event-judul">
      <h2>Environment & Event</h2>
    </div>
    <div class="env-event-btn">
      <button class="btn-env-event">Detail</button>
      <i class="fa-solid fa-arrow-right"></i>
    </div>
  </div>

  <div class="artikel-cards">
    <?php foreach($featuredArticles as $artikel): ?>

      <div class="artikel-card">
        <a href="">
          <?php
          // Ambil path thumbnail kecil (400px)
          $thumb = $artikel['thumbnail'] 
              ? str_replace('img_', 'thumb_img_', $artikel['thumbnail']) 
              : 'assets/img/default.jpg';

          // 🔹 Buat path WebP dari thumbnail
          $thumbWebp = preg_replace('/\.\w+$/', '.webp', $thumb);
          ?>

          <picture>
            <!-- Browser mendukung WebP -->
            <source srcset="<?= $thumbWebp ?>" type="image/webp">
            <!-- Fallback ke JPG/PNG -->
            <img src="<?= $thumb ?>" alt="<?= htmlspecialchars($artikel['judul']) ?>" loading="lazy">
          </picture>
          <div class="artikel-card-body">
            <h3><?= htmlspecialchars($artikel['judul']) ?></h3>
            <span class="card-date">Dipublish: <?= explode(' ', $artikel['tanggal_posting'])[0] ?></span>
            <p><?= $artikel['excerpt']; ?> <span class="baca-selengkapnya">...baca selengkapnya</span></p>
          </div>
        </a>
      </div>

    <?php endforeach; ?>
  </div>
</section>
<!-- event dan school environment end -->

<!-- pengajar section start -->
 <section class="pengajar-section educator-section">
  <div class="pengajar-section-top educator-image">
    <img src="<?=$base_url?>/assets/img/pengajar.jpeg" alt="" loading="lazy">
  </div>
  <div class="pengajar-section-body educator-text">
    <a href=""><h2>Tenaga Pendidik<i class="fa-solid fa-arrow-right"></i></h2></a>
    <p>Pendidikan hadir bukan sekadar untuk mengajar, 
        tetapi untuk membimbing dan menginspirasi.</p> <p>Latar belakang akademik 
        yang kuat dan pengalaman luas, membuat para pendidik dapat menjalankan perannya 
        dengan penuh dedikasi. 
        Di balik setiap keberhasilan siswa, ada guru yang bekerja dengan hati, 
        memberi keteladanan, dan menumbuhkan semangat untuk menjadi pembelajar sejati.</p>
        <p>Mengikuti perkembangan zaman dan senantiasa mengembangkan diri melalui pelatihan dan pengembangan profesional berkelanjutan adalah motto dari pengajar kami.</p>
  </div>
 </section>
<!-- pengajar section end -->

<!-- Artikel -->
 <section class="artikel anim-scroll">
    <div class="artikel-top">
      <div class="artikel-judul">
        <h2>Artikel</h2>
      </div>
      <div class="artikel-btn">
        <button class="btn-artikel">Detail</button>
        <i class="fa-solid fa-arrow-right"></i>
      </div>
    </div>

    <div class="artikel-cards">
      <?php foreach($otherArticles as $oArtikel): ?>

      <div class="oartikel-card">
        <a href="">
          <?php
          // Ambil path thumbnail kecil (400px)
          $thumb = $oArtikel['thumbnail'] 
              ? str_replace('img_', 'thumb_img_', $oArtikel['thumbnail']) 
              : 'assets/img/default.jpg';

          // 🔹 Buat path WebP dari thumbnail
          $thumbWebp = preg_replace('/\.\w+$/', '.webp', $thumb);
          ?>

          <picture>
            <!-- Browser mendukung WebP -->
            <source srcset="<?= $thumbWebp ?>" type="image/webp">
            <!-- Fallback ke JPG/PNG -->
            <img src="<?= $thumb ?>" alt="<?= htmlspecialchars($oArtikel['judul']) ?>" loading="lazy">
          </picture>

          <div class="oartikel-card-body">
            <h3><?= htmlspecialchars($oArtikel['judul']) ?></h3>
            <span class="card-date">Dipublish: <?= explode(' ', $oArtikel['tanggal_posting'])[0] ?></span>
            <p><?= $oArtikel['excerpt']; ?> <span class="baca-selengkapnya">...baca selengkapnya</span></p>
          </div>
        </a>
      </div>

    <?php endforeach; ?>
  </div>
</section>
<!-- artikel end -->

