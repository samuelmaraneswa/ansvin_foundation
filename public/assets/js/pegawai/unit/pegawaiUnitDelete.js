// pegawaiUnitDelete.js
import { deleteData } from "../../utils/api.js";
import { alertSuccess, alertError, alertConfirm } from "../../utils/alert.js";
import { fetchUnitPegawaiData } from "./pegawaiUnitTable.js";

/**
 * Inisialisasi event tombol hapus pegawai (admin_unit)
 * Delegasi global agar berfungsi meski tabel di-render ulang
 */
export function initUnitPegawaiDelete() {
  document.addEventListener("click", async (e) => {
    if (!e.target.classList.contains("btn-delete-unitPegawai")) return;

    const id = e.target.dataset.id;
    if (!id) return;

    // Konfirmasi sebelum hapus
    const konfirmasi = await alertConfirm(
      "Yakin ingin menghapus pegawai ini? Data tidak dapat dikembalikan!"
    );

    if (!konfirmasi.isConfirmed) return;

    // Ambil slug dari URL
    const slug = window.location.pathname.split("/")[2];

    try {
      const result = await deleteData(
        `${base_url}/unit/${slug}/pegawai/delete/${id}`
      );
      console.log("🗑️ Hapus pegawai result:", result);

      if (result?.status === "success") {
        alertSuccess(result.message || "Data pegawai berhasil dihapus.");

        // Hapus baris langsung dari tabel
        const row = e.target.closest("tr");
        if (row) row.remove();

        // Jika tabel kosong, reload ulang
        const tableBody =
          document.getElementById("unitPegawaiTableBody") ||
          document.getElementById("adminUnitTableBody");

        if (tableBody && tableBody.children.length === 0) {
          fetchUnitPegawaiData();
        }
      } else {
        alertError(result?.message || "Gagal menghapus data pegawai.");
      }
    } catch (err) {
      console.error("❌ Error hapus pegawai unit:", err);
      alertError("Terjadi kesalahan jaringan atau server.");
    }
  });
}
