import { loadGuruMapelDropdowns } from "./guruMapelDropdown.js";

const addBtn = document.getElementById("btnUnitSlugAdd");
const modalSlug = document.getElementById("unitGuruMapelModal");
const close = modalSlug ? modalSlug.querySelector(".fa-x") : null;
const formModal = modalSlug ? modalSlug.querySelector("#formGuruMapel") : null;
const idGuruMapel = modalSlug ? modalSlug.querySelector("#id") : null;
const modalTitle = modalSlug ? modalSlug.querySelector("#modal-title") : null;

export function openModal(){
  if(!addBtn || !close || !modalSlug) return;

  resetModal();
  
  modalSlug.classList.add("show");
  document.body.style.overflow = "hidden";
  document.documentElement.style.overflow = "hidden";

  loadGuruMapelDropdowns();
}

export function closeModal(){
  if(!close) return;

  modalSlug.classList.remove("show");
  document.body.style.overflow = "auto";
  document.documentElement.style.overflow = "auto";
}

export function initModalGuruMapel(){
  addBtn.addEventListener("click", () => {
    if(modalTitle) modalTitle.textContent = "Tambah Data"
    openModal()
  });

  close.addEventListener("click", () => {
    closeModal()
  });

  document.addEventListener("keydown", (e) => {
    if(e.key === "Escape") closeModal();
  })

  modalSlug.addEventListener("click", (e) => {
    if(e.target === modalSlug) closeModal();
  })
}

function resetModal(){
  formModal.reset();
  if(idGuruMapel) idGuruMapel.value = "";
}
