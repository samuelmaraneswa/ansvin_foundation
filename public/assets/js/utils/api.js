// utils/api.js

/**
 * Helper untuk permintaan GET JSON
 */
export async function getJSON(url){
  try{
    const res = await fetch(url);
    if(!res.ok) throw new Error(`HTTP Error ${res.status}`);
    return await res.json();
  }catch(err){
    console.error("GET JSON error:", err);
    throw err;
  }
}

/**
 * Helper untuk POST FormData (misal tambah/edit)
 */
export async function postForm(url, formData){
  try{
    const res = await fetch(url, {
      method: "POST",
      body: formData,
    });
    if(!res.ok) throw new Error(`HTTP error ${res.status}`);
    return await res.json();
  }catch(err){
    console.error("POST Form error:", err);
    throw err;
  }
}

/**
 * Helper untuk DELETE (atau POST jika backend belum dukung DELETE)
 */
export async function deleteData(url, usePostInstead = false){
  try{
    const res = await fetch(url, {
      method: usePostInstead ? "POST" : "DELETE",
    });
    if(!res.ok) throw new Error(`HTTP error ${res.status}`);
    return await res.json();
  }catch(err){
    console.error("DELETE error:", err);
    throw err;
  }
}