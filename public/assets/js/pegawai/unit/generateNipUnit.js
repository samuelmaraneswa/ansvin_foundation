// generateNipUnit.js
export async function generateNipUnit(base_url, slug) {
  const nipInput = document.querySelector('input[name="nipUnit"]');
  if (!nipInput) return;

  try {
    // Panggil endpoint generate NIP khusus admin_unit
    const res = await fetch(`${base_url}/unit/${slug}/pegawai/generate_nip`);
    const data = await res.json();

    if (data.success) {
      nipInput.value = data.nip;
    } else {
      console.warn("Gagal generate NIP:", data.message);
      nipInput.value = "";
    }
  } catch (err) {
    console.error("Terjadi kesalahan saat generate NIP:", err);
    nipInput.value = "";
  }
}
