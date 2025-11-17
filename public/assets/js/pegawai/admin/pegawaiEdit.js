// pegawaiEdit.js
import { alertConfirm, alertError } from "../../utils/alert.js";
import { getJSON } from "../../utils/api.js";
import { openModal } from "./modalPegawai.js";

/**
 * Inisialisasi event tombol edit pegawai
 * (Delegasi: berlaku untuk semua tombol .btn-edit-pegawai)
 */
export function initPegawaiEdit() {
  document.addEventListener("click", async (e) => {
    if (!e.target.classList.contains("btn-edit-pegawai")) return;

    const id = e.target.dataset.id;
    if (!id) return;

    try {
      const result = await getJSON(`${base_url}/admin/pegawai/get/${id}`);
      // console.log("🧾 Edit data result:", result);

      if (result?.status === "success") {
        openModal("edit", result.data);
      } else {
        alertConfirm("Pegawai tidak ditemukan di database.");
      }
    } catch (err) {
      console.error("Gagal mengambil data pegawai:", err);
      alertError("Terjadi kesalahan saat mengambil data pegawai.");
    }
  });
}
