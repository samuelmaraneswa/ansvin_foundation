document.addEventListener("DOMContentLoaded", () => {
  const sidebar = document.getElementById("sidebar");
  const toggleBtn = document.querySelector('#sidebarToggle');
  const userProfile = document.querySelector(".user-profile");

  if (userProfile) {
    userProfile.addEventListener("click", () => {
      userProfile.classList.toggle("active");
    });

    document.addEventListener("click", (e) => {
      if (!userProfile.contains(e.target)) {
        userProfile.classList.remove("active");
      }
    });
  }

  if (toggleBtn) {
    toggleBtn.addEventListener("click", () => {
      console.log('click')
      sidebar.classList.toggle('closed');
      // ubah ikon x/hamburger
      const icon = toggleBtn.querySelector('i')

      if(sidebar.classList.contains('closed')){
        // sidebar tertutup -> tampil hamburger putih
        icon.className = "fa-solid fa-bars";
        toggleBtn.classList.add("nonactive");
      }else{
        // sidebar terbuka -> tampil x merah
        icon.className = "fa-solid fa-xmark";
        toggleBtn.classList.remove("nonactive");
      }
    });
  }

  // Optional: search filter menu
  const searchInput = document.getElementById('sidebarSearch');
  searchInput?.addEventListener('input', function(){
    const query = searchInput.value.toLowerCase();
    document.querySelectorAll('.sidebar-menu li').forEach(li => {
        const text = li.textContent.toLowerCase();
        li.style.display = text.includes(query) ? 'block' : 'none';
    });
  });

  document.querySelectorAll(".has-submenu > a").forEach((link) => {
    link.addEventListener("click", (e) => {
      e.preventDefault();
      link.parentElement.classList.toggle("open");
    });
  });
  
});