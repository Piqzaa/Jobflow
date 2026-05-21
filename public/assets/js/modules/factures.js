import { openModal, closeModal } from "./modal.js";

export function initFactures() {
  const table = document.getElementById("factures-table");
  const modal = document.getElementById("modal-facture");
  if (!table || !modal) return;

  const form = modal;
  const modalTitle = modal.querySelector(".modal__title");
  const modalBtn = document.getElementById("modal-save-facture-btn");
  const factureIdInput = document.getElementById("facture-id");
  const addBtn = document.getElementById("add-facture");

  const tvaCheckbox = document.getElementById("facture-tva-applicable");
  const totalHtSpan = document.getElementById("total-ht");
  const totalTvaSpan = document.getElementById("total-tva");
  const totalTtcSpan = document.getElementById("total-ttc");
  const tvaRow = document.getElementById("tva-row");
  const container = document.getElementById("facture-items-container");
  const addItemBtn = document.getElementById("add-item-row");

  // event listeners

  addBtn?.addEventListener("click", () => {
    resetModal();
    openModal(modal);
  });

  addItemBtn?.addEventListener("click", () => {
    container.appendChild(createItemRow());
    calculateTotals();
  });

  container.addEventListener("click", (e) => {
    if (e.target.closest(".remove-item-row")) {
      const rows = container.querySelectorAll(".facture-item-row");
      if (rows.length > 1) {
        e.target.closest(".facture-item-row").remove();
        calculateTotals();
      } else {
        alert("Au moins une ligne requise.");
      }
    }
  });

  modal.addEventListener("input", (e) => {
    if (
      e.target.classList.contains("item-qty") ||
      e.target.classList.contains("item-price")
    ) {
      calculateTotals();
    }
  });

  tvaCheckbox.addEventListener("change", calculateTotals);

  table.addEventListener("click", async (e) => {
    const editBtn = e.target.closest(".edit-btn");
    const deleteBtn = e.target.closest(".delete-btn");

    if (editBtn) await handleEdit(editBtn);
    if (deleteBtn) await handleDelete(deleteBtn);
  });

  table.addEventListener("change", async (e) => {
    if (e.target.classList.contains("status-select")) {
      await handleStatusChange(e.target);
    }
  });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    const isEditing = factureIdInput.value !== "";

    try {
      const response = await fetch(form.action, {
        method: "POST",
        body: new URLSearchParams(formData),
      });

      const data = await response.json();
      if (data.success) {
        // Pour simplifier, on recharge la page car la gestion complexe des boutons dynamiques (Edit/Delete selon statut) est plus robuste via un refresh
        window.location.reload();
      } else {
        alert(data.error || "Erreur lors de la sauvegarde");
      }
    } catch (err) {
      console.error(err);
      alert("Une erreur est survenue lors de l'enregistrement.");
    }
  });

  // functions

  function calculateTotals() {
    let totalHt = 0;
    const rows = document.querySelectorAll(".facture-item-row");

    rows.forEach((row) => {
      const qty = parseFloat(row.querySelector(".item-qty").value) || 0;
      const price = parseFloat(row.querySelector(".item-price").value) || 0;
      totalHt += qty * price;
    });

    const isTvaApplicable = tvaCheckbox.checked;
    const tvaRate = isTvaApplicable ? 0.2 : 0;
    const totalTva = totalHt * tvaRate;
    const totalTtc = totalHt + totalTva;

    totalHtSpan.textContent = totalHt.toFixed(2);
    totalTvaSpan.textContent = totalTva.toFixed(2);
    totalTtcSpan.textContent = totalTtc.toFixed(2);

    if (isTvaApplicable) {
      tvaRow.style.display = "block";
    } else {
      tvaRow.style.display = "none";
    }
  }

  function createItemRow() {
    const row = document.createElement("div");
    row.className = "facture-item-row";

    row.innerHTML = `
      <input type="text" name="item_designation[]" placeholder="Désignation" class="modal__input" style="flex: 3;" required>
      <input type="number" name="item_quantite[]" placeholder="Qté" class="modal__input item-qty" style="flex: 1;" value="1" step="0.01" required>
      <input type="number" name="item_prix[]" placeholder="Prix Unit. HT" class="modal__input item-price" style="flex: 1;" step="0.01" required>
      <button type="button" class="remove-item-row" style="background: none; border: none; cursor: pointer; color: #dc3545;">✖</button>
    `;

    return row;
  }

  function resetModal() {
    modalTitle.textContent = "Ajouter une facture";
    modalBtn.textContent = "✓ Enregistrer";
    form.action = "/facture/add";
    factureIdInput.value = "";
    form.reset();

    const rows = container.querySelectorAll(".facture-item-row");
    rows.forEach((row, i) => {
      if (i > 0) row.remove();
    });
    calculateTotals();
  }

  async function handleDelete(btn) {
    if (!confirm("Voulez-vous vraiment supprimer cette facture ?")) return;

    const id = btn.dataset.id;
    const url = table.dataset.deleteUrl;
    const csrfToken = document.querySelector('input[name="csrf_token"]').value;

    try {
      const response = await fetch(url, {
        method: "POST",
        body: new URLSearchParams({ id: id, csrf_token: csrfToken }),
      });
      const data = await response.json();
      if (data.success) {
        btn.closest("tr").remove();
      } else {
        alert(data.error || "Erreur lors de la suppression");
      }
    } catch (err) {
      console.error(err);
      alert("Une erreur est survenue lors de la suppression.");
    }
  }

  async function handleEdit(btn) {
    const id = btn.dataset.id;
    const url = table.dataset.getUrl + `?id=` + id;

    try {
      const response = await fetch(url);
      const data = await response.json();
      if (data.success) {
        modalTitle.textContent = "Modifier la facture";
        modalBtn.textContent = "💾 Mettre à jour";
        form.action = table.dataset.updateUrl;
        factureIdInput.value = data.facture.id;

        document.getElementById("facture-numero").value = data.facture.numero;
        document.getElementById("facture-client-id").value = data.facture.client_id;
        document.getElementById("facture-date-emission").value = data.facture.date_emission;
        document.getElementById("facture-date-echeance").value = data.facture.date_echeance;
        document.getElementById("facture-notes").value = data.facture.notes || "";
        tvaCheckbox.checked = parseFloat(data.facture.montant_tva) > 0;

        container.innerHTML = "";
        data.items.forEach((item) => {
          const row = createItemRow();
          row.querySelector('input[name="item_designation[]"]').value = item.designation;
          row.querySelector(".item-qty").value = item.quantite;
          row.querySelector(".item-price").value = item.prix_unitaire;
          container.appendChild(row);
        });

        calculateTotals();
        openModal(modal);
      }
    } catch (err) {
      console.error(err);
      alert("Erreur lors de la récupération de la facture.");
    }
  }

  async function handleStatusChange(select) {
    const id = select.dataset.id;
    const status = select.value;
    const url = table.dataset.statusUrl;
    const csrfToken = document.querySelector('input[name="csrf_token"]').value;

    try {
      const response = await fetch(url, {
        method: "POST",
        body: new URLSearchParams({
          facture_id: id,
          status: status,
          csrf_token: csrfToken,
        }),
      });

      const data = await response.json();
      if (data.success) {
        select.className = `status-select badge-select badge--${status}`;
        // Si on change le statut et que ce n'est plus "brouillon", on devrait normalement masquer Edit/Delete
        // Pour plus de simplicité, on peut recharger la page
        if (status !== 'brouillon') {
            window.location.reload();
        }
      } else {
        alert(data.error || "Erreur lors de la mise à jour du statut");
      }
    } catch (err) {
      console.error(err);
      alert("Une erreur est survenue lors de la mise à jour du statut.");
    }
  }
}
