import { alertSuccess } from "../utils/alert.js";
import { getUnitSlug } from "../utils/url.js";
import { closeModal } from "./guruMapelModal.js";
import { guruMapelTable } from "./guruMapelTable.js";
import { updateGuruMapelRow } from "./guruMapelUpdateRow.js";

const formGuruMapel = document.getElementById("formGuruMapel");
const errGuru = document.getElementById("err-guru");
const errMapel = document.getElementById("err-mapel");
const errTahun = document.getElementById("err-tahun_ajaran");

export function initSubmitGuruMapel(){
  if(!formGuruMapel) return;

  formGuruMapel.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = new FormData(formGuruMapel);
    const slug = getUnitSlug();
    const id = formData.get("guru-mapel-id");
    
    const url = id
      ? `${base_url}/unit/${slug}/guru_mapel/update/${id}`
      : `${base_url}/unit/${slug}/guru_mapel/store`;

    fetch(url, {
      method: "POST",
      body: formData
    })
      .then(res => res.json())
      .then(result => {
        console.log(result);

        if(errGuru) errGuru.textContent = "";
        if(errMapel) errMapel.textContent = "";
        if(errTahun) errTahun.textContent = "";

        if(result.status === "error"){
          const errors = result.errors;
          if(errors.guru){
            errGuru.textContent = errors.guru[0];
          }
          
          if(errors.mapel){
            errMapel.textContent = errors.mapel[0];
          }
          
          if(errors.tahun_ajaran){
            errTahun.textContent = errors.tahun_ajaran[0];
          }
          
          return;
        }

        alertSuccess(result.message);
        closeModal();

        if(id){
          updateGuruMapelRow(result.data);
        }else{
          guruMapelTable();
        }
      })
      .catch(err => console.error(err));
  })
}