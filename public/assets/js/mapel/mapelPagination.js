import { mapelTabel } from "./mapelTable.js";

export function renderPagination(pagination, keyword, limit){
  const paginationBox = document.getElementById("paginationMapel")
  if(!paginationBox) return;

  const {page, total_pages} = pagination;

  if(!pagination || total_pages <= 1){
    paginationBox.innerHTML = "";
    return;
  }

  let html = "";

  if(page > 1){
    html += `<button class="page-btn prev" data-page="${page - 1}">⟨</button>`;
  }

  for(let i = 1; i <= total_pages; i++){
    html += `
      <button class="page-btn ${i === page ? "active" : ""}" data-page="${i}">${i}</button>
    `
  }

  if(page < total_pages){
    html += `<button class="page-btn next" data-page="${page + 1}">⟩</button>`;
  }

  paginationBox.innerHTML = html;

  paginationBox.querySelectorAll(".page-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const selectedPage = parseInt(btn.dataset.page);
      mapelTabel(keyword, selectedPage, limit)
    })
  }) 
}