import { initUbahStatusModal } from "./calonSiswaModal.js";
import { initCalonSiswaSearch } from "./calonSiswaSearch.js";
import { renderCalonSiswaTable } from "./calonSiswaTable.js";

document.addEventListener("DOMContentLoaded", () => {
  if (!window.location.pathname.includes("/pendaftaran")) return;

  initUbahStatusModal();
  renderCalonSiswaTable();

  initCalonSiswaSearch((item) => {
    renderCalonSiswaTable(item.nama_lengkap)
  })
});