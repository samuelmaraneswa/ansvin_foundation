import { getJSON } from "../../utils/api.js";
import { fetchPegawaiData } from "./pegawaiTable.js";

export function initSearchPegawai(base_url){
  const input = document.getElementById("searchInputPegawai")
  const icon = document.getElementById("searchIconPegawai")
  const suggestions = document.getElementById("suggestionsPegawai")
  const form = document.querySelector(".search-form-pegawai")
  const selectUnit = document.querySelector('select[name="unit_id_sekolah"]');

  if(!input || !form || !suggestions){
    console.warn("Elemen form pencarian pegawai tidak ditemukan.");
    return;
  }

  let currentFocus = -1;

  // 1.event ketika user mengetik
  input.addEventListener("input", async() => {
    const keyword = input.value.trim();
    const unitId = selectUnit ? selectUnit.value : "";

    if(keyword.length === 0) {
      icon.classList.remove("fa-times", "active")
      icon.classList.add("fa-search")
      closeSuggestions();
      fetchPegawaiData();
      return;
    }

    icon.classList.remove("fa-search");
    icon.classList.add("fa-times", "active");

    const url = `${base_url}/admin/pegawai/search_table?searchPegawai=${encodeURIComponent(
      keyword
    )}&unit_id_sekolah=${unitId}`;

    try{
      const result = await getJSON(url);
      console.log("📦 Hasil JSON:", result);
      renderSuggestions(result, keyword)
    }catch(err){
      console.error("Error fetch suggestions:", err);
    }
  });

  // 2. klik icon search/x
  icon.addEventListener("click", () => {
    if(icon.classList.contains("fa-times")){
      input.value = "";
      icon.classList.remove("fa-times", "active");
      icon.classList.add("fa-search");
      closeSuggestions();
      fetchPegawaiData();
    }
  });

  // 3.klik suggestions item
  function renderSuggestions(result, keyword){
    suggestions.innerHTML = "";
    currentFocus = -1;

    if(!result?.data || result.data.length === 0){
      const div = document.createElement("div")
      div.className = "suggestions-item no-result";
      div.textContent = "Tidak ada data ditemukan.";
      suggestions.appendChild(div);
      suggestions.style.display = "block";
      return;
    }

    result.data.forEach((p) => {
      const regex = new RegExp(`(${keyword})`, "gi");
      const namaHighlighted = p.nama.replace(regex, "<strong>$1</strong>");
      const div = document.createElement("div");
      div.className = "suggestions-item";
      div.innerHTML = namaHighlighted;

      div.addEventListener("click", () => {
        input.value = p.nama;
        closeSuggestions();
        input.focus();
        const len = input.value.length;
        input.setSelectionRange(len, len);
        // Nanti di tahap 3 kita tambahkan fetch tabel berdasarkan nama
      });
      
      suggestions.appendChild(div);
    });

    suggestions.style.display = "block";
  }

  // 4. keyboard navigation(up/down/enter/esc)
  input.addEventListener("keydown", (e) => {
    const items = suggestions.querySelectorAll(".suggestions-item");
    if(!items.length) return;

    if(e.key === "ArrowDown"){
      currentFocus++;
      addActive(items);
      e.preventDefault();
    }else if(e.key === "ArrowUp"){
      currentFocus--;
      addActive(items);
      e.preventDefault();
    }else if(e.key === "Enter"){
      e.preventDefault();

      // kalau ada item aktif → klik item itu
      if (currentFocus < -1 && items[currentFocus]) {
        items[currentFocus].click();
      }

      // kalau tidak ada item aktif → jalankan pencarian tabel
      const keyword = input.value.trim()
      const unitId = selectUnit ? selectUnit.value : "";
      if(keyword.length > 0 || unitId.length > 0){
        fetchPegawaiData(keyword, unitId);
      }

    }else if(e.key === "Escape"){
      closeSuggestions();
    }
  });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const keyword = input.value.trim();
    const unitId = selectUnit ? selectUnit.value : "";

    closeSuggestions();
    fetchPegawaiData(keyword, unitId);
  })

  // helper: aktifkan item saat dipilih
  function addActive(items){
    if(!items || !items.length) return;
    removeActive(items);

    if(currentFocus >= items.length) currentFocus = 0;
    if(currentFocus < 0) currentFocus = items.length - 1;

    const activeItem = items[currentFocus];
    activeItem.classList.add("active");

    // isi input dengan teks aktif
    input.value = activeItem.innerText;

    // posisikan cursor di akhir teks
    const len = input.value.length;
    input.setSelectionRange(len, len);

    // pastikan item aktif terlihat
    activeItem.scrollIntoView({
      behavior: "auto",
      block: "nearest",
      inline: "nearest",
    });
  }

  function removeActive(items){
    items.forEach((item) => item.classList.remove("active"));
  }

  // 5. tutup suggestion saat klik diluar
  document.addEventListener("click", (e) => {
    if(!suggestions.contains(e.target) && e.target !== input){
      closeSuggestions();
    }
  });

  function closeSuggestions(){
    suggestions.innerHTML = ""
    suggestions.style.display = "none";
  }
}