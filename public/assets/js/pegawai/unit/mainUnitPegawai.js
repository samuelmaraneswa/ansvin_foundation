import { fetchUnitPegawaiData } from "./pegawaiUnitTable.js";
import { initModalUnitEvents } from "./modalPegawaiUnit.js";
import { initUnitPegawaiForm } from "./pegawaiUnitForm.js";
import { initUnitPegawaiEdit } from "./pegawaiUnitEdit.js"; 
import { initUnitPegawaiDelete } from "./pegawaiUnitDelete.js";
import { initUnitPegawaiSearch } from "./searchUnitPegawai.js";

document.addEventListener("DOMContentLoaded", () => {
  if (!window.location.pathname.includes("/pegawai")) return;
  
  initModalUnitEvents();
  initUnitPegawaiForm();
  initUnitPegawaiEdit();
  initUnitPegawaiDelete();
  initUnitPegawaiSearch(base_url);
  fetchUnitPegawaiData(); 
});
