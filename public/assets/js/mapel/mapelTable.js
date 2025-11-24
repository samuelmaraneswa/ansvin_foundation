import { alertError } from "../utils/alert.js";
import { getUnitSlug } from "../utils/url.js";
import { renderPagination } from "./mapelPagination.js";

export function mapelTabel(keyword = "", page = 1, limit = 5){
  const tableBody = document.getElementById("tableMapel").querySelector("tbody");

  if(!tableBody) return;

  try{
    const slug = getUnitSlug(); 
    const url = keyword
      ? `${base_url}/unit/${slug}/mapel/search?keyword=${encodeURIComponent(keyword)}&page=${page}&limit=${limit}`
      : `${base_url}/unit/${slug}/mapel/fetchAll?page=${page}&limit=${limit}`;

    fetch(url)
      .then(res => res.json())
      .then(result => { console.log(result)
        if(result?.status === "success" && Array.isArray(result.data) && result.data.length > 0){
          // console.log("pagination:", result.pagination)
          tableBody.innerHTML = result.data.map((m, i) => {
            return `
              <tr data-id="${m.id}">
                <td>${(page - 1) * limit + (i + 1)}</td>
                <td>${m.nama_mapel}</td>
                <td>${m.kode_mapel}</td>
                <td>${m.tingkat_min}</td>
                <td>${m.tingkat_max}</td>
                <td>
                  <button class="editMapelBtn" data-id="${m.id}">Edit</button>
                  <button class="hapusMapelBtn" data-id="${m.id}">Hapus</button>
                </td>
              </tr>
            `;})
            .join("");
        }

        renderPagination(result.pagination, keyword, limit)
      })

      .catch(err => console.error(err))    
  }catch(err){
    console.error("Gagal mengambil data mapel", err);
    alertError("Gagal memuat data. Perika koneksi");
    tableBody.innerHTML = `
      <tr class="empty">
        <td colpsan="6">Gagal memuat data</td>
      </tr>
    `;
  }
}

export function updateRowInTable(id, updated){
  const row = document.querySelector(`tr[data-id="${id}"]`);
  if(!row) return;

  row.querySelector("td:nth-child(2)").textContent = updated.nama_mapel;
  row.querySelector("td:nth-child(3)").textContent = updated.kode_mapel;
  row.querySelector("td:nth-child(4)").textContent = updated.tingkat_min;
  row.querySelector("td:nth-child(5)").textContent = updated.tingkat_max;

  row.classList.add("row-updated");
  row.scrollIntoView({behavior: "smooth", block: "center"});
  setTimeout(() => {
    row.classList.remove("row-updated")
  }, 1000);
}

export function removeRowFromTable(id, keyword = "", page = 1, limit = 5){
  const row = document.querySelector(`tr[data-id="${id}"]`)
  if(!row) return;

  row.classList.add("row-deleting")

  setTimeout(() => {
    row.remove()

    const rows = document.querySelectorAll("#tableMapel tbody tr");
    
    if(rows.length === 0 && page > 1){
      mapelTabel(keyword, page - 1, limit);
    }else{
      mapelTabel(keyword, page, limit);
    }
  }, 1000)
}