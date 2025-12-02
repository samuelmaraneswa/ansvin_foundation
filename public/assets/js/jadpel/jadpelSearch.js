// import { jadpelTable } from "./jadpelTable.js";

// const suggestionsBox = document.getElementById("suggestionsJadpel")
// let debounceTimer = null;
// let input;
// let icon
// let lockSuggestions = false;
// let activeIndex = -1;
// let isNavigating = false;

// function autoScrollActiveItem(){
//   const activeItem = suggestionsBox.querySelector(".suggestion-item.active");
//   if(activeItem){
//     activeItem.scrollIntoView({
//       block: "nearest",
//       behavior: "smooth"
//     })
//   }
// }

// function setActiveSuggestion(index){
//   const items = suggestionsBox.querySelectorAll(".suggestion-item");
//   if(!items.length) return;

//   items.forEach(item => item.classList.remove("active"));

//   if(index >= 0 && index < items.length){
//     items[index].classList.add("active");
//   }
// }

// function clearSuggestions(){
//   suggestionsBox.innerHTML = "";
//   suggestionsBox.classList.remove("show");
// }

// function getElementsSuggestions(){
//   input = document.getElementById("searchInputJadpel");
//   icon = document.getElementById("searchIconJadpel");
// }

// export function initJadpelSearch(){
//   getElementsSuggestions();

//   if(!input || !icon) return;

//   input.addEventListener("keyup", (e) => {
//     if(isNavigating){
//       isNavigating = false;
//       return;
//     }

//     const keyword = e.target.value.trim();

//     if(keyword.length < 2){
//       clearSuggestions();
//       return;
//     }

//     icon.className = keyword ? "fa fa-x" : "fa fa-search";

//     clearTimeout(debounceTimer);
//     debounceTimer = setTimeout(() => {
//       lockSuggestions = false;
//       jadpelTable(keyword, 1, 5);
//     }, 300);
//   });

//   // reset keyword ketika x di klik
//   icon.addEventListener("click", () => {
//     if(icon.className === "fa fa-x"){
//       input.value = "";
//       icon.className = "fa fa-search";
//       lockSuggestions = false;
//       clearSuggestions();
//       jadpelTable("", 1, 5);
//       input.focus();
//     }
//   });
// }

// document.addEventListener("jadpelSuggestions", (e) => {
//   if(lockSuggestions) return;
  
//   const list = e.detail;
//   const keyword = input.value.trim();

//   if(keyword.length < 2 || !list.length) {
//     clearSuggestions();
//     return;
//   }

//   suggestionsBox.innerHTML = list.map(item => 
//     `<div class="suggestion-item" data-value="${item.hari} ${item.kelas} ${item.mapel} ${item.guru}">${item.hari} ${item.kelas}  ${item.mapel} ${item.guru}</div>`
//   ).join("");

//   suggestionsBox.querySelectorAll(".suggestion-item").forEach(el => {
//     el.addEventListener("click", () => {
//       const value = el.dataset.value;
//       input.value = value;

//       lockSuggestions = true;
//       clearSuggestions();

//       jadpelTable(value, 1, 5);
//       input.focus();
//     })
//   });

//   suggestionsBox.classList.add("show");
// });

// document.addEventListener("click", (e) => {
//   if(!suggestionsBox) return;

//   const clickInside = suggestionsBox.contains(e.target) || input.contains(e.target);
//   if(!clickInside) 
//     clearSuggestions();
// })

// document.addEventListener("keydown", (e) => {
//   if(e.key === "Escape"){
//     clearSuggestions();
//   }
// })

// document.addEventListener("keydown", (e) => {
//   const items = suggestionsBox.querySelectorAll(".suggestion-item");
//   if(!items.length) return;

//   if(e.key === "ArrowDown"){
//     e.preventDefault();
//     isNavigating = true;

//     activeIndex = (activeIndex + 1) % items.length;
//     setActiveSuggestion(activeIndex);

//     const value = items[activeIndex].dataset.value;
//     input.value = value;

//     autoScrollActiveItem();
//   }

//   if(e.key === "ArrowUp"){
//     e.preventDefault();
//     isNavigating = true;

//     activeIndex = (activeIndex - 1 + items.length) % items.length;
//     setActiveSuggestion(activeIndex);

//     const value = items[activeIndex].dataset.value;
//     input.value = value;

//     autoScrollActiveItem();
//   }
// })

// jadpelSearch.js
import { jadpelTable } from "./jadpelTable.js";

const suggestionsBox = document.getElementById("suggestionsJadpel");

let debounceTimer = null;
let input = null;
let icon = null;

const state = {
  keyword: "",
  suggestions: [],
  open: false,
  activeIndex: -1,
};

// utils
function getElements() {
  input = document.getElementById("searchInputJadpel");
  icon  = document.getElementById("searchIconJadpel");
}

function clearSuggestions() {
  state.open = false;
  state.suggestions = [];
  state.activeIndex = -1;
  if (!suggestionsBox) return;
  suggestionsBox.innerHTML = "";
  suggestionsBox.classList.remove("show");
}

function renderSuggestions() {
  if (!suggestionsBox) return;

  if (!state.open || !state.suggestions.length || state.keyword.length < 2) {
    clearSuggestions();
    return;
  }

  suggestionsBox.innerHTML = state.suggestions.map(item => {
    const value = `${item.hari} ${item.kelas} ${item.mapel} ${item.guru}`;
    return `
      <div class="suggestion-item" data-value="${value}">
        ${value}
      </div>
    `;
  }).join("");

  // klik suggestion
  suggestionsBox.querySelectorAll(".suggestion-item").forEach((el, index) => {
    el.addEventListener("click", () => {
      const value = el.dataset.value;
      input.value = value;
      state.keyword = value;
      state.activeIndex = index;

      clearSuggestions();              // tutup list
      fetchTableWithSuggestions(value); // refresh table
      input.focus();
    });
  });

  suggestionsBox.classList.add("show");
}

function updateActiveItem() {
  if (!suggestionsBox) return;
  const items = suggestionsBox.querySelectorAll(".suggestion-item");
  if (!items.length) return;

  items.forEach(item => item.classList.remove("active"));

  if (state.activeIndex >= 0 && state.activeIndex < items.length) {
    const activeItem = items[state.activeIndex];
    activeItem.classList.add("active");
    const value = activeItem.dataset.value;
    input.value = value;
    state.keyword = value;

    activeItem.scrollIntoView({ block: "nearest" });
  }
}

function fetchTableWithSuggestions(keyword, page = 1, limit = 5) {
  state.keyword = keyword;
  jadpelTable(keyword, page, limit, (list) => {
    // callback dipanggil dari jadpelTable
    state.suggestions = list || [];
    state.activeIndex = -1;

    if (state.keyword.length >= 2 && state.suggestions.length) {
      state.open = true;
      renderSuggestions();
    } else {
      clearSuggestions();
    }
  });
}

// init
export function initJadpelSearch() {
  getElements();
  if (!input || !icon || !suggestionsBox) return;

  // KEYDOWN: arrow, enter, esc
  input.addEventListener("keydown", (e) => {
    const items = suggestionsBox.querySelectorAll(".suggestion-item");
    const hasItems = items.length > 0;

    if (e.key === "ArrowDown" && hasItems) {
      e.preventDefault();
      state.open = true;

      state.activeIndex = (state.activeIndex + 1) % items.length;
      updateActiveItem();
      return;
    }

    if (e.key === "ArrowUp" && hasItems) {
      e.preventDefault();
      state.open = true;

      state.activeIndex = (state.activeIndex - 1 + items.length) % items.length;
      updateActiveItem();
      return;
    }

    if (e.key === "Enter") {
      e.preventDefault();
      const value = input.value.trim();
      if (!value) {
        clearSuggestions();
        return;
      }
      clearSuggestions();
      fetchTableWithSuggestions(value);
      return;
    }

    if (e.key === "Escape") {
      clearSuggestions();
      return;
    }
  });

  // KEYUP: ketik biasa
  input.addEventListener("keyup", (e) => {
    // hindari double-handle untuk tombol navigasi
    if (["ArrowDown", "ArrowUp", "Enter", "Escape"].includes(e.key)) {
      return;
    }

    const keyword = e.target.value.trim();
    state.keyword = keyword;

    icon.className = keyword ? "fa fa-x" : "fa fa-search";

    if (keyword.length < 2) {
      clearSuggestions();
      // boleh reload table kosong kalau mau:
      // fetchTableWithSuggestions("");
      return;
    }

    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      fetchTableWithSuggestions(keyword);
    }, 300);
  });

  // klik icon untuk reset
  icon.addEventListener("click", () => {
    if (icon.className === "fa fa-x") {
      input.value = "";
      icon.className = "fa fa-search";
      state.keyword = "";
      clearSuggestions();
      fetchTableWithSuggestions("");
      input.focus();
    }
  });

  // klik di luar → close suggestions
  document.addEventListener("click", (e) => {
    if (!suggestionsBox) return;
    const clickInside =
      suggestionsBox.contains(e.target) || input.contains(e.target);
    if (!clickInside) {
      clearSuggestions();
    }
  });

  const form = document.querySelector(".search-form-unitSlug");
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    const keyword = input.value.trim();
    jadpelTable(keyword, 1, 5);
  });
}
