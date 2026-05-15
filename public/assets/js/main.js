import { initModal } from "./modules/modal.js";
import { initClients } from "./modules/clients.js";

document.addEventListener("DOMContentLoaded", () => {
  initModal();
  initClients();
});
