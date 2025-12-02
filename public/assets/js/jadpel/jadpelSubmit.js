import { alertError, alertSuccess } from "../utils/alert.js";
import { getUnitSlug } from "../utils/url.js";
import { jadpelTable } from "./jadpelTable.js";
import { hideJadpelModal } from "./modalJadpel.js";
import { updateRowJadpel } from "./updatedRowJadpel.js";

export function initJadpelSubmit(){
  const form = document.getElementById("formJadpel");
  if(!form) return;

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const slug = getUnitSlug();
    const id = formData.get("jadpel-id"); 

    // console.log("Form Data:", Object.fromEntries(formData));
    const url = id 
      ? `${base_url}/unit/${slug}/jadpel/update/${id}`
      : `${base_url}/unit/${slug}/jadpel/store`

    fetch(url ,{
      method: "POST",
      body: formData
    })
      .then(res => res.json())
      .then(result => {
        console.log("Hasil server:", result);
        if(result.status === "success"){
          alertSuccess(result.message);
          hideJadpelModal();
          
          if(id){
            updateRowJadpel(result.data);
          }else{
            jadpelTable("", 1, 5);
          }
        }else{
          alertError(result.message)
        }
      })
      .catch(err => console.error("Gagal submit jadwal:", err));
  })
}