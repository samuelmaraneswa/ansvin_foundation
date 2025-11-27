import { getUnitSlug } from "../utils/url.js";
import { renderGuruMapelPagination } from "./guruMapelPagination.js";

export function guruMapelTable(keyword = "", page = 1, limit = 5){
  const table = document.getElementById("unitGuruMapelTable");
  const tbody = table.querySelector("tbody");

  if(!tbody) return;

  table.dataset.page = page;

  const slug = getUnitSlug();
  const url = keyword.trim()
    ? `${base_url}/unit/${slug}/guru_mapel/search?keyword=${keyword}&page=${page}&limit=${limit}`
    : `${base_url}/unit/${slug}/guru_mapel/fetchAll?page=${page}&limit=${limit}`;

  fetch(url)
    .then(res => res.json())
    .then(result => {console.log(result);

      const paginationBox = document.getElementById("paginationUnitSlug");

      if(result.status === "success" && Array.isArray(result.data) && result.data.length > 0){
        tbody.innerHTML = result.data.map((gm, i) => {
          return `
            <tr data-id="${gm.id}">
              <td>${(page - 1) * limit + i + 1}</td>
              <td>${gm.guru}</td>
              <td>${gm.mapel}</td>
              <td>${gm.tahun}</td>
              <td>
                <button class="editSlug" data-id="${gm.id}">Edit</button>
                <button class="deleteSlug" data-id="${gm.id}">Delete</button>
              </td>
            </tr>
          `;
        }).join("");

        if(result.pagination){
          renderGuruMapelPagination(result.pagination, (selectedPage) => {
            const input = document.getElementById("searchInputGuruMapel");
            const kw = input ? input.value.trim().split("-")[0].trim() : "";
            guruMapelTable(kw, selectedPage, limit);
          })
        }else{
          paginationBox.innerHTML = ""
        }

      }else{
        tbody.innerHTML = `
          <tr>
            <td colspan="5" class="text-center">Tidak ada data guru mapel.</td>
          </tr>
        `;
      }
    })
    .catch(err => {
      console.error(err)
      alertError("Gagal memuat data. Perika koneksi");
        tbody.innerHTML = `
          <tr class="empty">
            <td colspan="5">Gagal memuat data</td>
          </tr>
        `;
    });
}