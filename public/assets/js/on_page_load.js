// load-only
window.addEventListener("load", () => {
  document
    .querySelectorAll(".anim-load, .anim-left, .anim-right")
    .forEach((el, i) => setTimeout(() => el.classList.add("show"), i * 150));
});

window.addEventListener("DOMContentLoaded", () => {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("show");
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.25, rootMargin: "0px 0px -50px 0px" }
  );

  document
    .querySelectorAll(".anim-scroll")
    .forEach((el) => observer.observe(el));
});

// kurikulum
const sections = document.querySelectorAll('.educator-section');
const observer = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) entry.target.classList.add('show');
  });
}, { threshold: 0.3 });

sections.forEach(section => observer.observe(section));
