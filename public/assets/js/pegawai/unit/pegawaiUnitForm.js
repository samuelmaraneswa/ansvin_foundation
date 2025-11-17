// pegawaiUnitForm.js
import { closeModalUnit } from "./modalPegawaiUnit.js";
import { postForm } from "../../utils/api.js";
import { alertSuccess, alertError } from "../../utils/alert.js";
import {
  fetchUnitPegawaiData,
  updateRowInUnitTable,
} from "./pegawaiUnitTable.js";
 
/**
 * Inisialisasi event submit form pegawai (admin_unit)
 * — berfungsi untuk tambah & edit data
 */
export function initUnitPegawaiForm() {
  const form = document.getElementById("formAddUnitPegawai");
  if (!form) {
    console.warn("Form pegawai unit tidak ditemukan di DOM.");
    return;
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    // Bersihkan pesan error lama
    form.querySelectorAll(".error-message-unit").forEach((el) => el.remove());

    const formData = new FormData(form);
    const slug = window.location.pathname.split("/")[2]; // ambil nama unit dari URL
    const url = `${base_url}/unit/${slug}/pegawai/store`;

    try {
      // Kirim data ke server
      const result = await postForm(url, formData);
      const pegawaiId = form.querySelector("[name='pegawaiUnit_id']").value;

      // === SUCCESS ===
      if (result?.status === "success") {
        if (pegawaiId) {
          console.log(result);
          // Mode edit → update row langsung
          updateRowInUnitTable(result.data);
        } else {
          // Mode tambah → refresh data tabel
          fetchUnitPegawaiData();
        }

        alertSuccess(result.message || "Data pegawai berhasil disimpan!");
        closeModalUnit();
      }

      // === VALIDATION ERROR ===
      else if (result?.status === "error" && result.errors) {
        for (const [field, messages] of Object.entries(result.errors)) {
          const input = form.querySelector(`[name="${field}"]`);
          if (input) {
            const div = document.createElement("div");
            div.className = "error-message-unit";
            div.innerHTML = `<small style="color:red;font-style:italic;">** ${messages.join(
              ", "
            )} **</small>`;
            input.insertAdjacentElement("afterend", div);
          }
        }
      }

      // === SERVER ERROR ===
      else {
        alertError(result.message || "Terjadi kesalahan saat menyimpan data.");
      }
    } catch (err) {
      console.error("Error submit form unit:", err);
      alertError("Terjadi kesalahan jaringan atau server.");
    }
  });
}
