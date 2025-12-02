import { initJadpelSearch } from "./jadpelSearch.js";
import { initJadpelSubmit } from "./jadpelSubmit.js";
import { jadpelTable } from "./jadpelTable.js";
import { initJadpelModal, openJadpelAddModal } from "./modalJadpel.js";


function initJadwalPage(){
  if(!window.location.pathname.includes("/jadpel")) return;

  initJadpelModal();
  initJadpelSubmit();
  initJadpelSearch();

  const btnAdd = document.getElementById("btnJadpelAdd");
  if(btnAdd){
    btnAdd.addEventListener("click", () => {
      openJadpelAddModal();
    })
  }

  jadpelTable();
}

document.addEventListener("DOMContentLoaded", initJadwalPage);
