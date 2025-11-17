// public/assets/js/pendaftaran/unit/unitCalonSiswaStatus.js
import { postForm } from "../../utils/api.js";
import { alertSuccess, alertError } from "../../utils/alert.js";
import { renderUnitCalonSiswaTable } from "./unitCalonSiswaTable.js";

/**
 * Kirim permintaan ubah status calon siswa (untuk admin_unit)
 * @param {FormData} formData
 * @param {Function} onSuccess - callback jika berhasil
 * @param {Function} onError - callback jika gagal
 */
export async function updateUnitCalonSiswaStatus(formData, onSuccess, onError) {
  try {
    // Ambil slug unit dari URL, contoh: /unit/smp/calon-siswa
    const parts = window.location.pathname.split("/");
    const unitIndex = parts.indexOf("unit");
    const slug = unitIndex !== -1 ? parts[unitIndex + 1] : null;

    const url = `${base_url}/unit/${slug}/calon-siswa/updateStatus`;
    const result = await postForm(url, formData);
    console.log(result);

    if (result.status === "success") {
      alertSuccess(result.message || "Status pembayaran berhasil diperbarui.");
      renderUnitCalonSiswaTable(); // 🔄 refresh tabel
      if (onSuccess) onSuccess(result);
    } else {
      alertError(result.message || "Gagal memperbarui status.");
      if (onError) onError(result);
    }
  } catch (err) {
    console.error("Error update calon siswa status:", err);
    alertError("Terjadi kesalahan koneksi server.");
    if (onError) onError(err);
  }
}
