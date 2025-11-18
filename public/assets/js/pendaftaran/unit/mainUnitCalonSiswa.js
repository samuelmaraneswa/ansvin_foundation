import { initUnitUbahStatusModal } from "./unitCalonSiswaModal.js";
import { initUnitCalonSiswaSearch } from "./unitCalonSiswaSearch.js";
import { renderUnitCalonSiswaTable } from "./unitCalonSiswaTable.js";

document.addEventListener("DOMContentLoaded", () => {
  if (!window.location.pathname.includes("/calon-siswa")) return;

  initUnitUbahStatusModal();
  initUnitCalonSiswaSearch();
  renderUnitCalonSiswaTable();
});