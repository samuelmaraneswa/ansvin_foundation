// pegawaiDelete.js
import { deleteData } from "../../utils/api.js";
import { alertSuccess, alertError, alertConfirm } from "../../utils/alert.js";
import { fetchPegawaiData } from "./pegawaiTable.js";

/**
 * Inisialisasi event tombol delete pegawai
 * (Delegasi global agar tetap berfungsi pada tabel dinamis)
 */
export function initPegawaiDelete() {
  document.addEventListener("click", async (e) => {
    if (!e.target.classList.contains("btn-delete-pegawai")) return;

    const id = e.target.dataset.id;
    if (!id) return;

    // Tampilkan konfirmasi sebelum hapus
    const konfirmasi = await alertConfirm( 
      "Yakin hapus data ini? Data tidak dapat dikembalikan!"
    );

    if (!konfirmasi.isConfirmed) return;

    try {
      const result = await deleteData(`${base_url}/admin/pegawai/delete/${id}`);
      console.log("🗑️ Delete result:", result);

      if (result?.status === "success") {
        alertSuccess(result.message || "Data pegawai berhasil dihapus.");

        // Hapus baris dari tabel tanpa reload penuh
        const row = e.target.closest("tr");
        if (row) row.remove();

        // Kalau tabel kosong, refresh ulang
        const tableBody = document.querySelector("#adminUnitTable tbody");
        if (tableBody && tableBody.children.length === 0) {
          fetchPegawaiData();
        }
      } else {
        alertError(result?.message || "Tidak dapat menghapus data pegawai.");
      }
    } catch (err) {
      console.error("Gagal menghapus data pegawai:", err);
      alertError("Terjadi kesalahan jaringan atau server.");
    }
  });
}
