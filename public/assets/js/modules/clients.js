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

  // Protection XSS pour l'insertion dynamique de texte
  function escape(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  function resetModalToAdd() {
    modalTitle.textContent = "Ajouter un client";
    modalBtn.textContent = "✓ Ajouter";
    form.action = form.action.replace("/update", "/add");
    clientIdInput.value = "";
    form.reset();
  }

  function updateRowInTable(id, data) {
    const row = table.querySelector(`tr[data-id="${id}"]`);
    if (row) {
      row.querySelector(".c-nom").textContent = data.get("nom");
      row.querySelector(".c-email").textContent = data.get("email");
      row.querySelector(".c-siret").textContent = data.get("siret");
      row.querySelector(".c-adresse").textContent = data.get("adresse");
      row.querySelector(".c-code_postal").textContent = data.get("code_postal");
      row.querySelector(".c-ville").textContent = data.get("ville");
      row.querySelector(".c-telephone").textContent = data.get("telephone");
      row.querySelector(".c-notes").textContent = data.get("notes");
    }
  }

  function addRowToTable(id, data) {
    const tbody = table.querySelector("tbody");
    const row = document.createElement("tr");
    row.dataset.id = id;
    row.innerHTML = `
      <td class="c-nom">${escape(data.get("nom"))}</td>
      <td class="c-email">${escape(data.get("email"))}</td>
      <td class="c-siret">${escape(data.get("siret"))}</td>
      <td class="c-adresse">${escape(data.get("adresse"))}</td>
      <td class="c-code_postal">${escape(data.get("code_postal"))}</td>
      <td class="c-ville">${escape(data.get("ville"))}</td>
      <td class="c-telephone">${escape(data.get("telephone"))}</td>
      <td class="c-notes">${escape(data.get("notes"))}</td>
      <td>
          <button class="edit-btn" data-id="${id}">✏️</button>
          <button class="delete-btn" data-id="${id}">🗑️</button>
      </td>
    `;
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
          document.getElementById("client-nom").value = client.nom || "";
          document.getElementById("client-email").value = client.email || "";
          document.getElementById("client-siret").value = client.siret || "";
          document.getElementById("client-tel").value = client.telephone || "";
          document.getElementById("client-adresse").value = client.adresse || "";
          document.getElementById("client-code-postal").value = client.code_postal || "";
          document.getElementById("client-ville").value = client.ville || "";
          document.getElementById("client-notes").value = client.notes || "";

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
