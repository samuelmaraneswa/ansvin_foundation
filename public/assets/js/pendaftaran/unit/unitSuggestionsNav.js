export function handleSuggestionsKeyboardNav(e, items, state, onEnter){
  const {selectedIndex, updateIndex} = state;

  if(!items.length) return;

  if(e.key === "ArrowDown"){
    updateIndex(selectedIndex + 1 >= items.length ? 0 : selectedIndex + 1);
    e.preventDefault();
  }

  if(e.key === "ArrowUp"){
    updateIndex(selectedIndex - 1 < 0 ? items.length -1 : selectedIndex - 1);
    e.preventDefault();
  }

  if(e.key === "Enter"){
    e.preventDefault();
    if(selectedIndex >= 0 && items[selectedIndex]){
      onEnter(selectedIndex);
      return;
    }
     
    onEnter(null);
  }

 if (e.key === "Escape") {
   e.preventDefault();
   onEnter(null); 
 }
}