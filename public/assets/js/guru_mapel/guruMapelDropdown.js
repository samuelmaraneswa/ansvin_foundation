import { getUnitSlug } from "../utils/url.js";

const modal = document.getElementById("unitGuruMapelModal");
const selectGuru = modal ? modal.querySelector('select[name="guru"]') : null;
const selectMapel = modal ? modal.querySelector('select[name="mapel"]') : null;
const selectTahun = modal ? modal.querySelector('select[name="tahun_ajaran"]') : null;

function fillSelect(selectEl, items, placeholder){
  if(!selectEl) return;

  selectEl.innerHTML = `<option value="">${placeholder}</option>`
  items.forEach(item => {
    const opt = document.createElement("option");
    opt.value = item.id;
    opt.textContent = item.nama || item.nama_mapel || item.nama_tahun;
    selectEl.appendChild(opt);
  });
}

export function loadGuruMapelDropdowns(){
  if(!modal) return;

  const slug = getUnitSlug();
  const url = `${base_url}/unit/${slug}/guru_mapel/dropdown`;

  fetch(url)
    .then(res => res.json())
    .then(result => {
      console.log(result)
      fillSelect(selectGuru, result.guru, " - Pilih Guru - ");
      fillSelect(selectMapel, result.mapel, "- Pilih Mapel -");
      fillSelect(selectTahun, [result.tahun], "- Pilih Tahun Ajaran -");
    })
    .catch(err => console.error(err))
} 