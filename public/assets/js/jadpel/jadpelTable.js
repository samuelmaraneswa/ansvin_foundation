import { alertConfirm } from "../utils/alert.js";
import { getUnitSlug } from "../utils/url.js";
import { deleteJadpel } from "./deleteJadpel.js";
import { renderJadpelPagination } from "./jadpelPagination.js";
import { initJadpelModal, openEditJadpelModal, showJadpelModal } from "./modalJadpel.js";

export function jadpelTable(keyword = "", page = 1, limit = 5, onSuggestions = null){
  const tbody = document.querySelector("#unitJadpelTable tbody");
  if(!tbody) return;

  tbody.innerHTML = `
    <tr class="empty">
      <td colspan="8" class="text-center">Memuat data...</td>
    </tr>
  `;

  const slug = getUnitSlug();
  const url = keyword 
    ? `${base_url}/unit/${slug}/jadpel/search?keyword=${encodeURIComponent(keyword)}&page=${page}&limit=${limit}`
    : `${base_url}/unit/${slug}/jadpel/fetchAll?page=${page}&limit=${limit}`;

  fetch(url)
    .then(res => res.json())
    .then(result => { console.log(result)

      if(typeof onSuggestions === "function"){
        onSuggestions(result.data || []);
      }

      if(result.status !== "success" || !result.data.length){
        tbody.innerHTML = `
          <tr class="empty">
            <td colspan="8" class="text-center">Tidak ada data.</td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = result.data.map(
        (row, i) => `
        <tr data-id="${row.id}">
          <td>${(page - 1) * limit + i + 1} </td>
          <td>${row.hari}</td>
          <td>${row.kelas}</td>
          <td>${row.jam_mulai}</td>
          <td>${row.jam_selesai}</td>
          <td>${row.mapel}</td>
          <td>${row.guru}</td>
          <td>
            <button class="editSlug" data-id="${row.id}">Edit</button>
            <button class="deleteSlug" data-id="${row.id}">Hapus</button>
          </td>
        </tr>
      `
      ).join("");

      tbody.querySelectorAll(".editSlug").forEach(btn => {
        btn.addEventListener("click", () => {
          const id = btn.dataset.id;
          openEditJadpelModal(id);
        })
      })

      tbody.querySelectorAll(".deleteSlug").forEach(btn => {
        btn.addEventListener("click", async () => {
          const id = btn.dataset.id;
          
          const result = await alertConfirm();
          if(!result.isConfirmed) return;

          deleteJadpel(id, page, limit, keyword);
        })
      })

      if(result.pagination){
        renderJadpelPagination(result.pagination, (selectedPage) => {jadpelTable(keyword, selectedPage, limit, onSuggestions)});
      }
    })
    .catch(err => {
      console.error("Gagal memuat jadwal:", err);
      tbody.innerHTML = `
        <tr class="empty">
          <td colspan="8" class="text-center text-danger">Gagal memuat jadwal.</td>
        </tr>
      `;
    })
}