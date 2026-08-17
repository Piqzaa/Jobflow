let overlay = null;

function getOverlay() {
  if (!overlay) {
    overlay = document.createElement("div");
    overlay.className = "loading-overlay";

    const spinner = document.createElement("div");
    spinner.className = "loading-overlay__spinner";
    spinner.setAttribute("aria-hidden", "true");

    const text = document.createElement("p");
    text.className = "loading-overlay__text";
    text.textContent = "";

    const box = document.createElement("div");
    box.className = "loading-overlay__box";
    box.appendChild(spinner);
    box.appendChild(text);

    overlay.appendChild(box);
    document.body.appendChild(overlay);
  }
  return overlay;
}

export function showLoading(message = "Veuillez patienter…") {
  const el = getOverlay();
  el.querySelector(".loading-overlay__text").textContent = message;
  el.classList.add("is-active");
  document.body.style.overflow = "hidden";
}

export function hideLoading() {
  if (!overlay) return;
  overlay.classList.remove("is-active");
  document.body.style.overflow = "";
}
