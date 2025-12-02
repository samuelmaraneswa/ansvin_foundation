import { deleteJadpel } from "./deleteJadpel.js";
import { openEditJadpelModal } from "./modalJadpel.js";

export function updateRowJadpel(updated) {
  const row = document.querySelector(
    `#unitJadpelTable tr[data-id="${updated.id}"]`
  );
  if (!row) return;

  row.innerHTML = `
    <td>${updated.no}</td>
    <td>${updated.hari}</td>
    <td>${updated.kelas}</td>
    <td>${updated.jam_mulai}</td>
    <td>${updated.jam_selesai}</td>
    <td>${updated.mapel}</td>
    <td>${updated.guru}</td>
    <td>
      <button class="editSlug" data-id="${updated.id}">Edit</button>
      <button class="deleteSlug" data-id="${updated.id}">Hapus</button>
    </td>
  `;

  // Rebind edit
  row.querySelector(".editSlug").addEventListener("click", () => {
    openEditJadpelModal(updated.id);
  });

  // Rebind delete
  row.querySelector(".deleteSlug").addEventListener("click", () => {
    deleteJadpel(updated.id);
  });

  // highlight animasi perubahan
  row.classList.add("updated-row");
  setTimeout(() => row.classList.remove("updated-row"), 800);
}
