export function initTva() {
  const tvaForm = document.querySelector(".tva-form");
  const tvaTable = document.querySelector("#tva-table");
  const tbody = tvaTable?.querySelector("tbody");

  // Met à jour les cartes de stats
  const updateStats = (stats) => {
    const cards = document.querySelectorAll(".card--stats");
    if (cards.length >= 3) {
      cards[0].querySelector(".card__stat-value").textContent = stats.collectee;
      cards[1].querySelector(".card__stat-value").textContent = stats.payee;
      cards[2].querySelector(".card__stat-value").textContent = stats.restante;
    }
  };

  // Gère l'affichage ou le retrait du message "Aucun versement"
  const updateEmptyState = () => {
    const rows = tbody.querySelectorAll("tr");
    const emptyRow = tbody
      .querySelector(".tva-table__empty-state")
      ?.closest("tr");

    if (rows.length === 0 && !emptyRow) {
      tbody.innerHTML = `<tr><td colspan="4">
        <div class="tva-table__empty-state">
            <i class="ri-information-line"></i> Aucun versement enregistré pour le moment.
        </div>
      </td></tr>`;
    } else if (rows.length > 1 && emptyRow) {
      emptyRow.remove();
    }
  };

  // Ajout d'un versement
  tvaForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const formData = new FormData(tvaForm);
    const csrfToken = formData.get("csrf_token");

    const response = await fetch(tvaForm.action, {
      method: "POST",
      body: new URLSearchParams(formData),
    });

    const data = await response.json();
    if (data.success) {
      updateStats(data.stats);

      const emptyState = tbody.querySelector(".tva-table__empty-state");
      if (emptyState) emptyState.closest("tr").remove();

      const tr = document.createElement("tr");
      const date = formData.get("date_paiement").split("-").reverse().join("/");
      const periode = formData.get("periode");
      const montant = parseFloat(formData.get("montant")).toLocaleString(
        "fr-FR",
        { minimumFractionDigits: 2 },
      );

      tr.innerHTML = `
        <td data-label="Date"></td>
        <td data-label="Période" class="c-nom"></td>
        <td data-label="Montant"><strong></strong></td>
        <td>
            <div class="table-actions">
                <form action="/tva/delete" method="POST" class="delete-tva">
                    <input type="hidden" name="csrf_token" value="${csrfToken}">
                    <input type="hidden" name="id" value="${data.id}">
                    <button type="submit" class="btn-action btn-action--danger" title="Supprimer">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </form>
            </div>
        </td>
      `;

      tr.cells[0].textContent = date;
      tr.cells[1].textContent = periode;
      tr.cells[2].querySelector("strong").textContent = `${montant} €`;

      tbody.prepend(tr);
      tvaForm.reset();
    } else {
      alert(data.error);
    }
  });

  // Suppression d'un versement
  tvaTable?.addEventListener("submit", async (e) => {
    if (!e.target.classList.contains("delete-tva")) return;

    e.preventDefault();
    if (!confirm("Voulez-vous vraiment supprimer ce versement ?")) return;

    const form = e.target;
    const response = await fetch(form.action, {
      method: "POST",
      body: new URLSearchParams(new FormData(form)),
    });

    const data = await response.json();
    if (data.success) {
      updateStats(data.stats);
      form.closest("tr").remove();
      updateEmptyState();
    } else {
      alert(data.error || "Erreur lors de la suppression");
    }
  });
}
