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
  const tvaCheckbox = document.getElementById("devis-tva-applicable");
  const totalHtSpan = document.getElementById("total-ht");
  const totalTvaSpan = document.getElementById("total-tva");
  const totalTtcSpan = document.getElementById("total-ttc");
  const tvaRow = document.getElementById("total-tva-row");
  const container = document.getElementById("devis-items-container");
  const addItemBtn = document.getElementById("add-item-row");

  // --- Event listeners ---

  addBtn?.addEventListener("click", () => {
    resetModal();
    openModal(modal);
  });

  addItemBtn?.addEventListener("click", () => {
    container.appendChild(createItemRow());
    calculateTotals();
  });

  container.addEventListener("click", (e) => {
    if (!e.target.closest(".remove-item-row")) return;
    const rows = container.querySelectorAll(".devis-item-row");
    if (rows.length > 1) {
      e.target.closest(".devis-item-row").remove();
      calculateTotals();
    } else {
      alert("Au moins une ligne requise.");
    }
  });

  modal.addEventListener("input", (e) => {
    if (
      !e.target.classList.contains("item-qty") &&
      !e.target.classList.contains("item-price")
    )
      return;
    calculateTotals();
  });

  tvaCheckbox.addEventListener("change", calculateTotals);

  table.addEventListener("click", async (e) => {
    const pdfBtn = e.target.closest(".view-pdf-btn");
    if (pdfBtn) {
      window.open(`/devis/pdf?id=${pdfBtn.dataset.id}`, "_blank");
      return;
    }

    const editBtn = e.target.closest(".edit-btn");
    if (editBtn) {
      await handleEdit(editBtn);
      return;
    }

    const deleteBtn = e.target.closest(".delete-btn");
    if (deleteBtn) {
      await handleDelete(deleteBtn);
      return;
    }

    const convertBtn = e.target.closest(".convert-btn");
    if (convertBtn) {
      handleConvert(convertBtn);
      return;
    }
  });

  table.addEventListener("change", async (e) => {
    if (!e.target.classList.contains("status-select")) return;
    await handleStatusChange(e.target);
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
        updateDevisRow(formData);
      } else {
        addDevisToTable(formData, data.id);
      }
      closeModal(modal);
    } catch (err) {
      console.error(err);
      alert("Une erreur est survenue lors de l'enregistrement.");
    }
  });

  // --- Functions ---

  function calculateTotals() {
    let totalHt = 0;

    container.querySelectorAll(".devis-item-row").forEach((row) => {
      const qty = parseFloat(row.querySelector(".item-qty").value) || 0;
      const price = parseFloat(row.querySelector(".item-price").value) || 0;
      totalHt += qty * price;
    });

    const isTvaApplicable = tvaCheckbox.checked;
    const totalTva = isTvaApplicable ? totalHt * 0.2 : 0;
    const totalTtc = totalHt + totalTva;

    totalHtSpan.textContent = totalHt.toFixed(2);
    totalTvaSpan.textContent = totalTva.toFixed(2);
    totalTtcSpan.textContent = totalTtc.toFixed(2);
    tvaRow.style.display = isTvaApplicable ? "flex" : "none";
  }

  function createItemRow() {
    const row = document.createElement("div");
    row.className = "devis-item-row";

    row.innerHTML = `
      <div class="devis-item-row__field devis-item-row__field--designation">
        <input type="text" name="item_designation[]" placeholder="Désignation" class="form-control" required>
      </div>
      <div class="devis-item-row__field devis-item-row__field--qty">
        <input type="number" name="item_quantite[]" placeholder="Qté" class="form-control item-qty" value="1" step="0.01" required>
      </div>
      <div class="devis-item-row__field devis-item-row__field--price">
        <input type="number" name="item_prix[]" placeholder="Prix HT" class="form-control item-price" step="0.01" required>
      </div>
      <button type="button" class="btn-action btn-action--danger remove-item-row">
        <i class="ri-close-line"></i>
      </button>
    `;

    return row;
  }

  function resetModal() {
    modalTitle.textContent = "Ajouter un devis";
    modalBtn.querySelector("span").textContent = "Enregistrer";
    form.action = "/devis/add";
    devisIdInput.value = "";
    form.reset();
    container.innerHTML = "";
    container.appendChild(createItemRow());
    calculateTotals();
  }

  function updateDevisRow(formData) {
    const row = table.querySelector(`tr[data-id="${devisIdInput.value}"]`);
    if (!row) return;

    const clientSelect = document.getElementById("devis-client-id");
    const clientNom = clientSelect.options[clientSelect.selectedIndex].text;

    row.querySelector(".d-client .text-main").textContent = clientNom;
    row.querySelector(".d-date-validite .text-sub").textContent =
      formData.get("date_validite");
    row.querySelector(".d-montant-ttc .text-main").textContent = formatCurrency(
      parseFloat(totalTtcSpan.textContent),
    );
  }

  function addDevisToTable(formData, id) {
    const clientSelect = document.getElementById("devis-client-id");
    const clientNom = clientSelect.options[clientSelect.selectedIndex].text;
    const numero = document.getElementById("devis-numero").value;

    const tr = document.createElement("tr");
    tr.dataset.id = id;

    tr.innerHTML = `
      <td class="d-numero" data-label="Numéro"><span class="text-main"></span></td>
      <td class="d-client" data-label="Client"><span class="text-main"></span></td>
      <td class="d-date-validite" data-label="Validité"><span class="text-sub"></span></td>
      <td class="d-montant-ttc" data-label="Montant TTC"><span class="text-main"></span></td>
      <td class="d-statut" data-label="Statut">
        <select class="status-select badge-select badge--brouillon" data-id="${id}">
          <option value="brouillon" selected>Brouillon</option>
          <option value="envoye">Envoyé</option>
          <option value="accepte">Accepté</option>
          <option value="refuse">Refusé</option>
          <option value="expire">Expiré</option>
        </select>
      </td>
      <td>
        <div class="table-actions">
          <button class="btn-action view-pdf-btn" data-id="${id}" title="Voir PDF"><i class="ri-file-pdf-line"></i></button>
          <button class="btn-action edit-btn" data-id="${id}" title="Modifier"><i class="ri-pencil-line"></i></button>
          <button class="btn-action btn-action--danger delete-btn" data-id="${id}" title="Supprimer"><i class="ri-delete-bin-line"></i></button>
        </div>
      </td>
    `;

    tr.querySelector(".d-numero .text-main").textContent = numero;
    tr.querySelector(".d-client .text-main").textContent = clientNom;
    tr.querySelector(".d-date-validite .text-sub").textContent =
      formData.get("date_validite");
    tr.querySelector(".d-montant-ttc .text-main").textContent = formatCurrency(
      parseFloat(totalTtcSpan.textContent),
    );

    table.querySelector("tbody").prepend(tr);
  }

  function formatCurrency(amount) {
    return (
      new Intl.NumberFormat("fr-FR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(amount) + " €"
    );
  }

  async function handleDelete(btn) {
    if (!confirm("Voulez-vous vraiment supprimer ce devis ?")) return;

    const csrfToken = document.querySelector('input[name="csrf_token"]').value;

    try {
      const response = await fetch(table.dataset.deleteUrl, {
        method: "POST",
        body: new URLSearchParams({
          devis_id: btn.dataset.id,
          csrf_token: csrfToken,
        }),
      });
      const data = await response.json();

      if (!data.success) {
        alert(data.error || "Erreur lors de la suppression");
        return;
      }
      btn.closest("tr").remove();
    } catch (err) {
      console.error(err);
    }
  }

  async function handleEdit(btn) {
    try {
      const response = await fetch(
        `${table.dataset.getUrl}?id=${btn.dataset.id}`,
      );
      const data = await response.json();
      if (!data.success) return;

      modalTitle.textContent = "Modifier le devis";
      modalBtn.querySelector("span").textContent = "Mettre à jour";
      form.action = table.dataset.updateUrl;
      devisIdInput.value = data.devis.id;

      document.getElementById("devis-numero").value = data.devis.numero;
      document.getElementById("devis-client-id").value = data.devis.client_id;
      document.getElementById("devis-date-emission").value =
        data.devis.date_emission;
      document.getElementById("devis-date-validite").value =
        data.devis.date_validite;
      document.getElementById("devis-notes").value = data.devis.notes || "";
      tvaCheckbox.checked = parseFloat(data.devis.montant_tva) > 0;

      container.innerHTML = "";
      data.items.forEach((item) => {
        const row = createItemRow();
        row.querySelector('input[name="item_designation[]"]').value =
          item.designation;
        row.querySelector(".item-qty").value = item.quantite;
        row.querySelector(".item-price").value = item.prix_unitaire;
        container.appendChild(row);
      });

      calculateTotals();
      openModal(modal);
    } catch (err) {
      console.error(err);
      alert("Erreur lors de la récupération du devis.");
    }
  }

  async function handleStatusChange(select) {
    const csrfToken = document.querySelector('input[name="csrf_token"]').value;

    try {
      const response = await fetch(table.dataset.statusUrl, {
        method: "POST",
        body: new URLSearchParams({
          devis_id: select.dataset.id,
          status: select.value,
          csrf_token: csrfToken,
        }),
      });
      const data = await response.json();
      if (!data.success) return;

      select.className = `status-select badge-select badge--${select.value}`;

      const actionsDiv = select.closest("tr").querySelector(".table-actions");
      const existingConvertBtn = actionsDiv.querySelector(".convert-btn");

      if (select.value === "accepte" && !existingConvertBtn) {
        const btn = document.createElement("button");
        btn.className = "btn-action convert-btn";
        btn.dataset.id = select.dataset.id;
        btn.title = "Convertir en Facture";
        btn.innerHTML = '<i class="ri-exchange-funds-line"></i>';
        actionsDiv.insertBefore(btn, actionsDiv.querySelector(".edit-btn"));
        return;
      }

      if (select.value !== "accepte" && existingConvertBtn) {
        existingConvertBtn.remove();
      }
    } catch (err) {
      console.error(err);
    }
  }

  function handleConvert(btn) {
    if (!confirm("Convertir ce devis en facture ?")) return;
    window.location.href = `/devis/convert?id=${btn.dataset.id}`;
  }
}
