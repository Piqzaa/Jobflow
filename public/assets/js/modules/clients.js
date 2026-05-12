export function initClients() {
  const form = document.querySelector("#modal-client");
  const tableBody = document.querySelector("table tbody");

  if (form) {
    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(form);

      try {
        const response = await fetch(form.action || "/clients/add", {
          method: "POST",
          body: formData,
        });

        const result = await response.json();

        if (result.success) {
          const newRow = document.createElement("tr");

          const fields = [
            "nom",
            "email",
            "siret",
            "adresse",
            "code_postal",
            "ville",
            "telephone",
            "notes",
          ];

          fields.forEach((field) => {
            const td = document.createElement("td");
            td.textContent = formData.get(field) || "";
            newRow.appendChild(td);
          });

          if (tableBody) {
            tableBody.prepend(newRow);
          } else {
            window.location.reload();
            return;
          }

          form.reset();
          form.classList.remove("is-active");

          console.log("Client ajouté avec succès !");
        } else {
          alert("Erreur : " + (result.error || "Une erreur est survenue"));
        }
      } catch (error) {
        console.error("Erreur:", error);
        alert("Erreur technique lors de l'envoi.");
      }
    });
  }
}
