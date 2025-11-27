import { clearErrors } from "../utils/clearErrors.js";

const mapelModal = document.getElementById("mapelModal");
const addBtn = document.getElementById("btnMapelAdd");
const formMapel = document.getElementById("formMapel")
const idMapel = formMapel ? formMapel.querySelector("#mapelId") : null;
const close = mapelModal ? mapelModal.querySelector(".fa-x") : null;

export function openModal(){
  if(!mapelModal) return;
  
  resetModal();

  mapelModal.classList.add("show")
  document.body.style.overflow = "hidden";
  document.documentElement.style.overflow = "hidden";

  clearErrors();
  mapelModal.addEventListener(
    "transitionend",
    () => {
      const namaMapel = document.querySelector("#namaMapel");
      if (namaMapel) namaMapel.focus();
    },
    { once: true }
  );
}

export function closeModal(){
  mapelModal.classList.remove("show")
  document.body.style.overflow = "auto"
  document.documentElement.style.overflow = "auto";
}

function resetModal(){
  formMapel.reset();
  if(idMapel) idMapel.value = "";

  document.querySelectorAll(".error-text").forEach(el => el.innerText = "");
}

export function initModalEvents(){
  addBtn.addEventListener("click", () => {openModal()})
  close.addEventListener("click", () => {closeModal()})

  document.addEventListener("keydown", (e) => {
    if(e.key === "Escape") closeModal();
  })

  mapelModal.addEventListener("click", (e) => {
    if(e.target === mapelModal) closeModal();
  })
}