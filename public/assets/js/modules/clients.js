import { openModal, closeModal, confirmAction } from "./modal.js";
import { showToast } from "./toasts.js";

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
   * Met à jour l'affichage du message "aucun client"
   */
  function updateEmptyView() {
    const tbody = table.querySelector("tbody");
    const msgRow = document.getElementById("no-client-message");
    const rows = tbody.querySelectorAll("tr:not(#no-client-message)");

    if (rows.length === 0) {
      msgRow.classList.remove("is-hidden");
    } else {
      msgRow.classList.add("is-hidden");
    }
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

    // On construit le HTML dynamiquement
    row.innerHTML = `
      <td class="c-nom" data-label="Nom"></td>
      <td class="c-email" data-label="Email"></td>
      <td class="c-ville" data-label="Ville"></td>
      <td class="c-telephone" data-label="Tel"></td>
      <td data-label="Actions">
          <div class="table-actions">
              <button class="btn-action edit-client-btn" data-id="${id}" title="Modifier" aria-label="Modifier">
                  <i class="ri-pencil-line" aria-hidden="true"></i>
              </button>
              <button class="btn-action btn-action--danger delete-client-btn" data-id="${id}" title="Supprimer" aria-label="Supprimer">
                  <i class="ri-delete-bin-line" aria-hidden="true"></i>
              </button>
          </div>
      </td>
    `;

    // Remplissage sécurisé
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
    const editBtn = e.target.closest(".edit-client-btn");
    const deleteBtn = e.target.closest(".delete-client-btn");

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
          showToast(data.error || "Erreur de chargement", "error");
        }
      } catch (err) {
        console.error(err);
      }
    }

    if (deleteBtn) {
      if (!(await confirmAction("Voulez-vous vraiment supprimer ce client ? Cette action est irréversible.", { danger: true, confirmText: "Supprimer" }))) return;
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
          updateEmptyView();
          showToast("Client supprimé.", "success");
        } else {
          showToast(data.error || "Erreur lors de la suppression", "error");
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
        updateEmptyView();
        closeModal(modal);
        form.reset();
        showToast(id ? "Client modifié." : "Client ajouté.", "success");
      } else {
        showToast(data.error || "Erreur lors de la sauvegarde", "error");
      }
    } catch (err) {
      console.error(err);
    }
  });

  updateEmptyView();
}
