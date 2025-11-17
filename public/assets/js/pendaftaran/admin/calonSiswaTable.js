import { renderCalonSiswaPagination } from "./calonSiswaPagination.js";

export function renderCalonSiswaTable(keyword = "", page = 1, limit = 5) {
  const tableBody = document.querySelector("#table-calon-siswa tbody");
  const paginationBox = document.getElementById("paginationCalonSiswa");
  if (!tableBody) return;

  // tampilkan loading
  tableBody.innerHTML = `
    <tr>
      <td colspan="8" class="text-center text-muted py-3">
        Memuat data calon siswa...
      </td>
    </tr>
  `;
  if (paginationBox) paginationBox.innerHTML = "";

  // tentukan endpoint (search / fetchAll)
  const url = keyword.trim()
    ? `${base_url}/admin/pendaftaran/search?keyword=${encodeURIComponent(
        keyword
      )}&page=${page}&limit=${limit}`
    : `${base_url}/admin/pendaftaran/fetchAll?page=${page}&limit=${limit}`;

  fetch(url)
    .then((res) => res.json())
    .then((result) => {
      if (result.status !== "success" || !result.data.length) {
        tableBody.innerHTML = `
          <tr>
            <td colspan="8" class="text-center text-muted py-3">
              Tidak ada data calon siswa.
            </td>
          </tr>
        `;
        if (paginationBox) paginationBox.innerHTML = "";
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
                  ? ""
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

      // render pagination pakai modul terpisah
      if (result.pagination) {
        renderCalonSiswaPagination(
          result.pagination,
          (selectedPage, keyword, limit) => {
            renderCalonSiswaTable(keyword, selectedPage, limit);
          },
          keyword,
          limit
        );
      }
    })
    .catch((err) => {
      console.error("Gagal memuat data:", err);
      tableBody.innerHTML = `
        <tr>
          <td colspan="8" class="text-center text-danger py-3">
            Gagal memuat data calon siswa.
          </td>
        </tr>
      `;
      if (paginationBox) paginationBox.innerHTML = "";
    });
}
