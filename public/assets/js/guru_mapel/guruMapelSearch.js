import { getUnitSlug } from "../utils/url.js";
import { guruMapelTable } from "./guruMapelTable.js";

const searchInput = document.getElementById("searchInputGuruMapel");
const suggestionsBox = document.getElementById("suggestionsGuruMapel");
const searchIcon = document.getElementById("searchIconGuruMapel");
const searchForm = document.querySelector(".search-form-unitSlug");

function fetchSuggestions(keyword){
  const slug = getUnitSlug();
  const url = `${base_url}/unit/${slug}/guru_mapel/search?keyword=${keyword}`;

  return fetch(url)
    .then(res => res.json())
    .catch(err => {
      console.error(err);
      return {data: []};
    });
}

function renderSuggestions(list){
  if (!Array.isArray(list) || list.length === 0) {
    suggestionsBox.innerHTML = `<div class="empty-suggestion">Tidak ada hasil</div>`;
    return;
  }

  suggestionsBox.innerHTML = list.map(item => `
    <div class="suggestion-item" data-id="${item.id}"> 
      ${item.guru} - ${item.mapel}
    </div>  
  `).join("");
}

function clearSuggestions(){
  suggestionsBox.innerHTML = "";
}

function handleSuggestionClick(e){
  const item = e.target.closest(".suggestion-item");
  if(!item) return;

  const text = item.textContent.trim();
  searchInput.value = text;
  clearSuggestions();
  searchInput.focus();
}

function updateIcon(hasText){
  if(hasText){
    searchIcon.classList.remove("fa-search");
    searchIcon.classList.add("fa-x")
  }else{
    searchIcon.classList.remove("fa-x");
    searchIcon.classList.add("fa-search");
  }
}

export function initGuruMapelSearch(){
  if(!searchInput) return;

  searchInput.addEventListener("input", () => {
    const raw = searchInput.value.trim();
    const keyword = raw.split("-")[0].trim();

    updateIcon(raw.length > 0);
    
    if(keyword.length === 0){
      clearSuggestions();
      return;
    }

    fetchSuggestions(keyword).then(result => {
      renderSuggestions(result.data)
    })

  })

  suggestionsBox.addEventListener("click", handleSuggestionClick);

  searchIcon.addEventListener("click", () => {
    if (searchIcon.classList.contains("fa-x")) {
      searchInput.value = "";
      clearSuggestions();
      updateIcon(false);
      searchInput.focus();
      guruMapelTable()
    }
  });

  searchInput.addEventListener("keydown", (e) => {
    const items = suggestionsBox.querySelectorAll(".suggestion-item");
    if(items.length === 0) return;

    if(e.key === "ArrowDown"){
      e.preventDefault();

      let index = Array.from(items).findIndex(item => 
        item.classList.contains("selected")
      );

      if(index < items.length - 1) index++;
      else index = 0;
      
      items.forEach(i => i.classList.remove("selected"));

      items[index].classList.add("selected");
      items[index].scrollIntoView({
        block: "nearest",
      });
      searchInput.value = items[index].textContent.trim();
    }

    if(e.key === "ArrowUp"){
      e.preventDefault();

      let index = Array.from(items).findIndex(item => 
        item.classList.contains("selected")
      );

      if(index > 0) index--;
      else index = items.length - 1;

      items.forEach(i => i.classList.remove("selected"));

      items[index].classList.add("selected");
      items[index].scrollIntoView({
        block: "nearest"
      });
      searchInput.value = items[index].textContent.trim();
    }

    if(e.key === "Enter"){
      e.preventDefault();
      const raw = searchInput.value.trim();
      const keyword = raw.split("-")[0].trim();
      guruMapelTable(keyword);
      clearSuggestions();
    }
  });

  document.addEventListener("keydown", (e) => {
    if(e.key === "Escape"){
      clearSuggestions();
      updateIcon(searchInput.value.trim().length > 0);
    }
  });

  document.addEventListener("click", (e) => {
    if(!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)){
      clearSuggestions();
    }
  });

  searchForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const raw = searchInput.value.trim();
    const keyword = raw.split("-")[0].trim();
    guruMapelTable(keyword);
  })
}