// pagination.js
import { fetchPegawaiData } from "./pegawaiTable.js";

/**
 * Render tombol pagination dinamis.
 * @param {number} total - Total data.
 * @param {number} page - Halaman aktif saat ini.
 * @param {number} limit - Jumlah data per halaman.
 * @param {string} keyword - Keyword pencarian aktif.
 * @param {string|number} unitId - ID unit sekolah aktif.
 */
export function renderPagination(
  total,
  page,
  limit,
  keyword = "",
  unitId = ""
) {
  const paginationContainer = document.getElementById("paginationPegawai");
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
    // console.log("🧭 Pagination Click:", { keyword, unitId, page: page - 1 });
    fetchPegawaiData(keyword, unitId, page - 1);
  });
  paginationContainer.appendChild(prevBtn);

  // Tombol angka (hanya 5 terdekat agar tidak panjang)
  const startPage = Math.max(1, page - 2);
  const endPage = Math.min(totalPages, page + 2);
  for (let i = startPage; i <= endPage; i++) {
    const btn = document.createElement("button");
    btn.textContent = i;
    btn.className = "page-btn";
    if (i === page) btn.classList.add("active");

    btn.addEventListener("click", () => {
      // console.log("🧭 Pagination Click:", { keyword, unitId, page: i });
      fetchPegawaiData(keyword, unitId, i);
    });

    paginationContainer.appendChild(btn);
  }

  // Tombol Next
  const nextBtn = document.createElement("button");
  nextBtn.textContent = "Next";
  nextBtn.className = "page-btn next-btn";
  nextBtn.disabled = page === totalPages;
  nextBtn.addEventListener("click", () => {
    // console.log("🧭 Pagination Click:", { keyword, unitId, page: page + 1 });
    fetchPegawaiData(keyword, unitId, page + 1);
  });
  paginationContainer.appendChild(nextBtn);
}