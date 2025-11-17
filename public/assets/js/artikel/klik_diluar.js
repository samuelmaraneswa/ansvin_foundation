function onClickOutside(element, callback, exceptions = []) {
  document.addEventListener("click", (e) => {
    const isInsideElement = element.contains(e.target);
    const isException = exceptions.some((ex) => ex.contains(e.target));

    if (!isInsideElement && !isException) {
      callback();
    }
  });
}