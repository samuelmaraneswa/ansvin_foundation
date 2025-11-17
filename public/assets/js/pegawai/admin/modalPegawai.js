// modalPegawai.js
const modal = document.getElementById("adminUnitModal");
const btnAdd = document.getElementById("btn-pegawai-Add");
const btnClose = modal ? modal.querySelector(".close") : null;
const form = document.getElementById("formAddPegawai");
const inputFile = form ? form.querySelector('input[name="foto"]') : null;
const fotoPreview = form ? form.querySelector("#foto-preview") : null;
const modalTitle = modal ? modal.querySelector(".titlePegawai") : null;

let currentMode = "add";
let currentEditData = null; 

function openModal(mode = "add", data = null) {
  currentMode = mode;
  currentEditData = data;
  if (!modal) return;

  modal.classList.add("show");
  modal.style.display = "block";
  document.body.style.overflow = "hidden";

  // view modal selalu diatas
  modal.querySelector(".modal-inner").scrollTop = 0;

  if (modalTitle)
    modalTitle.textContent = mode === "add" ? "Tambah Pegawai" : "Edit Pegawai";

  resetModal();

  // password show/hide logic (example)
  const passwordField = form
    ? form.querySelector('input[name="password"]')
    : null;
  const passwordLabel = passwordField
    ? passwordField.previousElementSibling
    : null;
  if (passwordField) {
    if (mode === "add") {
      passwordField.style.display = "block";
      if (passwordLabel) passwordLabel.style.display = "block";
      passwordField.value = "123456";
    } else {
      passwordField.style.display = "none";
      if (passwordLabel) passwordLabel.style.display = "none";
      passwordField.value = "";
    }
  }

  if (mode === "edit" && data && form) {
    form.querySelector('[name="pegawai_id"]').value = data.id ?? "";
    for (const [key, value] of Object.entries(data)) {
      const input = form.querySelector(`[name="${key}"]`);
      if (input && input.type !== "file") input.value = value ?? "";
    }
    if (data.foto && fotoPreview) {
      if (data.foto.includes("uploads/pegawai")) {
        fotoPreview.src = base_url + "/" + data.foto;
      } else {
        fotoPreview.src = base_url + `/uploads/pegawai/${data.foto}`;
      }
    }
  }
}

function closeModal() {
  if (!modal) return;
  modal.classList.remove("show");
  modal.style.display = "none";
  document.body.style.overflow = "auto";
  resetModal();
}

function resetModal() {
  if (!form) return;

  // 🔹 Bersihkan semua pesan error lama
  form.querySelectorAll(".error-message").forEach((el) => el.remove());
  
  form.reset();
  const foto = fotoPreview;
  if (foto) foto.src = base_url + "/uploads/pegawai/default_img.jpg";
  const idField = form.querySelector('[name="pegawai_id"]');
  if (idField) idField.value = "";
  currentEditData = null;
}

function handleFotoPreview(event) {
  const file = event.target.files[0];
  if (file && fotoPreview) {
    const reader = new FileReader();
    reader.onload = (e) => (fotoPreview.src = e.target.result);
    reader.readAsDataURL(file);
  } else if (fotoPreview) {
    fotoPreview.src = base_url + "/uploads/pegawai/default_img.jpg";
  }
}

function initModalEvents() {
  if (btnAdd) btnAdd.addEventListener("click", () => openModal("add"));
  if (btnClose) btnClose.addEventListener("click", closeModal);
  window.addEventListener("click", (e) => {
    if (e.target === modal) closeModal();
  });
  window.addEventListener("keydown", (e) => {
    if (e.key === "Escape") closeModal();
  });
  if (inputFile) inputFile.addEventListener("change", handleFotoPreview);
}

export { initModalEvents, openModal, closeModal };
