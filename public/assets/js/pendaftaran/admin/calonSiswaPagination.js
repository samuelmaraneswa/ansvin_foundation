// Fungsi untuk merender tombol pagination dan menambahkan event click
export function renderCalonSiswaPagination(
  paginationData,
  onPageChange,
  keyword = "",
  limit = 5
) {
  const paginationBox = document.getElementById("paginationCalonSiswa");
  if (!paginationBox || !paginationData) return;

  const { page: current, total_pages } = paginationData;
  let html = "";

  // Tombol prev
  if (current > 1)
    html += `<button class="page-btn prev" data-page="${
      current - 1
    }">⟨</button>`;

  // Tombol nomor halaman
  for (let i = 1; i <= total_pages; i++) {
    html += `<button class="page-btn ${
      i === current ? "active" : ""
    }" data-page="${i}">${i}</button>`;
  }

  // Tombol next
  if (current < total_pages)
    html += `<button class="page-btn next" data-page="${
      current + 1
    }">⟩</button>`;

  paginationBox.innerHTML = html;

  // Event listener setiap tombol halaman
  paginationBox.querySelectorAll(".page-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const selectedPage = parseInt(btn.dataset.page);
      if (typeof onPageChange === "function") {
        onPageChange(selectedPage, keyword, limit);
      }
    });
  });
}