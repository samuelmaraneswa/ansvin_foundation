import { initUnitUbahStatusModal } from "./unitCalonSiswaModal.js";
import { renderUnitCalonSiswaTable } from "./unitCalonSiswaTable.js";

document.addEventListener("DOMContentLoaded", () => {
  if (!window.location.pathname.includes("/calon-siswa")) return;

  initUnitUbahStatusModal();
  renderUnitCalonSiswaTable();
});