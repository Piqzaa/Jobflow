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
  const tvaRow = document.getElementById("total-tva-row");
  const container = document.getElementById("facture-items-container");
  const addItemBtn = document.getElementById("add-item-row");

  /**
   * Met à jour l'affichage du message "aucune facture"
   */
  function updateEmptyView() {
    const tbody = table.querySelector("tbody");
    const msgRow = document.getElementById("no-facture-message");
    const rows = tbody.querySelectorAll("tr:not(#no-facture-message)");

    if (rows.length === 0) {
      msgRow.classList.remove("is-hidden");
    } else {
      msgRow.classList.add("is-hidden");
    }
  }

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
      const rows = container.querySelectorAll(".modal-item-row");
      if (rows.length > 1) {
        e.target.closest(".modal-item-row").remove();
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
    const pdfBtn = e.target.closest(".view-pdf-btn");
    if (pdfBtn) {
      window.open(`/facture/pdf?id=${pdfBtn.dataset.id}`, "_blank");
      return;
    }

    const editBtn = e.target.closest(".edit-btn");
    const deleteBtn = e.target.closest(".delete-btn");

    if (editBtn && !editBtn.classList.contains("is-hidden"))
      await handleEdit(editBtn);
    if (deleteBtn && !deleteBtn.classList.contains("is-hidden"))
      await handleDelete(deleteBtn);
  });

  table.addEventListener("change", async (e) => {
    if (e.target.classList.contains("status-select")) {
      await handleStatusChange(e.target);
    }
  });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    const id = factureIdInput.value;
    const isEditing = id !== "";
    const actionUrl = isEditing
      ? table.dataset.updateUrl
      : table.dataset.addUrl || "/facture/add";

    try {
      const response = await fetch(actionUrl, {
        method: "POST",
        body: new URLSearchParams(formData),
      });

      const data = await response.json();
      if (data.success) {
        if (isEditing) {
          updateFactureInTable(id, formData);
        } else {
          addFactureToTable(formData, data.id);
        }
        updateEmptyView();
        closeModal(modal);
        form.reset();
      } else {
        alert(data.error || "Erreur lors de la sauvegarde");
      }
    } catch (err) {
      console.error(err);
      alert("Une erreur est survenue lors de l'enregistrement.");
    }
  });

  updateEmptyView();

  // functions

  function calculateTotals() {
    let totalHt = 0;
    const rows = document.querySelectorAll(".modal-item-row");

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
      tvaRow.classList.remove("is-hidden");
    } else {
      tvaRow.classList.add("is-hidden");
    }
  }

  function createItemRow() {
    const row = document.createElement("div");
    row.className = "modal-item-row";

    row.innerHTML = `
    <div class="modal-item-row__designation">
      <input type="text" name="item_designation[]" placeholder="Désignation" class="form-control item-designation" required>
    </div>
    <div class="modal-item-row__qty">
      <input type="number" name="item_quantite[]" placeholder="Qté" class="form-control item-qty" value="1" step="0.01" required>
    </div>
    <div class="modal-item-row__price">
      <input type="number" name="item_prix[]" placeholder="Prix HT" class="form-control item-price" step="0.01" required>
    </div>
    <button type="button" class="btn-action btn-action--danger remove-item-row" title="Supprimer">
      <i class="ri-delete-bin-line"></i>
    </button>
  `;

    return row;
  }

  function resetModal() {
    modalTitle.textContent = "Ajouter une facture";
    modalBtn.textContent = "Enregistrer";
    factureIdInput.value = "";
    form.reset();
    container.innerHTML = "";
    container.appendChild(createItemRow());
    calculateTotals();
  }
  function addFactureToTable(formData, id) {
    const tbody = table.querySelector("tbody");
    const tr = document.createElement("tr");
    tr.dataset.id = id;

    const clientSelect = document.getElementById("facture-client-id");
    const clientNom = clientSelect.options[clientSelect.selectedIndex].text;
    const numero = document.getElementById("facture-numero").value;
    const dateEch = formData.get("date_echeance");
    const ttcValue = parseFloat(totalTtcSpan.textContent);

    tr.innerHTML = `
    <td class="f-numero" data-label="Numéro">
      <span class="text-main"></span>
    </td>
    <td class="f-client" data-label="Client">
      <span class="text-main"></span>
    </td>
    <td class="f-date-echeance" data-label="Échéance">
      <span class="text-sub"></span>
    </td>
    <td class="f-montant-ttc" data-label="Montant TTC">
      <span class="text-main"></span>
    </td>
    <td class="f-statut" data-label="Statut">
      <select class="status-select badge-select badge--brouillon" data-id="${id}">
        <option value="brouillon" selected>Brouillon</option>
        <option value="envoyee">Envoyée</option>
        <option value="payee">Payée</option>
        <option value="annulee">Annulée</option>
      </select>
    </td>
    <td>
      <div class="table-actions">
        <button class="btn-action view-pdf-btn" data-id="${id}" title="Voir PDF">
          <i class="ri-file-pdf-line"></i>
        </button>
        <button class="btn-action edit-btn" data-id="${id}" title="Modifier">
          <i class="ri-pencil-line"></i>
        </button>
        <button class="btn-action btn-action--danger delete-btn" data-id="${id}" title="Supprimer">
          <i class="ri-delete-bin-line"></i>
        </button>
      </div>
    </td>
  `;

    tr.querySelector(".f-numero span").textContent = numero;
    tr.querySelector(".f-client span").textContent = clientNom;
    tr.querySelector(".f-date-echeance span").textContent = formatDate(dateEch);
    tr.querySelector(".f-montant-ttc span").textContent =
      formatCurrency(ttcValue);

    tbody.prepend(tr);
  }

  function updateFactureInTable(id, formData) {
    const tr = table.querySelector(`tr[data-id="${id}"]`);
    if (!tr) return;

    const clientSelect = document.getElementById("facture-client-id");
    const clientNom = clientSelect.options[clientSelect.selectedIndex].text;

    tr.querySelector(".f-client span").textContent = clientNom;
    tr.querySelector(".f-date-echeance span").textContent = formatDate(
      formData.get("date_echeance"),
    );
    tr.querySelector(".f-montant-ttc span").textContent = formatCurrency(
      parseFloat(totalTtcSpan.textContent),
    );
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
        updateEmptyView();
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
        factureIdInput.value = data.facture.id;

        document.getElementById("facture-numero").value = data.facture.numero;
        document.getElementById("facture-client-id").value =
          data.facture.client_id;
        document.getElementById("facture-date-emission").value =
          data.facture.date_emission;
        document.getElementById("facture-date-echeance").value =
          data.facture.date_echeance;
        document.getElementById("facture-notes").value =
          data.facture.notes || "";
        tvaCheckbox.checked = parseFloat(data.facture.montant_tva) > 0;

        container.innerHTML = "";
        data.items.forEach((item) => {
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

        const actionsTd = select.closest("tr").querySelector("td:last-child");
        const buttons = actionsTd.querySelectorAll(".edit-btn, .delete-btn");

        buttons.forEach((btn) => {
          if (status === "brouillon") {
            btn.classList.remove("is-hidden");
          } else {
            btn.classList.add("is-hidden");
          }
        });
      } else {
        alert(data.error || "Erreur lors de la mise à jour du statut");
      }
    } catch (err) {
      console.error(err);
      alert("Une erreur est survenue lors de la mise à jour du statut.");
    }
  }

  function formatCurrency(amount) {
    return (
      new Intl.NumberFormat("fr-FR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(amount) + " €"
    );
  }

  function formatDate(dateStr) {
    if (!dateStr) return "";
    const [y, m, d] = dateStr.split("-");
    return `${d}/${m}/${y}`;
  }
}
