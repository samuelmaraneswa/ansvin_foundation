// pegawaiForm.js
import { closeModal } from "./modalPegawai.js";
import { postForm } from "../../utils/api.js";
import { alertSuccess, alertError } from "../../utils/alert.js";
import { fetchPegawaiData, updateRowInTable } from "./pegawaiTable.js";

/**
 * Inisialisasi event submit form pegawai
 * (berfungsi untuk tambah & edit)
 */
export function initPegawaiForm() {
  const form = document.getElementById("formAddPegawai");
  if (!form) {
    console.warn("Form pegawai tidak ditemukan di DOM.");
    return;
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    // bersihkan pesan error lama
    form.querySelectorAll(".error-message").forEach((el) => el.remove());

    const formData = new FormData(form);
    const url = `${base_url}/admin/pegawai/store`;

    try {
      // kirim data
      const result = await postForm(url, formData);
      const pegawaiId = form.querySelector("[name='pegawai_id']").value;
      // console.log("pegawaiId:", pegawaiId); 
      // console.log("🧾 Submit result:", result);
      
      // === SUCCESS ===
      if (result?.status === "success") {
        // Kalau edit → update baris yang sama
        if (pegawaiId) {
          // console.log("🧾 Submit result:", result);
          updateRowInTable(result.data);
        } else {
          // Kalau tambah → ambil ulang data halaman pertama
          fetchPegawaiData();
        }

        // SweetAlert
        alertSuccess(result.message || "Data berhasil disimpan");
       
        // Tutup modal
        closeModal();
      }

      // === VALIDATION ERROR ===
      else if (result?.status === "error" && result.errors) {
        for (const [field, messages] of Object.entries(result.errors)) {
          const input = form.querySelector(`[name="${field}"]`);
          if (input) {
            const div = document.createElement("div");
            div.className = "error-message";
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
      console.error("Error submit form:", err);
      alertError("Terjadi kesalahan jaringan atau server.");
    }
  });
}
