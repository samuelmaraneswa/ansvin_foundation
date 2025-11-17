// public/assets/js/pendaftaran/unit/unitCalonSiswaTable.js
export function renderUnitCalonSiswaTable() {
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
  fetch(`${base_url}/unit/${slug}/calon-siswa/fetchAll`)
    .then((res) => res.json())
    .then((result) => {
      if (result.status !== "success" || !result.data.length) {
        tableBody.innerHTML = `
          <tr>
            <td colspan="9" class="text-center text-muted py-3">
              Tidak ada data calon siswa.
            </td>
          </tr>
        `;
        return;
      }

      // render isi tabel
      tableBody.innerHTML = result.data
        .map(
          (s, i) => `
          <tr>
            <td>${i + 1}</td>
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
