import { openModal, closeModal } from "./modal.js";

export function initDevis() {
  // Elements
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

  // --- Fonctions ---

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
    tvaRow.style.display = isTvaApplicable ? "block" : "none";
  }

  function createItemRow() {
    const row = document.createElement("div");
    row.className = "devis-item-row";
    row.style.cssText = "display: flex; gap: 10px; margin-bottom: 10px;";

    const inputs = [
      {
        name: "item_designation[]",
        placeholder: "Désignation",
        flex: "3",
        type: "text",
      },
      {
        name: "item_quantite[]",
        placeholder: "Qté",
        flex: "1",
        type: "number",
        class: "item-qty",
        value: "1",
        step: "0.01",
      },
      {
        name: "item_prix[]",
        placeholder: "Prix Unit. HT",
        flex: "1",
        type: "number",
        class: "item-price",
        step: "0.01",
      },
    ];

    inputs.forEach((cfg) => {
      const input = document.createElement("input");
      input.type = cfg.type;
      input.name = cfg.name;
      input.placeholder = cfg.placeholder;
      input.className = "modal__input" + (cfg.class ? " " + cfg.class : "");
      input.style.flex = cfg.flex;
      if (cfg.value) input.value = cfg.value;
      if (cfg.step) input.step = cfg.step;
      input.required = true;
      row.appendChild(input);
    });

    const removeBtn = document.createElement("button");
    removeBtn.type = "button";
    removeBtn.className = "remove-item-row";
    removeBtn.style.cssText =
      "background: none; border: none; cursor: pointer; color: #dc3545;";
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

    const clientSelect = document.getElementById("devis-client-id");
    const clientNom = clientSelect.options[clientSelect.selectedIndex].text;
    const numero = document.getElementById("devis-numero").value;
    const dateEmi = formData.get("date_emission");
    const dateVal = formData.get("date_validite");
    const ttc = totalTtcSpan.textContent;

    // Colonnes texte
    [numero, clientNom, dateEmi, dateVal, `${ttc} €`].forEach((text) => {
      const td = document.createElement("td");
      td.textContent = text;
      tr.appendChild(td);
    });

    // Badge statut
    const tdStatus = document.createElement("td");
    const badge = document.createElement("span");
    badge.className = "badge badge--draft";
    badge.textContent = "Brouillon";
    tdStatus.appendChild(badge);
    tr.appendChild(tdStatus);

    // Actions
    const tdActions = document.createElement("td");
    tdActions.className = "table__actions";

    const actions = [
      { class: "view-pdf-btn", icon: "👁️", title: "Voir PDF" },
      { class: "edit-btn", icon: "✏️", title: "Modifier" },
      { class: "delete-btn", icon: "🗑️", title: "Supprimer" },
    ];

    actions.forEach((act) => {
      const btn = document.createElement("button");
      btn.className = `${act.class} action-btn`;
      btn.dataset.id = id;
      btn.title = act.title;
      btn.textContent = act.icon;
      tdActions.appendChild(btn);
    });

    tr.appendChild(tdActions);
    tbody.prepend(tr);
  }

  // --- Listeners ---

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

  addBtn?.addEventListener("click", () => {
    resetModal();
    openModal(modal);
  });

  table.addEventListener("click", async (e) => {
    // --- CLIC SUR VOIR PDF ---
    const pdfBtn = e.target.closest(".view-pdf-btn");
    if (pdfBtn) {
      window.open(`/devis/pdf?id=${pdfBtn.dataset.id}`, "_blank");
      return;
    }

    // --- CLIC SUR SUPPRIMER ---
    const deleteBtn = e.target.closest(".delete-btn");
    if (deleteBtn) {
      if (!confirm("Voulez-vous vraiment supprimer ce devis ?")) return;

      const id = deleteBtn.dataset.id;
      const url = table.dataset.deleteUrl;
      const csrfToken = document.querySelector(
        'input[name="csrf_token"]',
      ).value;

      try {
        const response = await fetch(url, {
          method: "POST",
          body: new URLSearchParams({
            devis_id: id,
            csrf_token: csrfToken,
          }),
        });

        const data = await response.json();
        if (data.success) {
          deleteBtn.closest("tr").remove();
        } else {
          alert(data.error || "Erreur lors de la suppression");
        }
      } catch (err) {
        console.error(err);
        alert("Une erreur est survenue lors de la suppression.");
      }
      return;
    }

    // --- CLIC SUR MODIFIER (À venir) ---
    const editBtn = e.target.closest(".edit-btn");
    if (editBtn) {
      console.log("Modifier le devis", editBtn.dataset.id);
    }
  });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(form);

    try {
      const response = await fetch(form.action, {
        method: "POST",
        body: new URLSearchParams(formData),
      });

      const data = await response.json();
      if (data.success) {
        addDevisToTable(formData, data.id);
        closeModal(modal);
      } else {
        alert(data.error || "Erreur lors de la sauvegarde");
      }
    } catch (err) {
      console.error(err);
    }
  });
}
