export function initModal() {
  const openButtons = document.querySelectorAll("[data-modal-target]");
  const closeButtons = document.querySelectorAll("[data-modal-close]");
  const overlays = document.querySelectorAll(".modal__overlay");

  openButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const modal = document.querySelector(button.dataset.modalTarget);
      if (modal) modal.classList.add("is-active");
    });
  });

  closeButtons.forEach((button) => {
    button.addEventListener("click", () => {
      const modal = button.closest(".modal");
      if (modal) modal.classList.remove("is-active");
    });
  });

  overlays.forEach((overlay) => {
    overlay.addEventListener("click", () => {
      const modal = overlay.closest(".modal");
      if (modal) modal.classList.remove("is-active");
    });
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      const modal = document.querySelector(".modal.is-active");
      if (modal) modal.classList.remove("is-active");
    }
  });
}
