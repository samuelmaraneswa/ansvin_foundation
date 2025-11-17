// pegawaiTable.js
import { getJSON } from "../../utils/api.js";
import { renderPagination } from "./pagination.js";

/**
 * Fetch data pegawai dan render tabel.
 * Bisa dipanggil dari mana saja (search, pagination, dll)
 */
export async function fetchPegawaiData(keyword = "", unitId = "", page = 1) {
  const tableBody = document.querySelector("#adminUnitTable tbody"); 
  
  if (!tableBody) {
    console.warn("Elemen tabel pegawai tidak ditemukan.");
    return;
  }

  try {
    const url = `${base_url}/admin/pegawai/search_table?searchPegawai=${encodeURIComponent(
      keyword
    )}&unit_id_sekolah=${unitId}&page=${page}`; 

    // console.log("🌐 Fetch URL:", url);
    const result = await getJSON(url);

    tableBody.innerHTML = "";

    if (
      result?.status === "success" &&
      Array.isArray(result.data) &&
      result.data.length > 0
    ) {
      result.data.forEach((p, index) => {
        const tr = document.createElement("tr");
        tr.innerHTML = `
          <td>${index + 1}</td>
          <td>${p.nip ?? "-"}</td>
          <td>${p.nama.split(" ")[0] ?? "-"}</td>
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

    // render pagination
    renderPagination(result.total, result.page, result.limit, keyword, unitId);
  } catch (err) {
    console.error("Gagal mengambil data pegawai:", err);
    tableBody.innerHTML = `
      <tr>
        <td colspan="5" class="text-center text-danger">
          Gagal memuat data. Periksa koneksi atau server.
        </td>
      </tr>
    `;
  }
}

/**
 * Update satu baris (tanpa reload seluruh tabel)
 * Dipanggil setelah user berhasil update data
 */
export function updateRowInTable(data) {
  // console.log("🔍 updateRowInTable called:", data);
  const button = document.querySelector(
    `button.btn-edit-pegawai[data-id="${data.id}"]`
  );
  console.log("🔎 found button:", button);

  const row = document
    .querySelector(`button.btn-edit-pegawai[data-id="${data.id}"]`)
    ?.closest("tr");
  if (!row) return;

  row.children[1].textContent = data.nip ?? "-";
  row.children[2].textContent = data.nama ? data.nama.split(" ")[0] : "-"; 
  row.children[3].textContent = data.nama_unit ?? "-";

  row.classList.add("row-updated");
  setTimeout(() => row.classList.remove("row-updated"), 1000);
}
