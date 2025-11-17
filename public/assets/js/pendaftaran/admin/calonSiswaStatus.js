// public/assets/js/pendaftaran/admin/calonSiswaStatus.js
import { renderCalonSiswaTable } from "./calonSiswaTable.js";
import { postForm } from "../../utils/api.js";
import { alertSuccess, alertError } from "../../utils/alert.js"; 

/**
 * Kirim permintaan ubah status calon siswa ke server
 * @param {FormData} formData
 * @param {Function} [onSuccess]
 * @param {Function} [onError]
 */
export async function updateCalonSiswaStatus(formData, onSuccess, onError) {
  try {
    const result = await postForm(`${base_url}/admin/pendaftaran/updateStatus`, formData);

    if (result.status === "success") {
      alertSuccess(result.message || "Status berhasil diperbarui.");
      
      const currentKeyword =
        document.getElementById("searchInputCalonSiswa")?.value.trim() || "";
      renderCalonSiswaTable(currentKeyword);

      if (onSuccess) onSuccess(result);
    } else {
      alertError(result.message || "Gagal memperbarui status.");
      if (onError) onError(result);
    }
  } catch (err) {
    console.error("Error:", err);
    alertError("Terjadi kesalahan koneksi server.");
    if (onError) onError(err);
  }
}
