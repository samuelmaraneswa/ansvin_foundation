export function renderGuruMapelPagination(pagination, onPageChange){
  const paginationBox = document.getElementById("paginationUnitSlug");
  if(!pagination || !paginationBox) return;

  const current = parseInt(pagination.page);
  const totalPages = parseInt(pagination.total_pages);

  if(totalPages <= 1){
    paginationBox.innerHTML = "";
    return;
  }

  let html = "";

  if(current > 1){
    html += `<button class="page-btn prev" data-page="${current - 1}">⟨</button>`;
  }

  for(let i = 1; i <= totalPages; i++){
    html += `<button class="page-btn ${i === current ? "active" : ""}" data-page="${i}">${i}</button>`;
  }

  if(current < totalPages){
    html += `<button class="page-btn next" data-page="${current + 1}">⟩</button>`
  }

  paginationBox.innerHTML = html;

  paginationBox.querySelectorAll(".page-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const selectedPage = parseInt(btn.dataset.page);
      if(typeof onPageChange === "function"){
        onPageChange(selectedPage);
      }
    })
  })
}