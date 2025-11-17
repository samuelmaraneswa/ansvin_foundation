// admin_artikel.js
tinymce.init({
  selector: "#isi",
  plugins: "image link media table code lists",
  toolbar:
    "undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | image link media insertfigure | code",
  menubar: false,
  height: 500,
  automatic_uploads: true,
  file_picker_types: "image",

  // Upload handler menggunakan async/await
  images_upload_handler: async (blobInfo) => {
    const formData = new FormData();
    formData.append("file", blobInfo.blob(), blobInfo.filename());

    try {
      const res = await fetch(
        "/ansvin_foundation/public/admin/upload_image_temp",
        {
          method: "POST",
          body: formData,
          credentials: "same-origin",
        }
      );

      // pastikan server mengembalikan JSON
      const data = await res.json();
      console.log("Response JSON dari server:", data);

      if (data && data.location) {
        // kembalikan URL sementara untuk preview di editor
        return data.location;
      } else {
        throw new Error(data?.error || "Upload gagal: response tidak valid");
      }
    } catch (err) {
      console.error("TinyMCE Upload Error:", err);
      throw err; // TinyMCE akan menampilkan error popup
    }
  },

  // Tambahkan di sini sebelum baris penutup });
  setup: function (editor) {
    editor.ui.registry.addButton("insertfigure", {
      text: "Gambar+Caption",
      onAction: function () {
        editor.insertContent(
          '<figure><img src="" alt=""><figcaption>Tulis caption...</figcaption></figure><p></p>'
        );
      },
    });
  },
});

// edit artikel hapus gambar tambahan + caption
document.addEventListener("DOMContentLoaded", () => {
  const gallery = document.querySelector(".artikel-gallery-edit");
  const deletedInput = document.getElementById("deleted_image_ids");
  const pendingDeletion = []; // array untuk menampung image_id yang dihapus

  // ================== Hapus Gambar Lama ==================
  if (gallery) {
    gallery.addEventListener("click", (e) => {
      const btn = e.target.closest(".delete-image");
      if (!btn) return;

      const container = btn.closest(".old-image");
      const imageId = container.dataset.id;

      // Tandai untuk dihapus nanti
      pendingDeletion.push(imageId);
      console.log(pendingDeletion);
      // Hapus dari DOM
      container.remove();

      // Update hidden input
      deletedInput.value = JSON.stringify(pendingDeletion);
    });
  }

  // ================== Preview Gambar Baru + Caption ==================
  const input = document.getElementById("images");
  const preview = document.getElementById("image-preview");

  if (input && preview) {
    input.addEventListener("change", function () {
      preview.innerHTML = "";
      [...this.files].forEach((file, i) => {
        const url = URL.createObjectURL(file);
        const wrapper = document.createElement("div");
        wrapper.classList.add("image-item");

        wrapper.innerHTML = `
          <img src="${url}" alt="" class="gambar-tambahan">
          <input type="text" name="captions[]" placeholder="Caption untuk ${file.name}" class="caption-gambar-tambahan">
          <button type="button" class="delete-image-new">
            <i class="fas fa-trash"></i>
          </button>
        `;

        preview.appendChild(wrapper);
      });

      // Tambahkan event listener untuk tombol hapus baru
      preview.querySelectorAll(".delete-image-new").forEach((btn) => {
        btn.addEventListener("click", (e) => {
          const container = e.target.closest(".image-item");
          container.remove();
        });
      });
    });
  }
});
