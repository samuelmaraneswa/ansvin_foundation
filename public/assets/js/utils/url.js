export function getUnitSlug(){
  const parts = window.location.pathname.split("/");
  const unitIndex = parts.indexOf("unit");
  return unitIndex !== -1 ? parts[unitIndex + 1] : null;
}