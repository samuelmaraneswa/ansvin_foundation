export function initUnitPegawaiSearchStep2(){
  const input = document.getElementById("searchInputUnitPegawai")
  const icon = document.getElementById("searchIconUnitPegawai")
  const suggestions = document.getElementById("suggestionsUnitPegawai")
  const form = document.querySelector(".search-form-unitPegawai")

  if(!input || !icon || !suggestions || !form){
    console.warn("Elemen search unit pegawai tidak lengkap.");
    return;
  }

  // helper: tutup & kosongkan suggestions
  function closeSuggestions(){
    suggestions.innerHTML = "";
    suggestions.style.display = "none";
  }

  // 1. saat user mengetik
  input.addEventListener("input", () => {
    const keyword = input.value.trim();

    if(keyword.length === 0){
      // icon search
      icon.classList.remove("fa-times", "active");
      icon.classList.add("fa-search");

      // belum fetch apapun, cukup tutup saran
      closeSuggestions();
      return;
    }

    // ada teks -> icon jadi x
    icon.classList.remove("fa-search");
    icon.classList.add("fa-times", "active");

    // belum render saran di step 1 - hanya siapkan container visible kosong
    suggestions.style.display = "block";
    suggestions.innerHTML = "";
  });

  // 2. klik icon: jika x maka clear input & tutup saran
  icon.addEventListener("click", () => {
    if(icon.classList.contains("fa-times")) {
      input.value = "";
      icon.classList.remove("fa-times", "active");
      icon.classList.add("fa-search");
      closeSuggestions();
      input.focus();
    }
  });

  // 3. klik diluar suggestions -> tutup
  document.addEventListener("click", (e) => {
    if(!suggestions.contains(e.target) && e.target !== input){
      closeSuggestions();
    }
  });

  // 4. submit form
  form.addEventListener("submit", (e) => {
    e.preventDefault();
  });
}