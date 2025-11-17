// public/assets/js/pendaftaran/unit/unitCalonSiswaModal.js
import { updateUnitCalonSiswaStatus } from "./unitCalonSiswaStatus.js";

export function initUnitUbahStatusModal() {
  const modal = document.getElementById("modal-status");
  const closeBtn = document.getElementById("close-modal");
  const cancelBtn = document.getElementById("cancel-modal");
  const idField = document.getElementById("status-id");
  const form = document.getElementById("form-ubah-status");

  const statusSelect = document.getElementById("status-bayar");
  const inputNominal = document.getElementById("input-nominal");
  const nominalInput = document.getElementById("nominal-bayar");

  // buka modal saat klik tombol ubah status
  document.addEventListener("click", (e) => {
    if (!e.target.classList.contains("btn-status")) return;

    const id = e.target.dataset.id;
    const currentStatus = e.target.dataset.status || "BELUM";
    idField.value = id;

    const select = document.getElementById("status-bayar");
    Array.from(select.options).forEach((opt) => (opt.disabled = false));

    // logika pembatasan status
    if (currentStatus === "BELUM") {
      select.querySelector('option[value="BELUM"]').disabled = true;
    } else if (currentStatus === "CICIL") {
      select.querySelector('option[value="BELUM"]').disabled = true;
      select.querySelector('option[value="CICIL"]').disabled = true;
    } else if (currentStatus === "LUNAS") {
      Array.from(select.options).forEach((opt) => (opt.disabled = true));
    }

    select.value = currentStatus;
    modal.style.display = "flex";
  });

  // tampilkan / sembunyikan input nominal
  statusSelect.addEventListener("change", () => {
    inputNominal.style.display =
      statusSelect.value === "CICIL" ? "block" : "none";
  });

  // format angka input nominal
  nominalInput.addEventListener("input", (e) => {
    let value = e.target.value.replace(/\D/g, "");
    e.target.value = value ? Number(value).toLocaleString("id-ID") : "";
  });

  // submit form ubah status
  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    if (nominalInput.value) {
      nominalInput.value = nominalInput.value.replace(/\./g, "");
    }

    const formData = new FormData(form);

    await updateUnitCalonSiswaStatus(formData, () => {
      closeModal();
    });
  });

  // tutup modal
  const closeModal = () => {
    modal.style.display = "none";
    nominalInput.value = "";
    inputNominal.style.display = "none";
  };

  closeBtn?.addEventListener("click", closeModal);
  cancelBtn?.addEventListener("click", closeModal);
  modal.addEventListener("click", (e) => {
    if (e.target === modal) closeModal();
  });
}
