import { alertConfirm } from "../utils/alert.js";
import { getUnitSlug } from "../utils/url.js";
import { guruMapelTable } from "./guruMapelTable.js";

const table = document.getElementById("unitGuruMapelTable");

export function initHapusGuruMapel(){
  if(!table) return;

  table.addEventListener("click", async (e) => {
    const btn = e.target.closest(".deleteSlug");
    if(!btn) return;

    const id = btn.dataset.id;
    console.log(id);

    // if(!confirm("Yakin ingin menghapus data ini?")) return;
    const resultConfirm = await alertConfirm();
    if(!resultConfirm.isConfirmed) return;

    const slug = getUnitSlug();

    const url = `${base_url}/unit/${slug}/guru_mapel/delete/${id}`
    fetch(url, {
      method: "POST"
    })
      .then(res => res.json())
      .then(result => {
        console.log(result)
        if(result.status === 'success'){
          const row = document.querySelector(`tr[data-id="${id}"]`);
          if(row) {
            row.classList.add("row-deleting");

            setTimeout(() => {
              row.remove();

              const rows = document.querySelectorAll("#unitGuruMapelTable tbody tr");

              // ambil keyword aktif
              const input = document.getElementById("searchInputGuruMapel");
              const keyword = input ? input.value.trim().split("-")[0].trim() : "";

              const page = parseInt(table.dataset.page) || 1;

              if(rows.length === 0 && page > 1){
                guruMapelTable(keyword, page - 1, 5);
              }else{
                guruMapelTable(keyword, page, 5);
              }
            }, 300);
          }   
        }
      })
      .catch(err => console.error(err))
  })
}