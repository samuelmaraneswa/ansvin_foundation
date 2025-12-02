import { getUnitSlug } from "../utils/url.js";
import { loadGuruMapelOptions, loadKelasOptions } from "./jadpelDropdown.js";

let jadpelModal;
let modalTitle;
let formJadpel;

function getElements(){
  jadpelModal = document.getElementById("unitJadpelModal");
  modalTitle = document.getElementById("modal-jadpel-title");
  formJadpel = document.getElementById("formJadpel");
}

function clearErrors(){
  document.querySelectorAll("#unitJadpelModal .error-text").forEach(el => {
    el.textContent = "";
  });
}

function resetJadpelForm(){
  if(!formJadpel) return;
  formJadpel.reset();

  const idInput = document.getElementById("jadpel-id");
  if(idInput) idInput.value = "";

  clearErrors();
}

function initTimeMask() {
  document
    .querySelectorAll("input[name='jam_mulai'], input[name='jam_selesai']")
    .forEach((input) => {
      input.addEventListener("input", () => {
        input.value = input.value.replace(/[^0-9:]/g, "");
      });

      input.addEventListener("keyup", () => {
        if (input.value.length === 2 && !input.value.includes(":")) {
          input.value += ":";
        }
      });
    });
}

export function showJadpelModal(){
  if(!jadpelModal) return;
  jadpelModal.classList.add("show");

  document.body.style.overflow = "hidden";
  document.documentElement.style.overflow = "hidden";
}

export function hideJadpelModal(){
  if(!jadpelModal) return;
  jadpelModal.classList.remove("show");

  document.body.style.overflow = "auto";
  document.documentElement.style.overflow = "auto";
}

export function initJadpelModal(){
  getElements();
  if(!jadpelModal || !modalTitle || !formJadpel) return;

  const closeIcon = jadpelModal.querySelector(".modal-slug-top i");
  if(closeIcon){
    closeIcon.addEventListener("click", hideJadpelModal);
  }
  
  document.addEventListener("keydown", (e) => {
    if(e.key === "Escape"){
      hideJadpelModal();
    }
  })

  initTimeMask();
}

export function openJadpelAddModal(){
  resetJadpelForm();

  loadGuruMapelOptions();
  loadKelasOptions();

  if(modalTitle){
    modalTitle.textContent = "Tambah Jadwal";
  }
  showJadpelModal();
}

export async function openEditJadpelModal(id){
  resetJadpelForm();
  formJadpel.querySelector("#jadpel-id").value = id;

  // isi dropdown dulu (harus di panggil sebelum fetch detail)
  await loadGuruMapelOptions();
  await loadKelasOptions();

  if(modalTitle){
    modalTitle.textContent = "Edit Data";
  }

  const slug = getUnitSlug();

  // ambil detail dari server
  fetch(`${base_url}/unit/${slug}/jadpel/getDetail/${id}`)
    .then(res => res.json())
    .then(result => { console.log(result)
      if(result.status === "success"){
        fillJadpelForm(result.data);
      }
      showJadpelModal();
    }) 
    .catch(err => console.error("Gagal load detail jadpel:", err));
}

function fillJadpelForm(data){
  if(!formJadpel) return;
  const hari = data.hari.charAt(0).toUpperCase() + data.hari.slice(1).toLowerCase();

  formJadpel.querySelector("select[name='hari']").value = hari; 
  formJadpel.querySelector("#selectKelas").value = data.kelas_id;
  formJadpel.querySelector("#selectGuruMapel").value = data.guru_mapel_id;
  formJadpel.querySelector("input[name='jam_mulai']").value = data.jam_mulai;
  formJadpel.querySelector("input[name='jam_selesai']").value = data.jam_selesai;
}