import { getJSON } from "../../utils/api.js";
import { fetchUnitPegawaiData } from "./pegawaiUnitTable.js";

export function initUnitPegawaiSearch(base_url){
  const input = document.getElementById("searchInputUnitPegawai")
  const icon = document.getElementById("searchIconUnitPegawai")
  const suggestions = document.getElementById("suggestionsUnitPegawai")
  const form = document.querySelector(".search-form-unitPegawai")

  if(!input || !icon || !suggestions || !form){
    console.warn("Elemen search unit pegawai tidak lengkap.");
    return;
  }

  let currentFocus = -1;

  // helper: tutup & kosongkan suggestions
  function closeSuggestions(){
    suggestions.innerHTML = "";
    suggestions.style.display = "none";
  }

  // 1. saat user mengetik
  input.addEventListener("input", async() => {
    const keyword = input.value.trim();

    if(keyword.length === 0){
      // icon search
      icon.classList.remove("fa-times", "active");
      icon.classList.add("fa-search");
      closeSuggestions();
      fetchUnitPegawaiData();
      return;
    }

    // ada teks -> icon jadi x
    icon.classList.remove("fa-search");
    icon.classList.add("fa-times", "active");

    try{
      const slug = window.location.pathname.split("/")[2];
      const url = `${base_url}/unit/${slug}/pegawai/search?keyword=${encodeURIComponent(keyword)}`;
      const result = await getJSON(url);
      // console.log("📦 Data pegawai unit:", result);

      renderSuggestions(result, keyword);
    }catch(err){
      console.error("Error fetch suggestions:", err);
    }
  });

  icon.addEventListener("click", () => {
    if (icon.classList.contains("fa-times")) {
      input.value = "";
      icon.classList.remove("fa-times", "active");
      icon.classList.add("fa-search");
      closeSuggestions();
      fetchUnitPegawaiData(); // tampilkan semua data lagi
      input.focus();
    }
  });

  input.addEventListener("keydown", (e) => {
    const items = suggestions.querySelectorAll(".suggestions-item");
    const hasItems = items.length > 0;

    if(!items.length) return;

    if (e.key === "ArrowDown") {
      if (hasItems) {
        currentFocus++;
        addActive(items);
      }
      e.preventDefault();
    } else if (e.key === "ArrowUp") {
      if (hasItems) {
        currentFocus--;
        addActive(items);
      }
      e.preventDefault();
    } else if (e.key === "Enter") {
      e.preventDefault();

      const keyword = input.value.trim();

      // 🔹 hanya jalankan klik suggestion jika user sudah memilih (pakai panah)
      if (currentFocus > -1 && hasItems && items[currentFocus]) {
        items[currentFocus].click();
      } else {
        // 🔹 kalau tidak ada yang dipilih, langsung cari pakai input saat ini
        if (keyword.length > 0) {
          fetchUnitPegawaiData(keyword, 1); // reset ke halaman pertama
          closeSuggestions();
        }
      }
    } else if (e.key === "Escape") {
      closeSuggestions();
    }
  });

  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const keyword = input.value.trim();
    closeSuggestions();
    fetchUnitPegawaiData(keyword);
  })

  // helper untuk menandai item aktif
  function addActive(items){
    if(!items.length) return;
    removeActive(items);

    if(currentFocus >= items.length) currentFocus = 0;
    if(currentFocus < 0) currentFocus = items.length - 1;

    const activeItem = items[currentFocus];
    activeItem.classList.add("active");

    // pastikan item terlihat
    activeItem.scrollIntoView({
      behavior: "auto",
      block: "nearest",
      inline: "nearest",
    });
  }

  function removeActive(items){
    items.forEach((i) => {
      i.classList.remove("active")
    });
  }

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

  // render hasil suggestions
  function renderSuggestions(result, keyword){
    suggestions.innerHTML = "";
    if (!result?.data || result.data.length === 0) {
      const div = document.createElement("div");
      div.className = "suggestions-item no-result";
      div.textContent = "Tidak ada data ditemukan.";
      suggestions.appendChild(div);
      suggestions.style.display = "block";
      return;
    }

    // tampilkan tiap nama dengan highlight
    result.data.forEach((p) => {
      const regex = new RegExp(`(${keyword})`, "gi");
      const namaHighlighted = p.nama.replace(regex, "<strong>$1</strong>");
      const div = document.createElement("div");
      div.className = "suggestions-item";
      div.innerHTML = namaHighlighted;

      // klik suggestion -> isi input
      div.addEventListener("click", () => {
        input.value = p.nama;
        closeSuggestions();

        // fokus & cursor di akhir teks
        input.focus();
        // fetchUnitPegawaiData(p.nama)
      });

      suggestions.appendChild(div);
    });

    suggestions.style.display = "block";
  }
}