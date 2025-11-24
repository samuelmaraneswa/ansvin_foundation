import { alertSuccess } from "../utils/alert.js";
import { clearErrors } from "../utils/clearErrors.js";
import { getUnitSlug } from "../utils/url.js";
import { mapelTabel, updateRowInTable } from "./mapelTable.js";
import { closeModal } from "./modalMapel.js";

export function initMapelSubmit(){
  const form = document.getElementById("formMapel");

  if(!form) return;

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    clearErrors();

    const id = document.getElementById("mapelId").value; 
    const slug = getUnitSlug();
    const formData = new FormData(form);

    const url = id ? `${base_url}/unit/${slug}/mapel/update/${id}` : `${base_url}/unit/${slug}/mapel/store`

    fetch(url, {
      method: "POST",
      body: formData,
    })
      .then(res => res.json())
      .then(result => {
        if(result.status === "success"){
          alertSuccess(result.message)
          closeModal();

          if(id){
            updateRowInTable(id, {
              nama_mapel: formData.get("nama_mapel"),
              kode_mapel: formData.get("kode_mapel"),
              tingkat_min: formData.get("tingkat_min"),
              tingkat_max: formData.get("tingkat_max")
            });
          }else{
            mapelTabel();
          }
        }else{
          console.log(result);
          if(result.errors){
            Object.keys(result.errors).forEach(field => {
              document.getElementById(`err-${field}`).innerText = result.errors[field][0];
            })
          }
        }
      })
      .catch(err => console.error(err));
  })
}