import { openModal, closeModal } from "./modal.js";

export function initClients() {
  const table = document.getElementById("clients-table");
  const modal = document.getElementById("modal-client");
  if (!table || !modal) return;

  const modalTitle = modal.querySelector(".modal__title");
  const modalBtn = document.getElementById("modal-addClient-btn");
  const form = modal;
  const clientIdInput = document.getElementById("client-id");

  const addBtn = document.getElementById("add-client");
  const getUrl = table.dataset.getUrl;
  const deleteUrl = table.dataset.deleteUrl;
  const updateUrl = table.dataset.updateUrl;

  function resetModalToAdd() {
    modalTitle.textContent = "Ajouter un client";
    modalBtn.textContent = "✓ Ajouter";
    form.action = form.action.replace("/update", "/add");
    clientIdInput.value = "";
    form.reset();
  }

  /**
   * Met à jour une ligne du tableau de manière sécurisée.
   * On vérifie si l'élément existe avant de modifier son texte.
   */
  function updateRowInTable(id, data) {
    const row = table.querySelector(`tr[data-id="${id}"]`);
    if (!row) return;

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
      const cell = row.querySelector(`.c-${field}`);
      if (cell) {
        cell.textContent = data.get(field);
      }
    });
  }

  /**
   * Ajoute une nouvelle ligne au tableau.
   * Utilise la structure HTML actuelle (simplifiée).
   */
  function addRowToTable(id, data) {
    const tbody = table.querySelector("tbody");
    const row = document.createElement("tr");
    row.dataset.id = id;

    // On construit le HTML dynamiquement en fonction des données reçues
    // Note: On n'ajoute que les colonnes présentes dans ton nouveau design (Nom, Email, Ville, Tel)
    row.innerHTML = `
      <td class="c-nom" data-label="Nom"></td>
      <td class="c-email" data-label="Email"></td>
      <td class="c-ville" data-label="Ville"></td>
      <td class="c-telephone" data-label="Tel"></td>
      <td data-label="Actions">
          <div class="table-actions">
              <button class="btn-action edit-btn" data-id="${id}" title="Modifier">
                  <i class="ri-pencil-line"></i>
              </button>
              <button class="btn-action btn-action--danger delete-btn" data-id="${id}" title="Supprimer">
                  <i class="ri-delete-bin-line"></i>
              </button>
          </div>
      </td>
    `;

    // Remplissage sécurisé (XSS)
    const fieldsMapping = {
      ".c-nom": data.get("nom"),
      ".c-email": data.get("email"),
      ".c-ville": data.get("ville"),
      ".c-telephone": data.get("telephone"),
    };

    Object.entries(fieldsMapping).forEach(([selector, value]) => {
      const el = row.querySelector(selector);
      if (el) el.textContent = value;
    });

    tbody.prepend(row);
  }

  addBtn.addEventListener("click", () => {
    resetModalToAdd();
  });

  table.addEventListener("click", async (e) => {
    const editBtn = e.target.closest(".edit-btn");
    const deleteBtn = e.target.closest(".delete-btn");

    if (editBtn) {
      const clientId = editBtn.dataset.id;
      try {
        const response = await fetch(`${getUrl}?id=${clientId}`);
        const data = await response.json();

        if (data.success) {
          const client = data.client;
          modalTitle.textContent = "Modifier le client";
          modalBtn.textContent = "✓ Modifier";
          form.action = updateUrl;

          clientIdInput.value = client.id;

          // Mapping des champs de la modal
          const inputsMapping = {
            "client-nom": client.nom,
            "client-email": client.email,
            "client-siret": client.siret,
            "client-tel": client.telephone,
            "client-adresse": client.adresse,
            "client-code-postal": client.code_postal,
            "client-ville": client.ville,
            "client-notes": client.notes,
          };

          Object.entries(inputsMapping).forEach(([id, value]) => {
            const input = document.getElementById(id);
            if (input) input.value = value || "";
          });

          openModal(modal);
        } else {
          alert(data.error || "Erreur de chargement");
        }
      } catch (err) {
        console.error(err);
      }
    }

    if (deleteBtn) {
      if (!confirm("Supprimer ce client ?")) return;
      const clientId = deleteBtn.dataset.id;
      const csrfToken = document.querySelector('[name="csrf_token"]').value;

      try {
        const response = await fetch(deleteUrl, {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({ id: clientId, csrf_token: csrfToken }),
        });

        const data = await response.json();
        if (data.success) {
          deleteBtn.closest("tr").remove();
        } else {
          alert(data.error || "Erreur lors de la suppression");
        }
      } catch (err) {
        console.error(err);
      }
    }
  });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    const id = clientIdInput.value;

    try {
      const response = await fetch(form.action, {
        method: "POST",
        body: new URLSearchParams(formData),
      });

      const data = await response.json();
      if (data.success) {
        if (id) {
          updateRowInTable(id, formData);
        } else {
          addRowToTable(data.id, formData);
        }
        closeModal(modal);
        form.reset();
      } else {
        alert(data.error || "Erreur lors de la sauvegarde");
      }
    } catch (err) {
      console.error(err);
    }
  });
}
