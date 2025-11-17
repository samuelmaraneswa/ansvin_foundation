// generateNip.js
export function initGenerateNip(base_url) {
  const unitSelect = document.querySelector('select[name="unit_id"]');
  const nipInput = document.querySelector('input[name="nip"]');
  if (!unitSelect || !nipInput) return;

  unitSelect.addEventListener("change", async function () {
    const unitId = this.value;
    if (!unitId) return;
    try {
      const res = await fetch(
        `${base_url}/admin/pegawai/generate_nip?unit_id=${unitId}`
      );
      const data = await res.json();
      if (data.success) nipInput.value = data.nip;
    } catch (err) {
      console.error("Gagal generate NIP", err);
    }
  });
}
