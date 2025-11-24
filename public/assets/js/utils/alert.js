// utils/alert.js

/**
 * SweetAlert wrapper untuk konfirmasi hapus data
 * @param {string} message
 */
export async function alertConfirm(
  message = "Yakin ingin menghapus data ini?"
) {
  return await Swal.fire({
    icon: "warning",
    title: "Konfirmasi",
    text: message,
    showCancelButton: true,
    confirmButtonText: "Ya, Hapus",
    cancelButtonText: "Batal",
  });
}

/**
 * SweetAlert wrapper untuk pesan sukses
 * @param {string} message
 */
export function alertSuccess(message = "Operasi berhasil!") {
  Swal.fire({
    icon: "success",
    title: "Berhasil!",
    text: message,
    timer: 1500,
    showConfirmButton: false,
  });
}

/**
 * SweetAlert wrapper untuk pesan error
 * @param {string} message
 */
export function alertError(message = "Terjadi kesalahan!") {
  Swal.fire({
    icon: "error",
    title: "Kesalahan!",
    text: message,
  });
}

export function alertConfirm2(message = "Yakin ingin menghapus data ini?"){
  return Swal.fire({
    icon: "warning",
    title: "Konfirmasi",
    text: message,
    showCancelButton: true,
    confirmButtonText: "Ya, Hapus",
    cancelButtonText: "Batal",
  });
}