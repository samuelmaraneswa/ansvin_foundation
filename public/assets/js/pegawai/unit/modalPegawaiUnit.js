import { generateNipUnit } from "./generateNipUnit.js";

const modalUnit = document.getElementById("adminUnitModalDiv")
const btnAdd = document.getElementById("btn-unitPegawai-Add");
const btnClose = modalUnit ? modalUnit.querySelector(".closeUnit") : null
const formUnit = document.getElementById("formAddUnitPegawai")
const inputFileUnit = formUnit ? formUnit.querySelector('input[name="foto_unit"]') : null
const fotoPreviewUnit = formUnit ? formUnit.querySelector("#foto-preview_unit") : null
const modalTitle = modalUnit ? modalUnit.querySelector(".titlePegawai") : null

let currentModeUnit = "add";
let currentEditDataUnit = null;

function openModalUnit(mode = "add", data = null){
  currentModeUnit = mode;
  currentEditDataUnit = data;
  if (!modalUnit) return;

  modalUnit.classList.add("show");
  modalUnit.style.display = "block";
  document.body.style.overflow = "hidden";

  // view modal selalu diatas
  modalUnit.querySelector(".modalUnit-inner").scrollTop = 0;

  if (modalTitle)
    modalTitle.textContent = mode === "add" ? "Tambah Pegawai" : "Edit Pegawai";

  resetModalUnit();

  // 🔹 Generate NIP otomatis saat mode "add"
  if (mode === "add") {
    const slug = window.location.pathname.split("/")[2]; // ambil slug dari URL (/unit/{slug}/pegawai)
    generateNipUnit(base_url, slug);
  }

  // password show/hide logic
  const passwordFieldUnit = formUnit
    ? formUnit.querySelector('input[name="password_unit"]')
    : null;
  const passwordLabelUnit = passwordFieldUnit
    ? passwordFieldUnit.previousElementSibling
    : null;

  if (passwordFieldUnit) {
    if (mode === "add") {
      passwordFieldUnit.style.display = "block";
      if (passwordLabelUnit) passwordLabelUnit.style.display = "block";
      passwordFieldUnit.value = "123456";
    } else {
      passwordFieldUnit.style.display = "none";
      if (passwordLabelUnit) passwordLabelUnit.style.display = "none";
      passwordFieldUnit.value = "";
    }
  }

  if (mode === "edit" && data && formUnit) {
    formUnit.querySelector('[name="pegawaiUnit_id"]').value = data.id ?? "";

    // mapping antara nama key data server dan name input di form
    const mapping = {
      nipUnit: data.nip,
      namaUnit: data.nama,
      jabatan_id_unit: data.jabatan_id,
      email_unit: data.email,
      telepon_unit: data.telepon,
      tanggal_lahir_unit: data.tanggal_lahir,
      alamat_unit: data.alamat,
      status_aktif_unit: data.status_aktif,
    };

    for (const [key, value] of Object.entries(mapping)) {
      const input = formUnit.querySelector(`[name="${key}"]`);
      if (input && input.type !== "file") input.value = value ?? "";
    }

    if (data.foto && fotoPreviewUnit) {
      if (data.foto.includes("uploads/pegawai")) {
        fotoPreviewUnit.src = base_url + "/" + data.foto;
      } else {
        fotoPreviewUnit.src = base_url + `uploads/pegawai/${data.foto}`;
      }
    }
  }
}

function resetModalUnit(){
  if(!formUnit) return;

  formUnit.querySelectorAll(".error-message-unit").forEach((el) => el.remove());

  formUnit.reset()
  const fotoUnit = fotoPreviewUnit
  if (fotoUnit) fotoUnit.src = base_url + "/uploads/pegawai/default_img.jpg";
  const idFieldUnit = formUnit.querySelector('[name="pegawaiUnit_id"]');
  if (idFieldUnit) idFieldUnit.value = "";
  currentEditDataUnit = null;
}

function handleFotoPreviewUnit(event) {
  const fileUnit = event.target.files[0];
  if (fileUnit && fotoPreviewUnit) {
    const reader = new FileReader();
    reader.onload = (e) => (fotoPreviewUnit.src = e.target.result);
    reader.readAsDataURL(fileUnit);
  } else if (fotoPreviewUnit) {
    fotoPreviewUnit.src = base_url + "/uploads/pegawai/default_img.jpg";
  }
}

function closeModalUnit(){
  if(!modalUnit) return;
  modalUnit.classList.remove("show")
  modalUnit.style.display = "none"
  document.body.style.overflow = "auto"
  resetModalUnit();
}

function initModalUnitEvents(){
  if (btnAdd) btnAdd.addEventListener("click", () => openModalUnit("add"));
  if(btnClose) btnClose.addEventListener("click", closeModalUnit);
  window.addEventListener("click", (e) => {
    if(e.target === modalUnit) closeModalUnit();
  })
  window.addEventListener("keydown", (e) => {
    if(e.key === "Escape") closeModalUnit();
  });
  if(inputFileUnit) inputFileUnit.addEventListener("change", handleFotoPreviewUnit);
}

export {initModalUnitEvents, closeModalUnit, openModalUnit} 