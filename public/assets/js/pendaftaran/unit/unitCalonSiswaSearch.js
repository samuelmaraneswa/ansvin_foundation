import { renderUnitCalonSiswaTable } from "./unitCalonSiswaTable.js";
import { fetchUnitSuggestions } from "./unitSuggestionsService.js";
import { renderUnitSuggestions } from "./unitSuggestionsRenderer.js";
import { handleSuggestionsKeyboardNav } from "./unitSuggestionsNav.js";
import { updateSuggestionSelection } from "./unitSuggestionsSelection.js";
import { hideSuggestionsBox } from "./unitSuggestionsVisibility.js";
import { activateClearIcon, resetSearchIcon } from "./unitSearchIcon.js";

export function initUnitCalonSiswaSearch(){
  const searchInput = document.getElementById("searchInputCalonSiswaUnit")
  const suggestionsBox = document.getElementById("suggestionsCalonSiswaUnit")
  const searchIcon = document.getElementById("searchIconCalonSiswaUnit");

  let selectedIndex = -1;
  let suggestions = [];

  // console.log("search loaded:", searchInput, suggestionsBox);

  searchInput.addEventListener("input", (e) => {
    const keyword = e.target.value.trim();
    // console.log("keyword:", keyword)

    if(keyword.length > 0){
      activateClearIcon(searchIcon);
    }else{
      resetSearchIcon(searchIcon);
      renderUnitCalonSiswaTable("", 1, 5);
    }

    if (!keyword) {
      hideSuggestionsBox(suggestionsBox);
      return;
    }

    fetchUnitSuggestions(keyword)
      .then(result => {
        console.log("hasil fetch:", result);

        const list = result.data || [];
        suggestions = list;
        selectedIndex = -1;
        
        renderUnitSuggestions(list, keyword, suggestionsBox, (selectedName) => {
          searchInput.value = selectedName;
          hideSuggestionsBox(suggestionsBox);
        });
      })
    .catch(err => console.error("error fetch:", err));
  })

  searchIcon.addEventListener("click", () => {
    if(searchInput.value.trim() !== ""){
      searchInput.value = "";
      hideSuggestionsBox(suggestionsBox);
      resetSearchIcon(searchIcon);
      renderUnitCalonSiswaTable("", 1, 5);
      searchInput.focus();
    }
  })

  document.addEventListener("click", (e) => {
    if(!e.target.closest("#searchInputCalonSiswaUnit") && !e.target.closest("#suggestionsCalonSiswaUnit")){
      hideSuggestionsBox(suggestionsBox);
    }
  })

  searchInput.addEventListener("keydown", (e) => {
    const items = suggestionsBox.querySelectorAll(".suggestions-item");
    
    handleSuggestionsKeyboardNav(e, items, {
      selectedIndex,
      updateIndex: (newIndex) => {
        selectedIndex = newIndex;
        updateSuggestionSelection(items, selectedIndex, suggestionsBox, searchInput);
      }
    }, (selected) => {
      if (selected === null) {
        hideSuggestionsBox(suggestionsBox);
        renderUnitCalonSiswaTable(searchInput.value.trim(), 1, 5);
        return;
      }
      
      searchInput.value = suggestions[selected].nama_lengkap;
      hideSuggestionsBox(suggestionsBox);
      renderUnitCalonSiswaTable(searchInput.value.trim(), 1, 5);
    });
  });

  const searchForm = document.getElementById("searchFormCalonSiswaUnit");
  searchForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const keyword = searchInput.value.trim();
    renderUnitCalonSiswaTable(keyword, 1, 5);
    hideSuggestionsBox(suggestionsBox);
  })
}

// import { renderUnitCalonSiswaTable } from "./unitCalonSiswaTable.js";

// export function initUnitCalonSiswaSearch() {
//   const searchInput = document.getElementById("searchInputCalonSiswaUnit");
//   const suggestionsBox = document.getElementById("suggestionsCalonSiswaUnit");
//   const searchIcon = document.getElementById("searchIconCalonSiswaUnit");

//   let selectedIndex = -1;
//   let suggestions = [];

//   // console.log("search loaded:", searchInput, suggestionsBox);

//   searchInput.addEventListener("input", (e) => {
//     const keyword = e.target.value.trim();
//     // console.log("keyword:", keyword)

//     if (keyword.length > 0) {
//       searchIcon.classList.remove("fa-search");
//       searchIcon.classList.add("fa-times");
//       searchIcon.style.color = "red";
//     } else {
//       resetSearchIcon();
//       renderUnitCalonSiswaTable();
//     }

//     const parts = window.location.pathname.split("/");
//     const unitIndex = parts.indexOf("unit");
//     const slug = unitIndex !== -1 ? parts[unitIndex + 1] : null;

//     if (!keyword) {
//       suggestionsBox.style.display = "none";
//       return;
//     }

//     fetch(`${base_url}/unit/${slug}/calon-siswa/search?keyword=${keyword}`)
//       .then((res) => res.json())
//       .then((result) => {
//         console.log("hasil fetch:", result);

//         const list = result.data || [];
//         suggestions = list;
//         selectedIndex = -1;
//         suggestionsBox.innerHTML = list
//           .map(
//             (item) =>
//               `<div class="suggestions-item">${highlight(
//                 item.nama_lengkap,
//                 keyword
//               )}</div>`
//           )
//           .join("");

//         document
//           .querySelectorAll("#suggestionsCalonSiswaUnit div")
//           .forEach((el) => {
//             el.addEventListener("click", () => {
//               searchInput.value = el.textContent;
//               suggestionsBox.style.display = "none";
//             });
//           });

//         suggestionsBox.style.display = "block";
//       })
//       .catch((err) => console.error("error fetch:", err));
//   });

//   searchIcon.addEventListener("click", () => {
//     if (searchInput.value.trim() !== "") {
//       searchInput.value = "";
//       suggestionsBox.style.display = "none";
//       resetSearchIcon();
//       renderUnitCalonSiswaTable();
//       searchInput.focus();
//     }
//   });

//   document.addEventListener("click", (e) => {
//     if (
//       !e.target.closest("#searchInputCalonSiswaUnit") &&
//       !e.target.closest("#suggestionsCalonSiswaUnit")
//     ) {
//       suggestionsBox.style.display = "none";
//     }
//   });

//   searchInput.addEventListener("keydown", (e) => {
//     const items = suggestionsBox.querySelectorAll(".suggestions-item");
//     if (!items.length) return;

//     if (e.key === "ArrowDown") {
//       selectedIndex++;
//       if (selectedIndex >= items.length) selectedIndex = 0;
//       updateSelection(items);
//       e.preventDefault();
//     }

//     if (e.key === "ArrowUp") {
//       selectedIndex--;
//       if (selectedIndex < 0) selectedIndex = items.length - 1;
//       updateSelection(items);
//       e.preventDefault();
//     }

//     if (e.key === "Enter") {
//       e.preventDefault();

//       const items = suggestionsBox.querySelectorAll(".suggestions-item");

//       if (selectedIndex >= 0 && items[selectedIndex]) {
//         searchInput.value = suggestions[selectedIndex].nama_lengkap;
//         suggestionsBox.style.display = "none";
//         renderUnitCalonSiswaTable(searchInput.value.trim());
//         return;
//       }

//       renderUnitCalonSiswaTable(searchInput.value.trim());
//       suggestions.style.display = "none";
//     }

//     if (e.key === "Escape") {
//       suggestionsBox.style.display = "none";
//     }
//   });

//   const searchForm = document.getElementById("searchFormCalonSiswaUnit");
//   searchForm.addEventListener("submit", (e) => {
//     e.preventDefault();
//     const keyword = searchInput.value.trim();
//     renderUnitCalonSiswaTable(keyword);
//     suggestionsBox.style.display = "none";
//   });

//   function updateSelection(items) {
//     items.forEach((item) => item.classList.remove("active"));
//     const activeItem = items[selectedIndex];
//     activeItem.classList.add("active");

//     // activeItem.scrollIntoView({
//     //   block: "nearest",
//     //   behavior: "auto",
//     // });

//     searchInput.value = activeItem.innerText;

//     // manual scroll control
//     const box = suggestionsBox;
//     const itemTop = activeItem.offsetTop;
//     const itemHeight = activeItem.offsetHeight;
//     const boxScrollTop = box.scrollTop;
//     const boxHeight = box.clientHeight;

//     if (itemTop < boxScrollTop) {
//       box.scrollTop = itemTop;
//     } else if (itemTop + itemHeight > boxScrollTop + boxHeight) {
//       box.scrollTop = itemTop + itemHeight - boxHeight;
//     }
//   }

//   function highlight(text, keyword) {
//     const regex = new RegExp(`(${keyword})`, "gi");
//     return text.replace(regex, "<strong>$1</strong>");
//   }

//   function resetSearchIcon() {
//     searchIcon.classList.add("fa-search");
//     searchIcon.classList.remove("fa-times");
//     searchIcon.style.color = "";
//   }
// }