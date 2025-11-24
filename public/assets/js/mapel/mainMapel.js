import { initHapusMapel } from "./hapusMapel.js";
import { initEditMapel } from "./mapelEdit.js";
import { initMapelSearch } from "./mapelSearch.js";
import { initMapelSubmit } from "./mapelSubmit.js";
import { mapelTabel } from "./mapelTable.js";
import { initModalEvents } from "./modalMapel.js";

document.addEventListener("DOMContentLoaded", () => {
  if(!window.location.pathname.includes("/mapel")) return;

  initModalEvents();
  initEditMapel();
  initMapelSubmit();
  initHapusMapel();
  initMapelSearch();
  mapelTabel();
})