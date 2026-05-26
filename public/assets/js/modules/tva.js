export function initTva() {
  const deleteForms = document.querySelectorAll(".delete-tva");

  deleteForms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      if (!confirm("Voulez-vous vraiment supprimer ce versement de TVA ?")) {
        e.preventDefault();
        return;
      }
    });
  });
}
