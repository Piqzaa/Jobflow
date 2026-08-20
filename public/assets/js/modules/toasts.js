const TYPES = {
  success: { icon: "ri-checkbox-circle-fill", defaultText: "Opération réussie." },
  error: { icon: "ri-error-warning-fill", defaultText: "Une erreur est survenue." },
  warning: { icon: "ri-alert-fill", defaultText: "Attention." },
  info: { icon: "ri-information-fill", defaultText: "Information." },
};

const DEFAULT_DURATION = 3500;

let container = null;

function getContainer() {
  if (!container) {
    container = document.createElement("div");
    container.className = "toast-container";
    container.setAttribute("aria-live", "polite");
    document.body.appendChild(container);
  }
  return container;
}

export function showToast(message, type = "success", duration = DEFAULT_DURATION) {
  const config = TYPES[type] || TYPES.info;
  const text = message || config.defaultText;

  const toast = document.createElement("div");
  toast.className = `toast toast--${type}`;
  toast.setAttribute("role", type === "error" ? "alert" : "status");

  const icon = document.createElement("i");
  icon.className = `toast__icon ${config.icon}`;
  icon.setAttribute("aria-hidden", "true");

  const span = document.createElement("span");
  span.className = "toast__text";
  span.textContent = text;

  const close = document.createElement("button");
  close.className = "toast__close";
  close.setAttribute("aria-label", "Fermer");
  const closeIcon = document.createElement("i");
  closeIcon.className = "ri-close-line";
  closeIcon.setAttribute("aria-hidden", "true");
  close.appendChild(closeIcon);

  toast.appendChild(icon);
  toast.appendChild(span);
  toast.appendChild(close);

  const dismiss = () => {
    clearTimeout(timer);
    if (!toast.isConnected) return;
    toast.classList.add("is-leaving");
    setTimeout(() => toast.remove(), 250);
  };

  close.addEventListener("click", dismiss);

  getContainer().appendChild(toast);
  requestAnimationFrame(() => toast.classList.add("is-visible"));

  let timer = null;
  if (duration > 0) {
    timer = setTimeout(dismiss, duration);
  }

  return toast;
}
