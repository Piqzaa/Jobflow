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
  const tvaRow = document.getElementById("tva-row");
  const container = document.getElementById("devis-items-container");
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
      const rows = container.querySelectorAll(".devis-item-row");
      if (rows.length > 1) {
        e.target.closest(".devis-item-row").remove();
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
    const editBtn = e.target.closest(".edit-btn");
    const deleteBtn = e.target.closest(".delete-btn");
    const convertBtn = e.target.closest(".convert-btn");

    if (pdfBtn) window.open(`/devis/pdf?id=${pdfBtn.dataset.id}`, "_blank");
    if (editBtn) await handleEdit(editBtn);
    if (deleteBtn) await handleDelete(deleteBtn);
    if (convertBtn) await handleConvert(convertBtn);
  });

  table.addEventListener("change", async (e) => {
    if (e.target.classList.contains("status-select")) {
      await handleStatusChange(e.target);
    }
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
      if (data.success) {
        if (isEditing) {
          const row = table.querySelector(
            `tr[data-id="${devisIdInput.value}"]`,
          );
          if (row) {
            const clientSelect = document.getElementById("devis-client-id");
            const clientNom =
              clientSelect.options[clientSelect.selectedIndex].text;
            const ttcValue = formatCurrency(
              parseFloat(totalTtcSpan.textContent),
            );

            row.querySelector(".d-client").textContent = clientNom;
            row.querySelector(".d-date-emission").textContent =
              formData.get("date_emission");
            row.querySelector(".d-date-validite").textContent =
              formData.get("date_validite");
            row.querySelector(".d-montant-ttc").textContent = ttcValue;
          }
        } else {
          addDevisToTable(formData, data.id);
        }
        closeModal(modal);
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
    const rows = document.querySelectorAll(".devis-item-row");

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
    row.className = "devis-item-row";

    const inputs = [
      {
        name: "item_designation[]",
        placeholder: "Désignation",
        type: "text",
        class: "item-designation",
      },
      {
        name: "item_quantite[]",
        placeholder: "Qté",
        type: "number",
        class: "item-qty",
        value: "1",
        step: "1",
      },
      {
        name: "item_prix[]",
        placeholder: "Prix Unit. HT",
        type: "number",
        class: "item-price",
      },
    ];

    inputs.forEach((cfg) => {
      const input = document.createElement("input");
      input.type = cfg.type;
      input.name = cfg.name;
      input.placeholder = cfg.placeholder;
      input.className = "modal__input" + (cfg.class ? " " + cfg.class : "");
      if (cfg.value) input.value = cfg.value;
      if (cfg.step) input.step = cfg.step;
      input.required = true;
      row.appendChild(input);
    });

    const removeBtn = document.createElement("button");
    removeBtn.type = "button";
    removeBtn.className = "remove-item-row";
    removeBtn.textContent = "✖";
    row.appendChild(removeBtn);

    return row;
  }

  function resetModal() {
    modalTitle.textContent = "Ajouter un devis";
    modalBtn.textContent = "✓ Enregistrer";
    form.action = "/devis/add";
    devisIdInput.value = "";
    form.reset();

    const rows = container.querySelectorAll(".devis-item-row");
    rows.forEach((row, i) => {
      if (i > 0) row.remove();
    });
    calculateTotals();
  }

  function addDevisToTable(formData, id) {
    const tbody = table.querySelector("tbody");
    const tr = document.createElement("tr");
    tr.dataset.id = id; // Permet l'édition immédiate sans rechargement

    const clientSelect = document.getElementById("devis-client-id");
    const clientNom = clientSelect.options[clientSelect.selectedIndex].text;
    const numero = document.getElementById("devis-numero").value;
    const dateEmi = formData.get("date_emission");
    const dateVal = formData.get("date_validite");
    const ttc = totalTtcSpan.textContent;
    const ttcFormatted = formatCurrency(parseFloat(ttc));

    tr.innerHTML = `
      <td class="d-numero"></td>
      <td class="d-client"></td>
      <td class="d-date-emission"></td>
      <td class="d-date-validite"></td>
      <td class="d-montant-ttc"></td>
      <td class="d-statut">
        <select class="status-select badge-select badge--brouillon" data-id="${id}">
          <option value="brouillon" selected>Brouillon</option>
          <option value="envoye">Envoyé</option>
          <option value="accepte">Accepté</option>
          <option value="refuse">Refusé</option>
          <option value="expire">Expiré</option>
        </select>
      </td>
      <td class="table__actions">
        <button class="view-pdf-btn action-btn" data-id="${id}" title="Voir PDF">👁️</button>
        <button class="edit-btn action-btn" data-id="${id}" title="Modifier">✏️</button>
        <button class="delete-btn action-btn" data-id="${id}" title="Supprimer">🗑️</button>
      </td>
    `;

    // Sécurité XSS
    tr.querySelector(".d-numero").textContent = numero;
    tr.querySelector(".d-client").textContent = clientNom;
    tr.querySelector(".d-date-emission").textContent = dateEmi;
    tr.querySelector(".d-date-validite").textContent = dateVal;
    tr.querySelector(".d-montant-ttc").textContent = ttcFormatted;

    tbody.prepend(tr);
  }

  function formatCurrency(amount) {
    return (
      new Intl.NumberFormat("fr-FR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      }).format(amount) + " €"
    );
  }

  // async function

  async function handleDelete(btn) {
    if (!confirm("Voulez-vous vraiment supprimer ce devis ?")) return;

    const id = btn.dataset.id;
    const url = table.dataset.deleteUrl;
    const csrfToken = document.querySelector('input[name="csrf_token"]').value;

    try {
      const response = await fetch(url, {
        method: "POST",
        body: new URLSearchParams({ devis_id: id, csrf_token: csrfToken }),
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
        modalTitle.textContent = "Modifier le devis";
        modalBtn.textContent = "💾 Mettre à jour";
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
      alert("Erreur lors de la récupération du devis.");
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
          devis_id: id,
          status: status,
          csrf_token: csrfToken,
        }),
      });

      const data = await response.json();
      if (data.success) {
        select.className = `status-select badge-select badge--${status}`;
      } else {
        alert(data.error || "Erreur lors de la mise à jour du statut");
      }
    } catch (err) {
      console.error(err);
      alert("Une erreur est survenue lors de la mise à jour du statut.");
    }
  }

  async function handleConvert(btn) {
    if (!confirm("Voulez-vous convertir ce devis en facture ?")) return;

    const id = btn.dataset.id;
    const csrfToken = document.querySelector('input[name="csrf_token"]').value;

    try {
      const response = await fetch("/facture/convert", {
        method: "POST",
        body: new URLSearchParams({ devis_id: id, csrf_token: csrfToken }),
      });

      const data = await response.json();
      if (data.success) {
        alert("Devis converti avec succès !");
        window.location.href = "/factures";
      } else {
        alert(data.error || "Erreur lors de la conversion");
      }
    } catch (err) {
      console.error(err);
      alert("Une erreur est survenue lors de la conversion.");
    }
  }
}
