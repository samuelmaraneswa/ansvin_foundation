document.addEventListener("DOMContentLoaded", function() {
  const searchInput = document.getElementById("searchInput");
  const suggestions = document.getElementById("suggestions");
  const kategoriSelect = document.querySelector('select[name="kategori_id"]');
  const searchIcon = document.getElementById("searchIcon");

  // let debounceTimeout;
  let suggestionItems = [];
  let selectedIndex = -1; // index untuk keyboard navigation

  // Fungsi fetch suggestion
  async function fetchSuggestions(query = "") {
    const kategori = kategoriSelect.value;

    // jika input kosong, tutup suggestion
    if (query.trim() === "") {
      suggestionItems = [];
      selectedIndex = -1;
      return;
    }

    try {
      const res = await fetch(
        `${base_url}/admin/artikel/search_suggest?search=${encodeURIComponent(
          query
        )}&kategori_id=${kategori}`
      );
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();

      suggestionItems = data;
      selectedIndex = -1;

      // bersihkan container
      suggestions.innerHTML = "";

      if (!data.length) {
        suggestions.innerHTML = "<div>Tidak ditemukan</div>";
      } else {
        const q = query.trim();
        const re = new RegExp(q.replace(/[.*+?^${}()|[\]\\]/g, "\\$&"), "gi");

        data.forEach((item) => {
          const div = document.createElement("div");
          div.dataset.title = item.judul;

          // buat highlight aman tanpa innerHTML langsung
          if (q) {
            let last = 0;
            let m;
            while ((m = re.exec(item.judul)) !== null) {
              div.append(item.judul.slice(last, m.index));
              const strong = document.createElement("strong");
              strong.textContent = m[0];
              div.append(strong);
              last = m.index + m[0].length;
            }
            div.append(item.judul.slice(last));
          } else {
            div.textContent = item.judul;
          }

          suggestions.appendChild(div);
        });
      }

      suggestions.style.display = "block";
    } catch (err) {
      console.error("Fetch/JSON error:", err);
    }
  }

  // jika salah satu elemen tidak ada → hentikan
  if (!searchInput || !suggestions || !kategoriSelect || !searchIcon) return;
  
  // Event focus → tampilkan suggestion jika ada input
  searchInput.addEventListener("focus", () => {
    const query = searchInput.value.trim();
    if (query === "") {
      suggestions.style.display = "none";
      updateIcon();
      return;
    }
    fetchSuggestions(query);
    updateIcon();
  });

  // Event keyup → debounce
  searchInput.addEventListener("keyup", function (e) {
    const query = this.value.trim();

    if (["ArrowDown", "ArrowUp", "Enter"].includes(e.key)) return;

    // clearTimeout(debounceTimeout);
    // debounceTimeout = setTimeout(() => {
    //   fetchSuggestions(query);
    //   updateIcon();
    // }, 300);
    if (query === "") {
      suggestions.style.display = "none";
      updateIcon();
      return;
    }
    fetchSuggestions(query);
    updateIcon();
  });

  // Update ikon X / search
  function updateIcon() {
    if (searchInput.value.trim() === "") {
      searchIcon.classList.remove("active", "fa-times");
      searchIcon.classList.add("fa-search");
    } else {
      searchIcon.classList.add("active");
      searchIcon.classList.remove("fa-search");
      searchIcon.classList.add("fa-times");
    }
  }

  function scrollToSelected() {
    const selected = suggestions.querySelector("div.selected");
    if (selected) {
      // pastikan item yang diseleksi terlihat
      selected.scrollIntoView({ block: "nearest" });
    }
  }

  // Clear selection highlight
  function clearSelection() {
    suggestions
      .querySelectorAll("div")
      .forEach((div) => div.classList.remove("selected"));
  }

  // Fungsi search saat tombol enter atau klik search
  function doSearch() {
    const query = searchInput.value.trim();
    const kategori = kategoriSelect.value;

    // contoh: redirect ke halaman search sesuai input
    window.location.href = `${base_url}/admin/artikel?search=${encodeURIComponent(
      query
    )}&kategori_id=${kategori}`;
  }

  // Keyboard navigation + Enter
  searchInput.addEventListener("keydown", function (e) {
    const items = suggestions.querySelectorAll("div[data-title]"); // pastikan hanya div dengan data-title
    if (!items.length && (e.key === "ArrowDown" || e.key === "ArrowUp")) return;

    if (e.key === "ArrowDown") {
      e.preventDefault();
      selectedIndex = (selectedIndex + 1) % items.length;
      clearSelection();
      items[selectedIndex].classList.add("selected");
      searchInput.value = items[selectedIndex].dataset.title;
      scrollToSelected();
    } else if (e.key === "ArrowUp") {
      e.preventDefault();
      selectedIndex = (selectedIndex - 1 + items.length) % items.length;
      clearSelection();
      items[selectedIndex].classList.add("selected");
      searchInput.value = items[selectedIndex].dataset.title;
      scrollToSelected();
    } else if (e.key === "Enter") {
      e.preventDefault();

      // jika ada item suggestion yang dipilih, isi input
      if (selectedIndex >= 0 && items[selectedIndex]) {
        searchInput.value = items[selectedIndex].dataset.title;
      }

      searchInput.blur();
      updateIcon();

      // jalankan search
      doSearch();
    }
  });

  // Klik suggestion → isi input, caret di akhir, suggestions tetap update
  suggestions.addEventListener("click", (e) => {
    const div = e.target.closest("div[data-title]");
    if (div) {
      // isi input dengan judul suggestion
      searchInput.value = div.dataset.title;

      // fokus ke input
      searchInput.focus();

      // letakkan caret di akhir teks
      const length = searchInput.value.length;
      searchInput.setSelectionRange(length, length);

      // panggil fetch lagi supaya suggestions tetap terbuka sesuai input
      fetchSuggestions(searchInput.value);

      updateIcon();
    }
  });

  // Klik ikon X → kosongkan input, fokus, suggestions muncul semua
  searchIcon.addEventListener("click", () => {
    if (searchIcon.classList.contains("active")) {
      searchInput.value = "";
      searchInput.focus();
      fetchSuggestions("");
      updateIcon();
    }
  });

  // Klik di luar → suggestion tertutup
  onClickOutside(
    suggestions,
    () => {
      suggestions.style.display = "none";
      updateIcon();
    },
    [searchInput, searchIcon]
  );

  updateIcon();
});