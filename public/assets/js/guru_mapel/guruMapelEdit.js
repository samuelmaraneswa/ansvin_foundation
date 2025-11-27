import { getUnitSlug } from "../utils/url.js";
import { openModal } from "./guruMapelModal.js";

const table = document.getElementById("unitGuruMapelTable");
const modalTitle = document.getElementById("modal-title");

export function initEditGuruMapel(){
  if(!table) return;

  table.addEventListener("click", (e) => {
    const btn = e.target.closest(".editSlug");
    if(!btn) return;

    const id = btn.dataset.id;
    const slug = getUnitSlug();

    fetch(`${base_url}/unit/${slug}/guru_mapel/get/${id}`)
      .then(res => res.json())
      .then(result => {
        console.log(result);

        if(result.status === "success" && result.data){
          const data = result.data
          
          if (modalTitle) modalTitle.textContent = "Edit Data";
          openModal();
          
          document.getElementById("id").value = data.id;
          
          setTimeout(() => {
            document.querySelector('select[name="guru"]').value = data.pegawai_id;
            document.querySelector('select[name="mapel"]').value = data.mapel_id;
            document.querySelector('select[name="tahun_ajaran"]').value = data.tahun_ajaran_id;
          }, 200);
        }
      })
      .catch(err => console.error(err))
  })
}