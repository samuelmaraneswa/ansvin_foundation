// pegawaiUnitEdit.js
import { alertError, alertConfirm } from "../../utils/alert.js";
import { getJSON } from "../../utils/api.js";
import { openModalUnit } from "./modalPegawaiUnit.js";

/**
 * Inisialisasi event tombol edit pegawai untuk admin_unit
 * (Delegasi global: berlaku di seluruh dokumen)
 */
export function initUnitPegawaiEdit() {
  document.addEventListener("click", async (e) => {
    if (!e.target.classList.contains("btn-edit-unitPegawai")) return;

    const id = e.target.dataset.id;
    if (!id) return;

    // Ambil slug dari URL aktif (misal /unit/smp/pegawai)
    const slug = window.location.pathname.split("/")[2];
    const url = `${base_url}/unit/${slug}/pegawai/get/${id}`;

    try {
      const result = await getJSON(url);
      // console.log("🧾 Edit data pegawai unit:", result);
// console.log(result)
      if (result?.status === "success") {
        // buka modal dengan data pegawai
        openModalUnit("edit", result.data);
      } else {
        alertConfirm("Data pegawai tidak ditemukan.");
      }
    } catch (err) {
      console.error("Gagal mengambil data pegawai unit:", err);
      alertError("Terjadi kesalahan saat mengambil data pegawai.");
    }
  });
}
