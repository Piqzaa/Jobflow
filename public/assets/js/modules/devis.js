import { openModal, closeModal } from "./modal.js";

export function initDevis() {
  const table = document.getElementById("devis-table");
  const modal = document.getElementById("modal-devis");
  if (!table || !modal) return;

  const form = modal;
  const modalTitle = modal.querySelector(".modal__title");
  const modalBtn = document.getElementById("modal-save-devis-btn");
  const devisIdInput = document.getElementById("devis-id");
  const addBtn = document.getElementById("add-devis");
  const container = document.getElementById("devis-items-container");
  const addItemBtn = document.getElementById("add-item-row");
  const tvaCheckbox = document.getElementById("devis-tva-applicable");
  
  // Éléments pour les calculs de totaux
  const totalHtSpan = document.getElementById("total-ht");
  const totalTvaSpan = document.getElementById("total-tva");
  const totalTtcSpan = document.getElementById("total-ttc");
  const tvaRow = document.getElementById("tva-row");

  // --- GESTION DE LA MODALE & DU FORMULAIRE ---

  addBtn?.addEventListener("click", () => {
    resetModal();
    openModal(modal);
  });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    const isEditing = devisIdInput.value !== "";

    try {
      const response = await fetch(form.action, {
        method: "POST",
        body: new URLSearchParams(formData),
      });

      const data = await response.json();
      if (!data.success) {
        alert(data.error || "Erreur lors de la sauvegarde");
        return;
      }

      if (isEditing) {
        const row = table.querySelector(`tr[data-id="${devisIdInput.value}"]`);
        if (row) updateRowUI(row, formData);
      } else {
        addDevisToTable(formData, data.id);
      }
      
      closeModal(modal);
    } catch (err) {
      console.error(err);
      alert("Une erreur est survenue.");
    }
  });

  // --- ACTIONS DU TABLEAU (EDIT / DELETE / PDF) ---

  table.addEventListener("click", async (e) => {
    const target = e.target;
    const pdfBtn = target.closest(".view-pdf-btn");
    const editBtn = target.closest(".edit-btn");
    const deleteBtn = target.closest(".delete-btn");

    if (pdfBtn) window.open(`/devis/pdf?id=${pdfBtn.dataset.id}`, "_blank");
    if (editBtn) handleEdit(editBtn);
    if (deleteBtn) handleDelete(deleteBtn);
  });

  async function handleDelete(btn) {
    if (!confirm("Voulez-vous vraiment supprimer ce devis ?")) return;

    try {
      const response = await fetch(table.dataset.deleteUrl, {
        method: "POST",
        body: new URLSearchParams({ 
          devis_id: btn.dataset.id, 
          csrf_token: document.querySelector('input[name="csrf_token"]').value 
        }),
      });
      const data = await response.json();
      if (data.success) btn.closest("tr").remove();
    } catch (err) {
      console.error(err);
    }
  }

  async function handleEdit(btn) {
    const url = `${table.dataset.getUrl}?id=${btn.dataset.id}`;

    try {
      const response = await fetch(url);
      const data = await response.json();
      if (data.success) {
        // Mode édition pour la modale
        modalTitle.textContent = "Modifier le devis";
        modalBtn.textContent = "💾 Mettre à jour";
        form.action = table.dataset.updateUrl;
        devisIdInput.value = data.devis.id;

        document.getElementById("devis-numero").value = data.devis.numero;
        document.getElementById("devis-client-id").value = data.devis.client_id;
        document.getElementById("devis-date-emission").value = data.devis.date_emission;
        document.getElementById("devis-date-validite").value = data.devis.date_validite;
        document.getElementById("devis-notes").value = data.devis.notes || "";
        tvaCheckbox.checked = parseFloat(data.devis.montant_tva) > 0;

        // On recrée les lignes d'articles
        container.innerHTML = "";
        data.items.forEach(item => {
          const row = createItemRow();
          row.querySelector(".item-designation").value = item.designation;
          row.querySelector(".item-qty").value = item.quantite;
          row.querySelector(".item-price").value = item.prix_unitaire;
          container.appendChild(row);
        });

        calculateTotals();
        openModal(modal);
      }
    } catch (err) {
      console.error(err);
    }
  }

  // --- LOGIQUE DES ARTICLES ---

  addItemBtn?.addEventListener("click", () => {
    container.appendChild(createItemRow());
    calculateTotals();
  });

  // Suppression d'une ligne d'article
  container.addEventListener("click", (e) => {
    if (e.target.closest(".remove-item-row")) {
      const rows = container.querySelectorAll(".devis-item-row");
      if (rows.length > 1) {
        e.target.closest(".devis-item-row").remove();
        calculateTotals();
      }
    }
  });

  modal.addEventListener("input", (e) => {
    if (e.target.classList.contains("item-qty") || e.target.classList.contains("item-price")) {
      calculateTotals();
    }
  });

  tvaCheckbox.addEventListener("change", calculateTotals);

  // --- HELPERS UI ---

  function calculateTotals() {
    let totalHt = 0;
    const rows = container.querySelectorAll(".devis-item-row");

    rows.forEach(row => {
      const qty = parseFloat(row.querySelector(".item-qty").value) || 0;
      const price = parseFloat(row.querySelector(".item-price").value) || 0;
      totalHt += qty * price;
    });

    const isTvaApplicable = tvaCheckbox.checked;
    const totalTva = totalHt * (isTvaApplicable ? 0.2 : 0);
    const totalTtc = totalHt + totalTva;

    totalHtSpan.textContent = totalHt.toFixed(2);
    totalTvaSpan.textContent = totalTva.toFixed(2);
    totalTtcSpan.textContent = totalTtc.toFixed(2);

    tvaRow.classList.toggle("is-hidden", !isTvaApplicable);
  }

  function createItemRow() {
    const row = document.createElement("div");
    row.className = "devis-item-row";
    row.innerHTML = `
      <input type="text" name="item_designation[]" placeholder="Désignation" class="modal__input item-designation" required>
      <input type="number" name="item_quantite[]" value="1" step="1" class="modal__input item-qty" required>
      <input type="number" name="item_prix[]" placeholder="Prix Unit. HT" class="modal__input item-price" required>
      <button type="button" class="remove-item-row">✖</button>
    `;
    return row;
  }

  function resetModal() {
    modalTitle.textContent = "Ajouter un devis";
    modalBtn.textContent = "✓ Enregistrer";
    form.action = "/devis/add";
    devisIdInput.value = "";
    form.reset();
    container.innerHTML = "";
    container.appendChild(createItemRow());
    calculateTotals();
  }

  function updateRowUI(row, formData) {
    const clientSelect = document.getElementById("devis-client-id");
    const clientNom = clientSelect.options[clientSelect.selectedIndex].text;
    
    row.querySelector(".d-client").textContent = clientNom;
    row.querySelector(".d-date-emission").textContent = formData.get("date_emission");
    row.querySelector(".d-date-validite").textContent = formData.get("date_validite");
    row.querySelector(".d-montant-ttc").textContent = formatCurrency(parseFloat(totalTtcSpan.textContent));
  }

  function addDevisToTable(formData, id) {
    const tbody = table.querySelector("tbody");
    const tr = document.createElement("tr");
    tr.dataset.id = id;

    const clientSelect = document.getElementById("devis-client-id");
    const clientNom = clientSelect.options[clientSelect.selectedIndex].text;
    const numero = document.getElementById("devis-numero").value;
    const ttcFormatted = formatCurrency(parseFloat(totalTtcSpan.textContent));

    tr.innerHTML = `
      <td class="d-numero">${numero}</td>
      <td class="d-client">${clientNom}</td>
      <td class="d-date-emission">${formData.get("date_emission")}</td>
      <td class="d-date-validite">${formData.get("date_validite")}</td>
      <td class="d-montant-ttc">${ttcFormatted}</td>
      <td class="d-statut"><span class="badge badge--draft">Brouillon</span></td>
      <td class="table__actions">
        <button class="view-pdf-btn action-btn" data-id="${id}">👁️</button>
        <button class="edit-btn action-btn" data-id="${id}">✏️</button>
        <button class="delete-btn action-btn" data-id="${id}">🗑️</button>
      </td>
    `;
    tbody.prepend(tr);
  }

  function formatCurrency(amount) {
    return new Intl.NumberFormat("fr-FR", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(amount) + " €";
  }
}
