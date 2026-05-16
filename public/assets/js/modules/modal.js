export function openModal(modal) {
  if (modal) {
    modal.classList.add("is-active");
    document.body.style.overflow = "hidden";
  }
}

export function closeModal(modal) {
  if (modal) {
    modal.classList.remove("is-active");
    document.body.style.overflow = "";
  }
}

export function initModal() {
  document.addEventListener("click", (e) => {
    const openBtn = e.target.closest("[data-modal-target]");
    if (openBtn) {
      const modal = document.querySelector(openBtn.dataset.modalTarget);
      openModal(modal);
    }

    const closeBtn = e.target.closest("[data-modal-close]");
    if (closeBtn || e.target.classList.contains("modal__overlay")) {
      const modal = e.target.closest(".modal");
      closeModal(modal);
    }
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      const modal = document.querySelector(".modal.is-active");
      closeModal(modal);
    }
  });
}
