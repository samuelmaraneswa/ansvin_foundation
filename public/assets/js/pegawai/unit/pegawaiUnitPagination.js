// pegawaiUnitPagination.js
import { fetchUnitPegawaiData } from "./pegawaiUnitTable.js";

/**
 * Render tombol pagination untuk Admin Unit.
 * @param {number} total - Total data pegawai unit.
 * @param {number} page - Halaman aktif saat ini.
 * @param {number} limit - Jumlah data per halaman.
 * @param {string} keyword - Keyword pencarian aktif (opsional).
 */
export function renderUnitPagination(total, page, limit, keyword = "") {
  const paginationContainer = document.getElementById("paginationUnitPegawai");
  if (!paginationContainer) return;

  paginationContainer.innerHTML = "";

  const totalPages = Math.ceil(total / limit);
  if (totalPages <= 1) return;

  // Tombol Prev
  const prevBtn = document.createElement("button");
  prevBtn.textContent = "Prev";
  prevBtn.className = "page-btn prev-btn";
  prevBtn.disabled = page === 1;
  prevBtn.addEventListener("click", () => {
    fetchUnitPegawaiData(keyword, page - 1);
  });
  paginationContainer.appendChild(prevBtn);

  // Tombol angka
  const startPage = Math.max(1, page - 2);
  const endPage = Math.min(totalPages, page + 2);
  for (let i = startPage; i <= endPage; i++) {
    const btn = document.createElement("button");
    btn.textContent = i;
    btn.className = "page-btn";
    if (i === page) btn.classList.add("active");
    btn.addEventListener("click", () => {
      fetchUnitPegawaiData(keyword, i);
    });
    paginationContainer.appendChild(btn);
  }

  // Tombol Next
  const nextBtn = document.createElement("button");
  nextBtn.textContent = "Next";
  nextBtn.className = "page-btn next-btn";
  nextBtn.disabled = page === totalPages;
  nextBtn.addEventListener("click", () => {
    fetchUnitPegawaiData(keyword, page + 1);
  });
  paginationContainer.appendChild(nextBtn);
}
 