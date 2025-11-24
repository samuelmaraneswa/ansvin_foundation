import { alertConfirm, alertConfirm2, alertSuccess } from "../utils/alert.js";
import { getUnitSlug } from "../utils/url.js";
import { mapelTabel, removeRowFromTable } from "./mapelTable.js";

export function initHapusMapel(){
  document.addEventListener("click", async (e) => {
    if (e.target.classList.contains("hapusMapelBtn")) {
      const id = e.target.dataset.id;
      const slug = getUnitSlug();
      const form = document.getElementById("formMapel");
      const currentPage = parseInt(document.querySelector(".page-btn.active")?.dataset?.page ?? 1);
      const currentKeyword = document.getElementById("searchInputMapel")?.value.trim() ?? "";
      const limit = 5;
      if (!form) return;

      // alertConfirm2("Yakin ingin menghapus mapel ini?")
      //   .then((result) => {
      //     if(!result.isConfirmed) return;

      //     const formData = new FormData(form);

      //     fetch(`${base_url}/unit/${slug}/mapel/delete/${id}`, {
      //       method: "POST",
      //       body: formData,
      //     })
      //       .then((res) => res.json())
      //       .then((result) => {console.log(result.message)
      //         alertSuccess(result.message);
      //         mapelTabel();
      //       });
      // })
      const confirm = await alertConfirm("Yakin ingin menghapus data ini?");
      if (!confirm.isConfirmed) return;

      const formData = new FormData(form);

      // fetch(`${base_url}/unit/${slug}/mapel/delete/${id}`, {
      //   method: "POST",
      //   body: formData,
      // })
      //   .then((res) => res.json())
      //   .then((result) => {console.log(result.message)
      //     alertSuccess(result.message);
      //     mapelTabel();
      //   });
      const res = await fetch(`${base_url}/unit/${slug}/mapel/delete/${id}`, {
        method: "POST",
        body: formData,
      });

      const result = await res.json();

      alertSuccess(result.message);
      removeRowFromTable(id, currentKeyword, currentPage, limit);
    }
  });
}