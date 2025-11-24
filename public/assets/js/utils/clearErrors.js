export function clearErrors(){
  document.querySelectorAll(".error-text").forEach(el => el.innerText = "");
}