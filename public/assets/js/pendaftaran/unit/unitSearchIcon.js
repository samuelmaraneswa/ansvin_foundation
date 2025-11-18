export function activateClearIcon(icon) {
  icon.classList.remove("fa-search");
  icon.classList.add("fa-times");
  icon.style.color = "red";
}

export function resetSearchIcon(icon) {
  icon.classList.add("fa-search");
  icon.classList.remove("fa-times");
  icon.style.color = "";
}
