import { initEditGuruMapel } from "./guruMapelEdit.js";
import { initHapusGuruMapel } from "./guruMapelHapus.js";
import { initModalGuruMapel } from "./guruMapelModal.js";
import { initGuruMapelSearch } from "./guruMapelSearch.js";
import { initSubmitGuruMapel } from "./guruMapelSubmit.js";
import { guruMapelTable } from "./guruMapelTable.js"

document.addEventListener("DOMContentLoaded", () => {
  if(!window.location.pathname.includes("/guru_mapel")) return;

  initModalGuruMapel();
  initSubmitGuruMapel();
  initEditGuruMapel();
  initHapusGuruMapel();
  initGuruMapelSearch();
  guruMapelTable();
})