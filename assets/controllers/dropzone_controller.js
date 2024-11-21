import { Controller } from "@hotwired/stimulus";
import Dropzone from "dropzone";

export default class extends Controller {
  connect() {
    console.log("Dropzone Controller Connected");

    const dropzone = new Dropzone(this.element, {
      url: "/upload-company-image",
      paramName: "file",
      maxFilesize: 5,
      acceptedFiles: "image/*",
      addRemoveLinks: true,
      headers: {
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
          .content,
      },
      init: function () {
        this.on("sending", (file, xhr, formData) => {
          console.log("Sending file:", file);
        });

        this.on("success", (file, response) => {
          console.log("Upload success:", response);
          if (response.success) {
            // Marquer le fichier comme accepté
            file.previewElement.classList.add("dz-success");

            // Ajouter l'input caché
            const form = this.element.closest("form");
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "sport_company[images][]";
            input.value = response.filename;
            form.appendChild(input);
          }
        });

        this.on("error", (file, errorMessage, xhr) => {
          console.error("Upload error:", errorMessage);
          if (xhr) {
            console.error("Server response:", xhr.responseText);
          }
          // Marquer le fichier comme échoué
          file.previewElement.classList.add("dz-error");
        });
      },
    });
  }
}
