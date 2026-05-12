export function initClients() {
  const form = document.querySelector("#modal-client");
  const tableBody = document.querySelector("table tbody");

  document.addEventListener("click", async (e) => {
    const deleteBtn = e.target.closest(".delete-btn");
    if (deleteBtn) {
      const id = deleteBtn.dataset.id;
      if (confirm("Êtes-vous sûr de vouloir supprimer ce client ?")) {
        try {
          const formData = new FormData();
          formData.append("id", id);
          formData.append(
            "csrf_token",
            document.querySelector('input[name="csrf_token"]').value,
          );

          const deleteUrl = document.querySelector("table").dataset.deleteUrl;
          const response = await fetch(deleteUrl, {
            method: "POST",
            body: formData,
          });

          const result = await response.json();

          if (result.success) {
            deleteBtn.closest("tr").remove();
            console.log("Client supprimé avec succès !");
          } else {
            alert("Erreur : " + (result.error || "Une erreur est survenue"));
          }
        } catch (error) {
          console.error("Erreur:", error);
        }
      }
    }
  });

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

          const tdActions = document.createElement("td");
          tdActions.innerHTML = `
            <button class="edit-btn" data-id="${result.id}">✏️</button>
            <button class="delete-btn" data-id="${result.id}">🗑️</button>
          `;
          newRow.appendChild(tdActions);

          if (tableBody) {
            tableBody.prepend(newRow);
          } else {
            window.location.reload();
            return;
          }

          form.reset();
          form.classList.remove("is-active");

          console.log("Client ajouté avec succès ! ID: " + result.id);
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
