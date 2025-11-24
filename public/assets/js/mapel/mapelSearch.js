import { getJSON } from "../utils/api.js";
import { getUnitSlug } from "../utils/url.js";
import { mapelTabel } from "./mapelTable.js";

export function initMapelSearch(){
  const form = document.querySelector(".search-form-mapel")
  const input = form ? form.querySelector("#searchInputMapel") : null;
  const suggestionsBox = document.getElementById("suggestionsMapel")
  const searchIcon = document.getElementById("searchIconMapel")
  let keyword = "";

  let activeIndex = -1;

  searchIcon.addEventListener("click", () => {
    if(searchIcon.classList.contains("fa-x")){
      input.value = "";
      keyword = "";
      clearSearchState(input, suggestionsBox)

      setIcon(false, searchIcon)
      mapelTabel(keyword, 1, 5);
    }
  })

  if(!form || !input || !suggestionsBox) return;
  
  form.addEventListener("input", async (e) => {
    e.preventDefault();
    keyword = input.value.trim();

    if(keyword){
      setIcon(true, searchIcon);
    }else{
      setIcon(false, searchIcon);
      clearSearchState(input, suggestionsBox)
      mapelTabel(keyword, 1, 5);
      return;
    }

    const slug = getUnitSlug();

    try{
      const url = `${base_url}/unit/${slug}/mapel/search?keyword=${keyword}&page=1&limit=5`;
      const result = await getJSON(url);

      console.log(result);
      
      if(result.status === "success"){
        renderSuggestions(result.data, suggestionsBox, input, keyword)
        setIcon(true, searchIcon)
      }

    }catch(err){
      console.error("Error search mapel:", err)
    }
  })

  input.addEventListener("keydown", (e) => {
    const items = suggestionsBox.querySelectorAll(".suggestion-item");
    if(!items) return;

    if(e.key === "ArrowDown"){
      e.preventDefault();
      activeIndex = (activeIndex + 1) % items.length;
      updateActiveItems(items);
    }

    if(e.key === "ArrowUp"){
      e.preventDefault();
      activeIndex = (activeIndex - 1 + items.length) % items.length
      updateActiveItems(items)
    }

    if(e.key === "Enter"){
      e.preventDefault();
      keyword = input.value.trim()
      clearSearchState(input, suggestionsBox)
      mapelTabel(keyword, 1, 5);
    }

    if(e.key === "Escape"){
      clearSearchState(input, suggestionsBox);
    }
  })
  
  function updateActiveItems(items) {
    items.forEach((item, index) => {
      if (index === activeIndex) {
        item.classList.add("active");
        item.scrollIntoView({ block: "nearest" });

        input.value = item.textContent
      } else {
        item.classList.remove("active");
      }
    });
  }

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    keyword = input.value.trim();
    mapelTabel(keyword, 1, 5);
  })

  document.addEventListener("click", (e) => {
    if(!suggestionsBox.contains(e.target) && e.target !== input){
      clearSearchState(input, suggestionsBox);
    }
  })
}

function renderSuggestions(data, suggestionsBox, input, keyword){
  if(!data || data.length === 0){
    suggestionsBox.innerHTML = `<div class="no-result">Tidak ada data</div>`;
    suggestionsBox.style.display = "block";
    return;
  }

  suggestionsBox.innerHTML = data
    .map((item) => {
      const regex = new RegExp(keyword, "gi");
      const highlighted = item.nama_mapel.replace(regex, (match) => `<strong>${match}</strong>`)
      return `<div class="suggestion-item">${highlighted}</div>`
    })
    .join("");

  suggestionsBox.style.display = "block";

  const items = suggestionsBox.querySelectorAll(".suggestion-item");
  attachSuggestionsClick(items, input, suggestionsBox);
}

function attachSuggestionsClick(items, input, suggestionsBox){
  items.forEach((item) => {
    item.addEventListener("click", () => {
      input.value = item.textContent;
      input.focus();
      clearSearchState(input, suggestionsBox)
    });
  });
}

function setIcon(isActive, searchIcon){
  if(isActive){
    searchIcon.classList.remove("fa-search");
    searchIcon.classList.add("fa-x", "active");
  }else{
    searchIcon.classList.remove("fa-x", "active");
    searchIcon.classList.add("fa-search");
  }
}

function clearSearchState(input, suggestionsBox){
  suggestionsBox.innerHTML = "";
  suggestionsBox.style.display = "none";
}
