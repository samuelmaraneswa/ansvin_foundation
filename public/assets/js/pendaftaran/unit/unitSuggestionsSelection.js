export function updateSuggestionSelection(items, selectedIndex, suggestionsBox, searchInput) {
  if (!items.length) return;

  items.forEach((item) => item.classList.remove("active"));

  const activeItem = items[selectedIndex];
  if (!activeItem) return;

  activeItem.classList.add("active");
  searchInput.value = activeItem.innerText;

  const itemTop = activeItem.offsetTop;
  const itemHeight = activeItem.offsetHeight;
  const boxScrollTop = suggestionsBox.scrollTop;
  const boxHeight = suggestionsBox.clientHeight;

  if (itemTop < boxScrollTop) {
    suggestionsBox.scrollTop = itemTop;
  } else if (itemTop + itemHeight > boxScrollTop + boxHeight) {
    suggestionsBox.scrollTop = (itemTop + itemHeight) - boxHeight;
  }
}
