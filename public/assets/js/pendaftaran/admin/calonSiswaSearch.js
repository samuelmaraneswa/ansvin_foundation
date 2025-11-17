// public/assets/js/pendaftaran/admin/calonSiswaSearch.js
import { getJSON } from "../../utils/api.js"; // helper untuk fetch JSON

export function initCalonSiswaSearch(onSelectCallback) {
  const searchInput = document.getElementById("searchInputCalonSiswa");
  const searchIcon = document.getElementById("searchIconCalonSiswa");
  const suggestionsBox = document.getElementById("suggestionsCalonSiswa");
  const searchForm = document.getElementById("searchFormCalonSiswa")

  let suggestions = [];
  let selectedIndex = -1;

  // 🔍 Saat mengetik di input
  searchInput.addEventListener("input", async (e) => {
    const query = e.target.value.trim();

    // Ubah icon search <-> X
    if (query.length > 0) {
      searchIcon.classList.remove("fa-search");
      searchIcon.classList.add("fa-times");
      searchIcon.style.color = "red";
    } else {
      resetSearchIcon();
    }

    // Jika input kosong, sembunyikan suggestions
    if (!query) {
      hideSuggestions();
      return;
    }

    // Fetch suggestions dari server
    const data = await getJSON(
      `${base_url}/admin/pendaftaran/search?keyword=${encodeURIComponent(
        query
      )}`
    );
    // console.log(data)
    suggestions = data.data || [];

    showSuggestions(query);
  });

  // ⌨️ Navigasi panah atas/bawah + Enter
  searchInput.addEventListener("keydown", (e) => {
    if (["ArrowDown", "ArrowUp", "Enter"].includes(e.key)) {
      e.preventDefault();
    }

    if (e.key === "ArrowDown") {
      selectedIndex++;
      if (selectedIndex >= suggestions.length) selectedIndex = 0; // ⬇️ loop ke atas
      highlightSuggestion();
    } else if (e.key === "ArrowUp") {
      selectedIndex--;
      if (selectedIndex < 0) selectedIndex = suggestions.length - 1; // ⬆️ loop ke bawah
      highlightSuggestion();
    } else if (e.key === "Enter") {
      if (selectedIndex >= 0) {
        // 🔹 pilih item yang sedang di-highlight
        chooseSuggestion(selectedIndex);
      } else {
        // 🔹 kalau belum pilih apa pun, langsung cari pakai input
        const keyword = searchInput.value.trim();
        if (keyword !== "" && typeof onSelectCallback === "function") {
          onSelectCallback({ nama_lengkap: keyword });
        }
        hideSuggestions();
      }
    } else if (e.key === "Escape") {
      hideSuggestions(); // 🔥 tutup dropdown
      selectedIndex = -1;
    }
  });

  if (searchForm) {
    searchForm.addEventListener("submit", (e) => {
      e.preventDefault(); // jangan reload halaman
      const keyword = searchInput.value.trim();

      if (keyword !== "") {
        // 🔥 panggil render tabel calon siswa berdasarkan keyword
        if (typeof onSelectCallback === "function") {
          onSelectCallback({ nama_lengkap: keyword });
        }
      }
    });
  }

  // 🖱️ Klik icon search atau X
  searchIcon.addEventListener("click", () => {
    if (searchInput.value.trim() !== "") {
      clearSearch();
    }
  });

  // 🖱️ Klik di luar suggestions → sembunyikan
  document.addEventListener("click", (e) => {
    if (!e.target.closest(".search-form")) hideSuggestions();
  });

  // ======================
  // 🔧 Fungsi Pendukung
  // ======================

  function showSuggestions(query) {
    suggestionsBox.innerHTML = "";
    if (suggestions.length === 0) {
      suggestionsBox.innerHTML = `
        <div class="suggestion-item no-result">
          Tidak ada data ditemukan.
        </div>
      `;
      suggestionsBox.style.display = "block";
      return;
    }

    const html = suggestions
      .map(
        (s, i) => `
        <div class="suggestion-item" data-index="${i}">
          ${highlightMatch(s.nama_lengkap, query)}
        </div>`
      )
      .join("");

    suggestionsBox.innerHTML = html;
    suggestionsBox.style.display = "block";

    // Klik suggestion
    document.querySelectorAll(".suggestion-item").forEach((item) => {
      item.addEventListener("click", () => {
        const index = item.dataset.index;
        chooseSuggestion(index);
      });
    });
  }

  function hideSuggestions() {
    suggestionsBox.style.display = "none";
    suggestionsBox.innerHTML = "";
    selectedIndex = -1;
  }

  function highlightMatch(text, query) {
    const regex = new RegExp(`(${query})`, "gi");
    return text.replace(regex, "<strong>$1</strong>");
  }

  function highlightSuggestion() {
    const items = suggestionsBox.querySelectorAll(".suggestion-item");
    const total = items.length;
    if (total === 0) return;

    // looping index
    if (selectedIndex >= total) selectedIndex = 0;
    if (selectedIndex < 0) selectedIndex = total - 1;

    // hapus semua highlight dulu
    items.forEach((item) => item.classList.remove("active"));

    // tambahkan highlight pada item aktif
    const activeItem = items[selectedIndex];
    activeItem.classList.add("active");

    // isi input dengan teks aktif (aman)
    const selected = suggestions[selectedIndex];
    searchInput.value = selected?.nama_lengkap || selected?.nama || "";

    // --- Manual scroll control supaya hanya dropdown yang menggulir ---
    // pastikan suggestionsBox adalah container dengan overflow-y:auto
    const box = suggestionsBox;
    const itemTop = activeItem.offsetTop;
    const itemBottom = itemTop + activeItem.offsetHeight;
    const boxScrollTop = box.scrollTop;
    const boxHeight = box.clientHeight;

    if (itemTop < boxScrollTop) {
      // item di atas area terlihat -> geser ke atas
      box.scrollTop = itemTop;
    } else if (itemBottom > boxScrollTop + boxHeight) {
      // item di bawah area terlihat -> geser ke bawah
      box.scrollTop = itemBottom - boxHeight;
    }
  }

  function chooseSuggestion(index) {
    const item = suggestions[index];
    if (!item) return;
    searchInput.value = item.nama_lengkap;
    hideSuggestions();

    // 🔥 panggil callback saat item dipilih
    if (typeof onSelectCallback === "function") {
      onSelectCallback(item);
    }
  }

  function clearSearch() {
    searchInput.value = "";
    resetSearchIcon();
    hideSuggestions();

    if (typeof onSelectCallback === "function") {
      onSelectCallback({ nama_lengkap: "" });
    }

    searchInput.focus();
  }

  function resetSearchIcon() {
    searchIcon.classList.remove("fa-times");
    searchIcon.classList.add("fa-search");
    searchIcon.style.color = "";
  }
}