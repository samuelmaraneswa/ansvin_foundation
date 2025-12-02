export function renderJadpelPagination(paginationData, onPageChange, keyword = "", limit = 5){
  const paginationBox = document.getElementById("paginationJadpel");
  if(!paginationBox || !paginationData) return;

  const {page: current, total_pages} = paginationData;
  let html = "";

  html += `
    <button class="page-prev">⟨</button>

    <select id="selectJadpelPage"></select>
    <span class="page-info">/ ${total_pages}</span>

    <button class="page-next">⟩</button>
  `;

  // if(current > 1){
  //   html += `<button class="page-btn prev" data-page="${current - 1}">⟨</button>`
  // }

  // for(let i = 1; i <= total_pages; i++){
  //   html += `<button class="page-btn ${i === current ? "active" : ""}" data-page="${i}">${i}</button>`;
  // }

  // if(current < total_pages){
  //   html += `<button class="page-btn next" data-page="${current + 1}">⟩</button>`
  // }

  paginationBox.innerHTML = html;

  // paginationBox.querySelectorAll(".page-btn").forEach(btn => {
  //   btn.addEventListener("click", () => {
  //     const selectedPage = parseInt(btn.dataset.page);
  //     if(typeof onPageChange === "function"){
  //       onPageChange(selectedPage, keyword, limit);
  //     }
  //   })
  // })

  const select = document.getElementById("selectJadpelPage");
  for(let i = 1; i <= total_pages; i++){
    const option = document.createElement("option");
    option.value = i;
    option.textContent = i;
    if(i === current) option.selected = true;
    select.appendChild(option);
  }

  const prevBtn = paginationBox.querySelector(".page-prev");
  if(prevBtn){
    if(current === 1) prevBtn.disabled = true;
    prevBtn.addEventListener("click", () => {
      if(current > 1){
        onPageChange(current - 1, keyword, limit);
      }
    })
  }

  const nextBtn = paginationBox.querySelector(".page-next");
  if(nextBtn){
    if (current === total_pages) nextBtn.disabled = true;
    nextBtn.addEventListener("click", () => {
      if(current < total_pages){
        onPageChange(current + 1, keyword, limit);
      }
    })
  }

  select.addEventListener("change", () => {
    onPageChange(parseInt(select.value), keyword, limit);
  })
}