// public/assets/js/pendaftaran/unit/unitCalonSiswaTable.js
export function renderUnitCalonSiswaTable(keyword = "", page = 1, limit = 5) {
  const tableBody = document.querySelector("#table-calon-siswa tbody");
  if (!tableBody) return;

  // tampilkan indikator loading
  tableBody.innerHTML = `
    <tr>
      <td colspan="9" class="text-center text-muted py-3">
        Memuat data calon siswa...
      </td>
    </tr>
  `;

  // ambil slug dari URL (contoh: /unit/smp/calon-siswa)
  const parts = window.location.pathname.split("/");
  const unitIndex = parts.indexOf("unit");
  const slug = unitIndex !== -1 ? parts[unitIndex + 1] : null;
  // fetch data calon siswa milik unit admin

  const url = keyword
    ? `${base_url}/unit/${slug}/calon-siswa/search?keyword=${encodeURIComponent(
        keyword
      )}&page=${page}&limit=${limit}`
    : `${base_url}/unit/${slug}/calon-siswa/fetchAll?page=${page}&limit=${limit}`;

  fetch(url)
    .then((res) => res.json())
    .then((result) => {console.log(result)
      if (result.status !== "success") {
        tableBody.innerHTML = `
          <tr><td colspan="9" class="text-center text-danger py-3">Terjadi kesalahan.</td></tr>
        `;
              return;
            }

            if (!result.data.length) {
              tableBody.innerHTML = `
          <tr><td colspan="9" class="text-center text-muted py-3">Tidak ada data calon siswa.</td></tr>
        `;

        document.getElementById("paginationCalonSiswaUnit").innerHTML = "";

        return;
      }

      // render isi tabel
      tableBody.innerHTML = result.data
        .map(
          (s, i) => `
          <tr>
            <td>${(page - 1) * limit + i + 1}</td>
            <td>${s.no_pendaftaran || "-"}</td>
            <td>${s.nama_lengkap || "-"}</td>
            <td>${s.status_pendaftaran || "-"}</td>
            <td>${s.status_bayar || "-"}</td>
            <td>${
              s.total_tagihan
                ? "Rp " + Number(s.total_tagihan).toLocaleString("id-ID")
                : "-"
            }</td>
            <td>${
              s.total_bayar
                ? "Rp " + Number(s.total_bayar).toLocaleString("id-ID")
                : "-"
            }</td>
            <td>${
              s.sisa_tagihan
                ? "Rp " + Number(s.sisa_tagihan).toLocaleString("id-ID")
                : "-"
            }</td>
            <td>
              <button class="btn btn-sm btn-info btn-detail" data-id="${s.id}">
                <i class="fas fa-eye"></i> Detail
              </button>
              ${
                s.status_bayar === "LUNAS"
                  ? "" // jika LUNAS, tidak tampilkan tombol ubah status
                  : `<button class="btn btn-sm btn-warning btn-status"
                            data-id="${s.id}"
                            data-status="${s.status_bayar}">
                      <i class="fas fa-edit"></i> Ubah Status
                    </button>`
              }
            </td>
          </tr>
        `
        )
        .join("");

        if (result.pagination && result.pagination.total_pages > 1) {
          renderPagination(result.pagination, keyword, limit);
        } else {
          document.getElementById("paginationCalonSiswaUnit").innerHTML = "";
        }
    })
    .catch((err) => {
      console.error("Gagal memuat data calon siswa unit:", err);
      tableBody.innerHTML = `
        <tr>
          <td colspan="9" class="text-center text-danger py-3">
            Gagal memuat data calon siswa.
          </td>
        </tr>
      `;
    });
}

function renderPagination(pagination, keyword, limit){
  const paginationBox = document.getElementById("paginationCalonSiswaUnit")
  if(!paginationBox) return;

  const {page, total_pages} = pagination;
  let html ="";

  if(page > 1){
    html += `<button class="page-btn-prev" data-page="${page - 1}">⟨</button>`;
  }

  for(let i = 1; i <= total_pages; i++){
    html += `<button class="page-btn ${i === page ? "active" : ""}" data-page="${i}">${i}</button>`;
  }

  if(page < total_pages){
    html += `<button class="page-btn-next" data-page="${page + 1}">⟩</button>`;
  }

  paginationBox.innerHTML = html;

  paginationBox.querySelectorAll("button").forEach((btn) => {
    btn.addEventListener("click", () => {
      const selectedPage = parseInt(btn.dataset.page);
      renderUnitCalonSiswaTable(keyword, selectedPage, limit)
    })
  })
}