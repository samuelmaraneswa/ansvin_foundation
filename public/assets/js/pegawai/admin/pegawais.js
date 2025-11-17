document.addEventListener("DOMContentLoaded", function () {
  const inputPegawai = document.getElementById("searchInputPegawai");
  const modal = document.getElementById("adminUnitModal");
  const btnAdd = document.getElementById("btn-pegawai-Add");
  const spanClose = modal.querySelector(".close");
  const form = document.getElementById("formAddPegawai");
  const tableBody = document.querySelector("#adminUnitTable tbody");
  const passwordField = form.querySelector('input[name="password"]');
  const passwordLabel = passwordField.previousElementSibling;
  const fotoPreview = form.querySelector("#foto-preview");
  const unitSelect = document.querySelector('select[name="unit_id"]');
  const nipInput = document.querySelector('input[name="nip"]');
  const searchIcon = document.getElementById("searchIconPegawai");
  const formSearch = document.querySelector(".search-form-pegawai");
  const suggestions = document.getElementById("suggestionsPegawai");
  const unitSelectSekolah = document.querySelector(
    'select[name="unit_id_sekolah"]'
  );

  let currentPage = 1;

  // === Update preview saat user pilih file baru ===
  const fotoInput = form.querySelector('input[name="foto"]');
  if (fotoInput && fotoPreview) {
    fotoInput.addEventListener("change", function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          fotoPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // === Buka / Tutup modal ===
  btnAdd.onclick = () => {
    modal.style.display = "flex";

    // Tambah mode → tampilkan password dan reset form
    form.reset();
    if (passwordField) {
      passwordField.style.display = "block";
      passwordField.value = "123456"; // password default
    }
    if (passwordLabel) passwordLabel.style.display = "block";

    // Reset preview foto ke default
    if (fotoPreview)
      fotoPreview.src = base_url + "/uploads/pegawai/default_img.jpg";
  };

  spanClose.onclick = () => (modal.style.display = "none");
  window.onclick = (e) => {
    if (e.target === modal) modal.style.display = "none";
  };
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") modal.style.display = "none";
  });

  // === Generate NIP otomatis saat unit berubah ===
  unitSelect.addEventListener("change", function () {
    const unitId = this.value;
    if (!unitId) return;
    fetch(`${base_url}/admin/pegawai/generate_nip?unit_id=${unitId}`)
      .then((res) => res.json())
      .then((data) => {
        if (data.success) nipInput.value = data.nip;
      })
      .catch(console.error);
  });

  // === Fungsi menambahkan baris baru di tabel ===
  function appendRow(data) {
    const tr = document.createElement("tr");
    tr.classList.add("fade-in");
    tr.innerHTML = `
      <td></td>
      <td>${data.nip}</td>
      <td>${data.nama}</td>
      <td>${data.nama_unit}</td>
      <td>
        <button class="btn-edit-pegawai" data-id="${data.id}">Edit</button>
        <button class="btn-delete-pegawai" data-id="${data.id}">Hapus</button>
      </td>
    `;
    const empty = tableBody.querySelector(".empty");
    if (empty) empty.remove();
    tableBody.prepend(tr);
    renumberRows();
  }

  // === Fungsi update row di tabel berdasarkan ID pegawai ===
  function updateRow(data) {
    // Cari <tr> yang memiliki button edit dengan data-id yang sama
    const row = tableBody
      .querySelector(`button.btn-edit-pegawai[data-id="${data.id}"]`)
      ?.closest("tr");
    if (!row) return; // kalau tidak ketemu, keluar

    // Update konten <td>
    row.children[1].textContent = data.nip; // kolom NIP
    row.children[2].textContent = data.nama; // kolom Nama
    row.children[3].textContent = data.nama_unit; // kolom Unit
  }

  // === Fungsi perbarui nomor urut ===
  function renumberRows() {
    const rows = tableBody.querySelectorAll("tr");
    rows.forEach((row, i) => {
      const cell = row.querySelector("td:first-child");
      if (cell) cell.textContent = i + 1;
    });
  }

  // === Submit form (AJAX) ===
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    // Hapus semua pesan error sebelumnya
    form.querySelectorAll(".error-message").forEach((el) => el.remove());

    const formData = new FormData(form);
    try {
      const res = await fetch(`${base_url}/admin/pegawai/store`, {
        method: "POST",
        body: formData,
      });
      const result = await res.json();
      console.log(result);

      // === Jika validasi gagal ===
      if (result.status === "error" && result.errors) {
        for (const [field, messages] of Object.entries(result.errors)) {
          const input = form.querySelector(`[name="${field}"]`);
          if (input) {
            const div = document.createElement("div");
            div.className = "error-message";
            div.innerHTML = `<small style="color:red;font-style:italic;font-size:0.85em;">** ${messages.join(
              ", "
            )} **</small>`;
            input.insertAdjacentElement("afterend", div);
          }
        }
        return;
      }

      // === Jika sukses tambah data ===
      if (result.status === "success") {
        console.log(" Response sukses dari server:", result);

        const pegawai_id = form.querySelector('input[name="pegawai_id"]').value;
        if (pegawai_id) {
          // EDIT MODE → update row yang sudah ada
          updateRow(result.data);
        } else {
          // ADD MODE → tambah row baru
          fetchPegawaiData(currentPage);
        }

        form.querySelector("[name='pegawai_id']").value = ""; // kosongkan id
        form.reset();
        modal.querySelector(".modal-inner").scrollTop = 0;
        modal.style.display = "none";

        Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: "Data pegawai berhasil ditambahkan.",
          timer: 2000,
          showConfirmButton: false,
        });
      } else {
        console.log("Response error dari server:", result);
        Swal.fire({
          icon: "error",
          title: "Gagal!",
          text: result.message || "Terjadi kesalahan saat menyimpan pegawai.",
        });
      }
    } catch (err) {
      console.error(err);
      Swal.fire({
        icon: "error",
        title: "Kesalahan!",
        text: "Terjadi kesalahan jaringan atau server.",
      });
    }
  });

  // Edit Pegawai
  document.addEventListener("click", function (e) {
    if (e.target.classList.contains("btn-edit-pegawai")) {
      const id = e.target.dataset.id;

      // 1️⃣ ambil data dari server via AJAX
      fetch(`${base_url}/admin/pegawai/get/${id}`)
        .then((res) => res.json())
        .then((result) => {
          console.log(result);
          if (result.status === "success") {
            const data = result.data;

            // 2️⃣ buka modal
            modal.style.display = "flex";

            // Sembunyikan password saat edit
            if (passwordField) passwordField.style.display = "none";
            if (passwordLabel) passwordLabel.style.display = "none";

            // 3️⃣ isi semua input
            form.querySelector('input[name="pegawai_id"]').value = data.id;
            form.querySelector('input[name="nama"]').value = data.nama;
            form.querySelector('input[name="nip"]').value = data.nip;
            form.querySelector('select[name="unit_id"]').value = data.unit_id;
            form.querySelector('select[name="jabatan_id"]').value =
              data.jabatan_id;
            form.querySelector('select[name="role"]').value = data.role;
            form.querySelector('input[name="email"]').value = data.email;
            form.querySelector('input[name="telepon"]').value = data.telepon;
            form.querySelector('input[name="tanggal_lahir"]').value =
              data.tanggal_lahir;
            form.querySelector('textarea[name="alamat"]').value = data.alamat;
            form.querySelector('select[name="status_aktif"]').value =
              data.status_aktif;

            // Preview foto
            const fotoPath = data.foto
              ? base_url + "/" + data.foto
              : base_url + "/uploads/pegawai/default_img.jpg";
            if (fotoPreview) fotoPreview.src = fotoPath;

            // Update preview saat user pilih file baru
            form
              .querySelector('input[name="foto"]')
              .addEventListener("change", function () {
                const file = this.files[0];
                if (file) {
                  const reader = new FileReader();
                  reader.onload = function (e) {
                    fotoPreview.src = e.target.result;
                  };
                  reader.readAsDataURL(file);
                }
              });
          } else {
            alert("Gagal mengambil data pegawai.");
          }
        });
    }
  });

  // Hapus Pegawai
  document.addEventListener("click", async function (e) {
    if (e.target.classList.contains("btn-delete-pegawai")) {
      const id = e.target.dataset.id;

      const konfirmasi = await Swal.fire({
        icon: "warning",
        title: "Yakin hapus data ini?",
        text: "Data tidak dapat dikembalikan setelah dihapus!",
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus",
        cancelButtonText: "Batal",
      });

      if (!konfirmasi.isConfirmed) return;

      try {
        const res = await fetch(`${base_url}/admin/pegawai/delete/${id}`, {
          method: "DELETE",
        });
        const result = await res.json();

        if (result.status === "success") {
          // Hapus baris dari tabel
          const row = e.target.closest("tr");
          if (row) row.remove();
          // renumberRows();
          // Ambil ulang data halaman sekarang
          fetchPegawaiData(currentPage).then((res) => {
            if (res.data.length === 0 && currentPage > 1) {
              currentPage--;
              fetchPegawaiData(currentPage);
            }
          });

          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: result.message,
            timer: 1500,
            showConfirmButton: false,
          });
        } else {
          Swal.fire({
            icon: "error",
            title: "Gagal!",
            text: result.message || "Tidak dapat menghapus data pegawai.",
          });
        }
      } catch (err) {
        console.error(err);
        Swal.fire({
          icon: "error",
          title: "Kesalahan!",
          text: "Terjadi kesalahan jaringan atau server.",
        });
      }
    }
  });

  // search pegawai ajax

  // Variabel untuk navigasi suggestion
  let currentFocus = -1;

  // Fungsi umum untuk fetch data pegawai
  async function fetchPegawai(url) {
    try {
      const res = await fetch(url);
      const result = await res.json();
      return result;
    } catch (err) {
      console.error("Fetch error:", err);
      return { status: "error", data: [] };
    }
  }

  // Fungsi render tabel
  function renderPegawaiTable(data) {
    tableBody.innerHTML = "";
    if (data.status === "success" && data.data.length > 0) {
      data.data.forEach((p, index) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
        <td>${index + 1}</td>
        <td>${p.nip}</td>
        <td>${p.nama}</td>
        <td>${p.nama_unit ?? "-"}</td>
        <td>
          <button class="btn-edit-pegawai" data-id="${p.id}">Edit</button>
          <button class="btn-delete-pegawai" data-id="${p.id}">Hapus</button>
        </td>
      `;
        tableBody.appendChild(tr);
      });
    } else {
      tableBody.innerHTML = `
      <tr class="empty">
        <td colspan="5" class="text-center">Data tidak ditemukan</td>
      </tr>
    `;
    }
  }

  // Fungsi render suggestions (autocomplete)
  function renderSuggestions(data, keyword) {
    suggestions.innerHTML = "";
    currentFocus = -1;

    // jika tidak ada data sama sekali
    if (data.status !== "success" || !Array.isArray(data.data)) {
      suggestions.style.display = "none";
      return;
    }

    const filtered = data.data.filter((p) =>
      p.nama.toLowerCase().includes(keyword.toLowerCase())
    );

    // kalau hasil filter kosong
    if (filtered.length === 0) {
      const div = document.createElement("div");
      div.className = "suggestion-item no-result";
      div.textContent = "Tidak ada data ditemukan";
      suggestions.appendChild(div);
      suggestions.style.display = "block";
      return;
    }

    // render daftar suggestion seperti biasa
    filtered.forEach((p) => {
      const regex = new RegExp(`(${keyword})`, "gi");
      const namaHighlighted = p.nama.replace(regex, "<strong>$1</strong>");
      const div = document.createElement("div");
      div.className = "suggestion-item";
      div.innerHTML = namaHighlighted;

      div.addEventListener("click", () => {
        inputPegawai.value = p.nama;
        closeSuggestions();
        inputPegawai.focus();
        const len = inputPegawai.value.length;
        inputPegawai.setSelectionRange(len, len);
      });

      suggestions.appendChild(div);
    });

    suggestions.style.display = "block";
  }

  // Fungsi bantu untuk menandai item aktif
  function addActive(items) {
    if (!items) return false;
    removeActive(items);
    if (currentFocus >= items.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = items.length - 1;
    items[currentFocus].classList.add("active");
    inputPegawai.value = items[currentFocus].innerText; // isi otomatis input
  }

  // Fungsi hapus tanda aktif
  function removeActive(items) {
    items.forEach((item) => item.classList.remove("active"));
  }

  // Tutup semua suggestion
  function closeSuggestions() {
    suggestions.innerHTML = "";
    suggestions.style.display = "none";
  }

  // Event: Ketika user mengetik
  inputPegawai.addEventListener("input", async () => {
    const keyword = inputPegawai.value.trim();
    const unitId = unitSelectSekolah.value;

    // Jika user mengetik sesuatu
    if (keyword.length > 0) {
      searchIcon.classList.remove("fa-search");
      searchIcon.classList.add("fa-times", "active");

      const url = `${base_url}/admin/pegawai/search_table?searchPegawai=${encodeURIComponent(
        keyword
      )}&unit_id_sekolah=${unitId}`;
      const result = await fetchPegawai(url);
      // console.log(result.debug);
      renderSuggestions(result, keyword);
      renderPagination(result.total, result.page, result.limit);
    }
    // Jika input dikosongkan
    else {
      searchIcon.classList.remove("fa-times", "active");
      searchIcon.classList.add("fa-search");
      closeSuggestions();

      // 🔁 Ambil ulang semua data berdasarkan unit
      const url = `${base_url}/admin/pegawai/search_table?unit_id_sekolah=${unitId}`;
      const result = await fetchPegawai(url);
      renderPegawaiTable(result);
    }
  });


  // 🧭 Event: Navigasi keyboard (panah + enter)
  inputPegawai.addEventListener("keydown", async (e) => {
    const items = suggestions.querySelectorAll(".suggestion-item");
    if (!items.length) return;

    if (e.key === "ArrowDown") {
      currentFocus++;
      addActive(items);
    } else if (e.key === "ArrowUp") {
      currentFocus--;
      addActive(items);
    } else if (e.key === "Enter") {
      e.preventDefault();
      // Ambil nama di input
      const keyword = inputPegawai.value.trim();
      const unitId = unitSelectSekolah.value;

      // Fetch tabel berdasarkan input
      const url = `${base_url}/admin/pegawai/search_table?searchPegawai=${encodeURIComponent(
        keyword
      )}&unit_id_sekolah=${unitId}`;
      const result = await fetchPegawai(url);
      renderPegawaiTable(result);

      // Jangan tutup suggestions
      // currentFocus tetap, agar user bisa navigasi lagi
    } else if (e.key === "Escape") {
      closeSuggestions();
      inputPegawai.blur(); // opsional
    }
  });


  // 🔙 Event: Klik ikon search (reset input)
  searchIcon.addEventListener("click", async () => {
    if (searchIcon.classList.contains("fa-times")) {
      inputPegawai.value = "";
      inputPegawai.focus();
      searchIcon.classList.remove("fa-times", "active");
      searchIcon.classList.add("fa-search");
      closeSuggestions();

      const unitId = unitSelectSekolah.value;
      const url = `${base_url}/admin/pegawai/search_table?unit_id_sekolah=${unitId}`;
      const result = await fetchPegawai(url);
      renderPegawaiTable(result);
      renderPagination(result.total, result.page, result.limit);
    }
  });

  // 🔍 Event: Submit form pencarian manual
  formSearch.addEventListener("submit", async (e) => {
    e.preventDefault();
    const keyword = inputPegawai.value.trim();
    const unitId = unitSelectSekolah.value;

    const url = `${base_url}/admin/pegawai/search_table?searchPegawai=${encodeURIComponent(
      keyword
    )}&unit_id_sekolah=${unitId}`;

    const result = await fetchPegawai(url);
    renderPegawaiTable(result);
    renderPagination(result.total, result.page, result.limit);
  });

  function addActive(items) {
    if (!items) return false;
    removeActive(items);

    if (currentFocus >= items.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = items.length - 1;

    const activeItem = items[currentFocus];
    activeItem.classList.add("active");

    // isi otomatis input (tetap lakukan ini, atau hapus kalau tidak mau)
    inputPegawai.value = activeItem.innerText;

    // Pastikan item aktif terlihat di dalam container suggestions
    // 'nearest' menjaga agar scroll bergerak seminimal mungkin
    // gunakan behavior: 'auto' jika tidak mau animasi
    activeItem.scrollIntoView({
      behavior: "auto",
      block: "nearest",
      inline: "nearest",
    });

    return true;
  }

  document.addEventListener("click", (e) => {
    if (!suggestions.contains(e.target) && e.target !== inputPegawai) {
      closeSuggestions();
    }
  });

  async function fetchPegawaiData(page = 1) {
    currentPage = page;
    const keyword = inputPegawai.value.trim();
    const unitId = unitSelectSekolah.value;

    const url = `${base_url}/admin/pegawai/search_table?searchPegawai=${encodeURIComponent(
      keyword
    )}&unit_id_sekolah=${unitId}&page=${page}`;
    const result = await fetchPegawai(url);
    renderPegawaiTable(result);
    renderPagination(result.total, result.page, result.limit);

    return result; // <-- pastikan ini ada
  }

  function renderPagination(total, page, limit) {
    const totalPages = Math.ceil(total / limit);
    const paginationContainer = document.getElementById("paginationPegawai");
    paginationContainer.innerHTML = "";

    if (totalPages <= 1) return;

    // Tombol Prev
    const prevBtn = document.createElement("button");
    prevBtn.textContent = "Prev";
    prevBtn.className = "page-btn prev-btn";
    if (page === 1) prevBtn.disabled = true;
    else {
      prevBtn.addEventListener("click", () => fetchPegawaiData(page - 1));
    }
    paginationContainer.appendChild(prevBtn);

    // Tombol angka
    for (let i = 1; i <= totalPages; i++) {
      const btn = document.createElement("button");
      btn.textContent = i;
      btn.className = "page-btn";
      if (i === page) btn.classList.add("active");
      btn.addEventListener("click", () => fetchPegawaiData(i));
      paginationContainer.appendChild(btn);
    }

    // Tombol Next
    const nextBtn = document.createElement("button");
    nextBtn.textContent = "Next";
    nextBtn.className = "page-btn next-btn";
    if (page === totalPages) nextBtn.disabled = true;
    else {
      nextBtn.addEventListener("click", () => fetchPegawaiData(page + 1));
    }
    paginationContainer.appendChild(nextBtn);
  }
  
  fetchPegawaiData(1);
});

  