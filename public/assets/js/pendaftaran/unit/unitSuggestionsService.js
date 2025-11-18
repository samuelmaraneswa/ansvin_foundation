import { getJSON } from "../../utils/api.js";
import { getUnitSlug } from "../../utils/url.js";

export async function fetchUnitSuggestions(keyword){
  const slug = getUnitSlug();
  const url = `${base_url}/unit/${slug}/calon-siswa/search?keyword=${keyword}&page=1&limit=5`;

  return await getJSON(url);
}