import { showSuggestionsBox } from "./unitSuggestionsVisibility.js";

export function renderUnitSuggestions(list, keyword, suggestionsBox, onSelect){
  if (!list || list.length === 0) {
    suggestionsBox.innerHTML = `<div class="suggestions-item no-data">
      *Data calon siswa tidak ada*
    </div>`;
    showSuggestionsBox(suggestionsBox);
    return;
  }

  suggestionsBox.innerHTML = list
    .map(item => `<div class="suggestions-item">${highlight(item.nama_lengkap, keyword)}</div>`)
    .join("");
  
  const items = suggestionsBox.querySelectorAll(".suggestions-item");

  items.forEach((el, index) => {
    el.addEventListener("click", () => {
      onSelect(list[index].nama_lengkap);
    });
  });

  showSuggestionsBox(suggestionsBox);

  function highlight(text, keyword){
    const regex = new RegExp(`(${keyword})`, "gi");
    return text.replace(regex, "<strong>$1</strong>");
  }
}