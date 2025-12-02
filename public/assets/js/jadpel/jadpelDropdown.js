import { getUnitSlug } from "../utils/url.js";

export async function loadGuruMapelOptions(){
  try{
    const slug = getUnitSlug();
    const res = await fetch(`${base_url}/unit/${slug}/jadpel/getGuruMapelOptions`);
    const json = await res.json();

    if(json.status !== "success") return;

    renderGuruMapelOptions(json.data);
    return true;
  }catch(err){
    console.error("Gagal load guru_mapel:", err);
    return false;
  }
}

function renderGuruMapelOptions(list){
  const select = document.getElementById("selectGuruMapel");
  if(!select) return;

  select.innerHTML = `<option value="">- Pilih Guru Mapel -</option>`;

  list.forEach(item => {
    select.innerHTML += `<option value="${item.id}">${item.text}</option>`
  });
}

export async function loadKelasOptions(){
  try{
    const slug = getUnitSlug();
    const res = await fetch(`${base_url}/unit/${slug}/jadpel/getKelasOptions`);
    const json = await res.json();

    if(json.status !== "success") return;

    renderKelasOptions(json.data);
    return true;
  }catch(err){
    console.error("Gagal load kelas:", err);
    return false;
  }
}

function renderKelasOptions(list){
  const select = document.getElementById("selectKelas");
  if(!select) return;

  select.innerHTML = `<option value="">- Pilih Kelas -</option>`;

  list.forEach(item => {
    select.innerHTML += `<option value="${item.id}">${item.text}</option>`
  })
}