// mainPegawai.js (module)
import { initModalEvents} from "./modalPegawai.js";
import { initGenerateNip } from "./generateNip.js";
import { initSearchPegawai } from "./searchPegawai.js";
import { fetchPegawaiData } from "./pegawaiTable.js";
import { initPegawaiForm } from "./pegawaiForm.js";
import { initPegawaiEdit } from "./pegawaiEdit.js";
import { initPegawaiDelete } from "./pegawaiDelete.js" 

document.addEventListener("DOMContentLoaded", () => {
  if (!window.location.pathname.includes("/pegawai")) {
    return;
  }

  const isAdminPage = window.location.pathname.includes("/admin/pegawai");

  if (!isAdminPage) {
    return;
  }
  
  initModalEvents();
  initGenerateNip(base_url);
  initPegawaiForm();
  initPegawaiEdit();
  initPegawaiDelete();
  initSearchPegawai(base_url)
  fetchPegawaiData(); // ambil data awal
});


