export function initMenu() {
  const headerToggle = document.querySelector(".header__toggle");
  const headerNav = document.querySelector(".header__nav");

  if (headerToggle && headerNav) {
    headerToggle.addEventListener("click", () => {
      const isOpen = headerNav.classList.toggle("is-open");
      const icon = headerToggle.querySelector("i");

      if (icon) {
        icon.className = isOpen ? "ri-close-line" : "ri-menu-3-line";
      }
    });
  }

  const sidebarToggle = document.querySelector(".topbar__toggle");
  const sidebar = document.querySelector(".sidebar");
  const overlay = document.querySelector(".sidebar-overlay");

  if (sidebarToggle && sidebar) {
    const toggleSidebar = () => {
      sidebar.classList.toggle("is-open");
      if (overlay) {
        overlay.classList.toggle("is-open");
      }
    };

    sidebarToggle.addEventListener("click", toggleSidebar);

    if (overlay) {
      overlay.addEventListener("click", toggleSidebar);
    }
  }
}
