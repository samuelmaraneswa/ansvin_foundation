// pegawaiUnitTable.js
import { getJSON } from "../../utils/api.js";
import { alertError } from "../../utils/alert.js";
import { renderUnitPagination } from "./pegawaiUnitPagination.js";

/**
 * Ambil seluruh data pegawai milik unit login (tanpa keyword & pagination)
 * Dipanggil saat halaman dimuat, dan setelah insert / edit
 */
export async function fetchUnitPegawaiData(keyword = "", page = 1) {
  const tableBody =
    document.getElementById("unitPegawaiTableBody") ||
    document.getElementById("adminUnitTableBody");

  if (!tableBody) {
    console.warn("Elemen tabel pegawai tidak ditemukan di halaman ini.");
    return;
  }

  // tampilkan indikator loading
  tableBody.innerHTML = `
    <tr>
      <td colspan="5" class="text-center">Memuat data pegawai...</td>
    </tr>
  `;

  try {
    // Ambil slug dari URL (misal /unit/smp/pegawai → slug = smp)
    const slug = window.location.pathname.split("/")[2];
    let url = "";
    
    if(keyword && keyword.trim().length > 0){
      url = `${base_url}/unit/${slug}/pegawai/search?keyword=${encodeURIComponent(
        keyword
      )}&page=${page}`;
    }else {
      url = `${base_url}/unit/${slug}/pegawai/fetchAll?page=${page}`;
    }

    // Ambil data dari server
    const result = await getJSON(url);
    tableBody.innerHTML = "";

    if (
      result?.status === "success" &&
      Array.isArray(result.data) &&
      result.data.length > 0
    ) {
      // Render semua baris data pegawai
      result.data.forEach((p, index) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${index + 1}</td>
          <td>${p.nip ?? "-"}</td>
          <td>${p.nama.split(" ")[0] ?? "-"}</td>
          <td>${p.nama_jabatan ?? "-"}</td>
          <td>
            <button class="btn-edit-unitPegawai" data-id="${p.id}">Edit</button>
            <button class="btn-delete-unitPegawai" data-id="${
              p.id
            }">Hapus</button>
          </td>
        `;
        tableBody.appendChild(tr);
      });

      // 🔹 Tambahkan pagination
      renderUnitPagination(result.total, result.page, result.limit, keyword);
    } else {
      // Kalau data kosong
      tableBody.innerHTML = `
        <tr class="empty">
          <td colspan="5" class="text-center">Belum ada data pegawai.</td>
        </tr>
      `;
    }
  } catch (err) {
    console.error("Gagal mengambil data pegawai unit:", err);
    alertError("Gagal memuat data pegawai. Periksa koneksi atau server.");
    tableBody.innerHTML = `
      <tr>
        <td colspan="5" class="text-center text-danger">
          Gagal memuat data.
        </td>
      </tr>
    `;
  }
}

/**
 * Update satu baris tabel (tanpa reload seluruh data)
 * Dipanggil setelah user berhasil melakukan edit data pegawai
 */
export function updateRowInUnitTable(data) {
  const button = document.querySelector(
    `button.btn-edit-unitPegawai[data-id="${data.id}"]`
  );
  const row = button?.closest("tr");
  if (!row) return;

  // Perbarui nilai kolom
  row.children[1].textContent = data.nip ?? "-";
  row.children[2].textContent = data.nama ? data.nama.split(" ")[0] : "-";
  row.children[3].textContent = data.nama_jabatan ?? "-";

  // Efek highlight sementara
  row.classList.add("row-updated");
  setTimeout(() => row.classList.remove("row-updated"), 1000);
}
