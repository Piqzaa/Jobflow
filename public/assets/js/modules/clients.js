export function initClients() {
  const table = document.getElementById("clients-table");
  const modal = document.getElementById("modal-client");
  if (!table || !modal) return;

  const modalTitle = modal.querySelector(".modal__title");
  const modalBtn = document.getElementById("modal-addClient-btn");
  const form = modal;
  const clientIdInput = document.getElementById("client-id");

  const addBtn = document.getElementById("add-client");
  const deleteUrl = table.dataset.deleteUrl;
  const getUrl = table.dataset.getUrl;
  const updateUrl = table.dataset.updateUrl;

  function resetModalToAdd() {
    modalTitle.textContent = "Ajouter un client";
    modalBtn.textContent = "✓ Ajouter";
    form.action = form.action.replace("/update", "/add");
    clientIdInput.value = "";
    form.reset();
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
          document.getElementById("client-adresse").value =
            client.adresse || "";
          document.getElementById("client-code-postal").value =
            client.code_postal || "";
          document.getElementById("client-ville").value = client.ville || "";
          document.getElementById("client-notes").value = client.notes || "";

          modal.classList.add("is-active");
        } else {
          alert(data.error || "Erreur lors du chargement du client");
        }
      } catch (err) {
        console.error(err);
        alert("Erreur lors du chargement du client");
      }
    }

    if (deleteBtn) {
      if (!confirm("Voulez-vous vraiment supprimer ce client ?")) return;

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
        alert("Erreur lors de la suppression");
      }
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
        window.location.reload();
      } else {
        alert(data.error || "Erreur lors de la sauvegarde");
      }
    } catch (err) {
      console.error(err);
      alert("Erreur lors de la sauvegarde");
    }
  });
}
