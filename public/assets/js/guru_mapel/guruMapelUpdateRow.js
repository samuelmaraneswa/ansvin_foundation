export function updateGuruMapelRow(data){
  const row = document.querySelector(`tr[data-id="${data.id}"]`)
  if(!row) return;

  row.querySelector("td:nth-child(2)").textContent = data.guru;
  row.querySelector("td:nth-child(3)").textContent = data.mapel;
  row.querySelector("td:nth-child(4)").textContent = data.tahun;

  row.classList.add("updated-row");

  setTimeout(() => {
    row.classList.remove("updated-row");
  }, 1000);
}