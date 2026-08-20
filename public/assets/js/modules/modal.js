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

function buildConfirmModal({ title, message, confirmText, cancelText, danger }) {
  const existing = document.querySelector(".modal--confirm");
  if (existing) existing.remove();

  const modal = document.createElement("div");
  modal.className = "modal modal--confirm" + (danger ? " is-danger" : "");

  const titleValue = title || "Confirmer";
  const messageValue = message || "";
  const confirmTextValue = confirmText || "Confirmer";
  const cancelTextValue = cancelText || "Annuler";

  const overlay = document.createElement("div");
  overlay.className = "modal__overlay";

  const dialog = document.createElement("div");
  dialog.className = "modal__dialog";
  dialog.setAttribute("role", "dialog");
  dialog.setAttribute("aria-modal", "true");
  dialog.setAttribute("aria-labelledby", "confirm-title");
  dialog.setAttribute("aria-describedby", "confirm-message");

  const closeBtn = document.createElement("button");
  closeBtn.className = "modal__close";
  closeBtn.type = "button";
  closeBtn.setAttribute("aria-label", "Fermer");
  const closeIcon = document.createElement("i");
  closeIcon.className = "ri-close-line";
  closeIcon.setAttribute("aria-hidden", "true");
  closeBtn.appendChild(closeIcon);

  const iconWrap = document.createElement("div");
  iconWrap.className = "modal__confirm-icon";
  const icon = document.createElement("i");
  icon.className = danger ? "ri-delete-bin-6-line" : "ri-question-line";
  icon.setAttribute("aria-hidden", "true");
  iconWrap.appendChild(icon);

  const titleEl = document.createElement("h2");
  titleEl.className = "modal__title";
  titleEl.id = "confirm-title";
  titleEl.textContent = titleValue;

  const messageEl = document.createElement("p");
  messageEl.className = "modal__confirm-message";
  messageEl.id = "confirm-message";
  messageEl.textContent = messageValue;

  const actions = document.createElement("div");
  actions.className = "modal__actions";

  const cancelBtn = document.createElement("button");
  cancelBtn.className = "btn--light";
  cancelBtn.type = "button";
  cancelBtn.dataset.confirmCancel = "";
  cancelBtn.textContent = cancelTextValue;

  const okBtn = document.createElement("button");
  okBtn.className = danger ? "btn--danger" : "btn--primary";
  okBtn.type = "button";
  okBtn.dataset.confirmOk = "";
  okBtn.textContent = confirmTextValue;

  actions.appendChild(cancelBtn);
  actions.appendChild(okBtn);

  dialog.appendChild(closeBtn);
  dialog.appendChild(iconWrap);
  dialog.appendChild(titleEl);
  dialog.appendChild(messageEl);
  dialog.appendChild(actions);

  modal.appendChild(overlay);
  modal.appendChild(dialog);

  document.body.appendChild(modal);
  return modal;
}

export function confirmAction(message, options = {}) {
  return new Promise((resolve) => {
    const modal = buildConfirmModal({ message, ...options });
    const previouslyFocused = document.activeElement;

    let settled = false;
    const done = (value) => {
      if (settled) return;
      settled = true;
      document.removeEventListener("keydown", keydown);
      document.removeEventListener("keydown", trapFocus);
      closeModal(modal);
      modal.remove();
      if (previouslyFocused && typeof previouslyFocused.focus === "function") {
        previouslyFocused.focus();
      }
      resolve(value);
    };

    const okBtn = modal.querySelector("[data-confirm-ok]");
    const cancelBtn = modal.querySelector("[data-confirm-cancel]");
    const focusables = [cancelBtn, okBtn];

    okBtn.addEventListener("click", () => done(true));
    cancelBtn.addEventListener("click", () => done(false));
    modal.querySelector(".modal__overlay").addEventListener("click", () => done(false));
    modal.querySelector(".modal__close").addEventListener("click", () => done(false));

    const keydown = (e) => {
      if (e.key === "Escape") done(false);
    };

    const trapFocus = (e) => {
      if (e.key !== "Tab") return;
      const first = focusables[0];
      const last = focusables[focusables.length - 1];
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    };

    document.addEventListener("keydown", keydown);
    document.addEventListener("keydown", trapFocus);

    openModal(modal);
    okBtn.focus();
  });
}

export function initModal() {
  document.addEventListener("click", (e) => {
    const openBtn = e.target.closest("[data-modal-target]");
    if (openBtn) {
      const modal = document.querySelector(openBtn.dataset.modalTarget);
      openModal(modal);
    }

    const confirmEl = e.target.closest(".modal--confirm");
    if (confirmEl) return;

    const closeBtn = e.target.closest("[data-modal-close]");
    if (closeBtn || e.target.classList.contains("modal__overlay")) {
      const modal = e.target.closest(".modal");
      closeModal(modal);
    }
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      const modal = document.querySelector(".modal.is-active");
      if (!modal || modal.classList.contains("modal--confirm")) return;
      closeModal(modal);
    }
  });
}
