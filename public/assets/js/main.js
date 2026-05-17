import { initModal } from "./modules/modal.js";
import { initClients } from "./modules/clients.js";
import { initDevis } from "./modules/devis.js";

document.addEventListener("DOMContentLoaded", () => {
  initModal();
  initClients();
  initDevis();
});
