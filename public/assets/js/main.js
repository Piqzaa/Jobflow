import { initModal } from "./modules/modal.js";
import { initClients } from "./modules/clients.js";
import { initDevis } from "./modules/devis.js";
import { initFactures } from "./modules/factures.js";
import { initDashboard } from "./modules/dashboard.js";
import { initMenu } from "./modules/menu.js";

document.addEventListener("DOMContentLoaded", () => {
  initMenu();
  initModal();
  initClients();
  initDevis();
  initFactures();
  initDashboard();
});
