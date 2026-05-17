import { openModal, closeModal } from "./modal.js";

export function initDevis() {
  const table = document.getElementById("devis-table");
  const modal = document.getElementById("modal-devis");
  if (!table || !modal) return;

  const modalTitle = modal.querySelector(".modal__title");
  const modalBtn = document.getElementById("modal-save-devis-btn");
  const form = modal;
  const devisIdInput = document.getElementById("devis-id");

  const addBtn = document.getElementById("add-devis");

  // --- LOGIQUE DE CALCUL DES TOTAUX ---
  const tvaCheckbox = document.getElementById("devis-tva-applicable");
  const totalHtSpan = document.getElementById("total-ht");
  const totalTvaSpan = document.getElementById("total-tva");
  const totalTtcSpan = document.getElementById("total-ttc");
  const tvaRow = document.getElementById("tva-row");

  function calculateTotals() {
    let totalHt = 0;
    const rows = document.querySelectorAll(".devis-item-row");

    rows.forEach(row => {
        const qty = parseFloat(row.querySelector(".item-qty").value) || 0;
        const price = parseFloat(row.querySelector(".item-price").value) || 0;
        totalHt += qty * price;
    });

    const isTvaApplicable = tvaCheckbox.checked;
    const tvaRate = isTvaApplicable ? 0.20 : 0;
    const totalTva = totalHt * tvaRate;
    const totalTtc = totalHt + totalTva;

    // Mise à jour de l'affichage
    totalHtSpan.textContent = totalHt.toFixed(2);
    totalTvaSpan.textContent = totalTva.toFixed(2);
    totalTtcSpan.textContent = totalTtc.toFixed(2);

    // Cacher la ligne TVA si non applicable
    tvaRow.style.display = isTvaApplicable ? "block" : "none";
  }

  // Écouteurs pour le calcul
  modal.addEventListener("input", (e) => {
    if (e.target.classList.contains("item-qty") || e.target.classList.contains("item-price")) {
        calculateTotals();
    }
  });

  tvaCheckbox.addEventListener("change", calculateTotals);

  // --- GESTION DYNAMIQUE DES LIGNES ---
  const container = document.getElementById("devis-items-container");
  const addItemBtn = document.getElementById("add-item-row");

  if (addItemBtn) {
    addItemBtn.addEventListener("click", () => {
        const row = document.createElement("div");
        row.className = "devis-item-row";
        row.style.display = "flex";
        row.style.gap = "10px";
        row.style.marginBottom = "10px";
        row.innerHTML = `
            <input type="text" name="item_designation[]" placeholder="Désignation" class="modal__input" style="flex: 3;" required>
            <input type="number" name="item_quantite[]" placeholder="Qté" class="modal__input item-qty" style="flex: 1;" value="1" step="0.01" required>
            <input type="number" name="item_prix[]" placeholder="Prix Unit. HT" class="modal__input item-price" style="flex: 1;" step="0.01" required>
            <button type="button" class="remove-item-row" style="background: none; border: none; cursor: pointer; color: #dc3545;">✖</button>
        `;
        container.appendChild(row);
        calculateTotals();
    });
  }

  // Délégation d'événement pour la suppression de ligne
  container.addEventListener("click", (e) => {
    if (e.target.closest(".remove-item-row")) {
        const rows = container.querySelectorAll(".devis-item-row");
        if (rows.length > 1) {
            e.target.closest(".devis-item-row").remove();
            calculateTotals();
        } else {
            alert("Un devis doit comporter au moins une ligne.");
        }
    }
  });

  function resetModalToAdd() {
    modalTitle.textContent = "Ajouter un devis";
    modalBtn.textContent = "✓ Enregistrer";
    form.action = "/devis/add";
    devisIdInput.value = "";
    form.reset();
  }

  if (addBtn) {
    addBtn.addEventListener("click", () => {
        resetModalToAdd();
        openModal(modal);
    });
  }

  // Gestion des clics dans le tableau (Edit / Delete / PDF)
  table.addEventListener("click", async (e) => {
    const editBtn = e.target.closest(".edit-btn");
    const deleteBtn = e.target.closest(".delete-btn");
    const viewPdfBtn = e.target.closest(".view-pdf-btn");

    if (viewPdfBtn) {
        const id = viewPdfBtn.dataset.id;
        window.open(`/devis/pdf?id=${id}`, '_blank');
    }

    if (editBtn) {
        alert("Modification bientôt disponible (en attente du Controller) !");
    }

    if (deleteBtn) {
        alert("Suppression bientôt disponible (en attente du Controller) !");
    }
  });

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    alert("Sauvegarde bientôt disponible (en attente du Controller) !");
    closeModal(modal);
  });
}
