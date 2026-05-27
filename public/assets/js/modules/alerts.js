export function initAlerts() {
  document.querySelectorAll(".alert").forEach((alert) => {
    const id = alert.id;
    const closeBtn = alert.querySelector(".alert__close");

    if (id) {
      const dismissedAt = localStorage.getItem(`alert_dismissed_${id}`);
      const oneWeek = 7 * 24 * 60 * 60 * 1000;

      if (dismissedAt && Date.now() - parseInt(dismissedAt) < oneWeek) {
        alert.remove();
        return;
      }
    }

    if (closeBtn) {
      closeBtn.addEventListener("click", () => {
        alert.remove();
        if (id)
          localStorage.setItem(`alert_dismissed_${id}`, Date.now().toString());
      });
    }
  });
}
