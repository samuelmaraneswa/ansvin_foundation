document.addEventListener("DOMContentLoaded", () => {
  const slides = document.querySelectorAll(".slide");
  const carousel = document.querySelector(".carousel");
  const dotsContainer = document.querySelector(".carousel-dots");

  if (!slides.length || !dotsContainer) return;

  let currentIndex = 0;
  let interval;
  const totalSlides = slides.length;

  // Clone slide pertama dan tambahkan ke akhir (untuk efek looping mulus)
  const firstClone = slides[0].cloneNode(true);
  carousel.appendChild(firstClone);

  // Buat dots sesuai jumlah slide asli (bukan termasuk clone)
  slides.forEach((_, index) => {
    const dot = document.createElement("button");
    if (index === 0) dot.classList.add("active");
    dot.addEventListener("click", () => {
      showSlide(index);
      resetInterval();
    });
    dotsContainer.appendChild(dot);
  });

  const dots = document.querySelectorAll(".carousel-dots button");

  function updateDots(index) {
    dots.forEach((dot, i) => dot.classList.toggle("active", i === index));
  }

  function showSlide(index) {
    carousel.style.transition = "transform 1s ease-in-out";
    carousel.style.transform = `translateX(-${index * 100}%)`;
    currentIndex = index;
    updateDots(index % totalSlides);
  }

  function nextSlide() {
    currentIndex++;
    showSlide(currentIndex);
  }

  function startInterval() {
    interval = setInterval(nextSlide, 10000);
  }

  function resetInterval() {
    clearInterval(interval);
    startInterval();
  }

  carousel.addEventListener("transitionend", () => {
    if (currentIndex === totalSlides) {
      carousel.style.transition = "none";
      carousel.style.transform = "translateX(0%)";
      currentIndex = 0;

      // aktifkan kembali transisi setelah 50ms
      setTimeout(() => {
        carousel.style.transition = "transform 1s ease-in-out";
      }, 50);
    }
  });

  document.addEventListener("visibilitychange", () => {
    if (document.visibilityState === "hidden") {
      clearInterval(interval);
    } else if (document.visibilityState === "visible") {
      carousel.style.transition = "none";
      carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
      setTimeout(() => {
        carousel.style.transition = "transform 1s ease-in-out";
      }, 50);
      startInterval();
    }
  });

  window.addEventListener("load", () => startInterval());
});

