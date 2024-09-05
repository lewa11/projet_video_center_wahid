
            // Fonction pour ouvrir le modal et réinitialiser les cases à cocher
            function openModal() {
            // Réinitialiser toutes les cases à cocher
                document
                .querySelectorAll('input[name="theme-checkbox"]')
                .forEach((checkbox) => {
                checkbox.checked = false; // Décoche toutes les cases
                });
  // Ouvrir le modal
  document.getElementById("my_modal_2").showModal();
}

document
  .querySelectorAll('input[name="theme-checkbox"]')
  .forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
      // Fermer le modal uniquement si une case est cochée
      if (this.checked) {
        // Décochez toutes les autres cases avant de cocher l'actuelle
        document
          .querySelectorAll('input[name="theme-checkbox"]')
          .forEach((box) => {
            if (box !== this) {
              box.checked = false;
            }
          });

        document.documentElement.setAttribute("data-theme", this.value);
        document.getElementById("my_modal_2").close(); // Ferme le modal
      }
    });
  });
  

        