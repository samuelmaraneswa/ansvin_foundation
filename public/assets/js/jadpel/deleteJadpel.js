import { alertError } from "../utils/alert.js";
import { getUnitSlug } from "../utils/url.js";
import { jadpelTable } from "./jadpelTable.js";

export function deleteJadpel(id, page, limit, keyword = ""){
  const slug = getUnitSlug();
  const formData = new FormData();
  const csrf_token = document.querySelector("input[name='csrf_token']").value;

  formData.append("csrf_token", csrf_token);

  const row = document.querySelector(`#unitJadpelTable tr[data-id="${id}"]`);

  row.classList.add("row-deleting");

  fetch(`${base_url}/unit/${slug}/jadpel/delete/${id}`, {
    method: "POST",
    body: formData
  })
    .then(res => res.json())
    .then(result => {
      if(result.status === 'success'){
        setTimeout(() => {
          row.remove();
          
          const rows = document.querySelectorAll("#unitJadpelTable tbody tr");
          const newPage = rows.length === 0 && page > 1 ? page - 1 : page;

          jadpelTable(keyword, newPage, limit);
        }, 300);
      }else{
        alertError(result.message);
        row.classList.remove("row-deleting");
      }
    })
    .catch(err => console.error("Gagal menghapus data:", err));
}
