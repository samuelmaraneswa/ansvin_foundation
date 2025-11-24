import { alertError } from "../utils/alert.js";
import { getUnitSlug } from "../utils/url.js";
import { openModal } from "./modalMapel.js";

export function initEditMapel(){
  document.addEventListener("click", (e) => {
    if(e.target.classList.contains("editMapelBtn")){

      const id = e.target.dataset.id;
      const slug = getUnitSlug();

      fetch(`${base_url}/unit/${slug}/mapel/get/${id}`)
        .then(res => res.json())
        .then(result => {
          openModal();
          fillForm(result.data);
        })
        .catch(err => alertError(err));
    }
  })
}

function fillForm(data){
  document.getElementById("mapelId").value = data.id
  document.getElementById("namaMapel").value = data.nama_mapel;
  document.getElementById("kodeMapel").value = data.kode_mapel;
  document.getElementById("tingkatMin").value = data.tingkat_min;
  document.getElementById("tingkatMax").value = data.tingkat_max;
}