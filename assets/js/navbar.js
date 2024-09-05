
            document.addEventListener("DOMContentLoaded", function () {
    const burgers = document.querySelectorAll(".navbar-burger");
    const menus = document.querySelectorAll(".navbar-menu");
    const closes = document.querySelectorAll(".navbar-close, .navbar-backdrop");

    // Fonction pour ouvrir/fermer le menu
    function toggleMenu() {
        menus.forEach(menu => {
            menu.classList.toggle("hidden");
        });
    }

    // Fonction pour fermer le menu explicitement
    function closeMenu() {
        menus.forEach(menu => {
            menu.classList.add("hidden");
        });
    }

    // Fonction pour ouvrir le menu explicitement
    function openMenu() {
        menus.forEach(menu => {
            menu.classList.remove("hidden");
        });
    }

    // Attacher l'événement de clic pour ouvrir/fermer le burger
    burgers.forEach(burger => {
        burger.addEventListener("click", function() {
            if (menus[0].classList.contains("hidden")) {
                openMenu();
            } else {
                closeMenu();
            }
        });
    });

    // Attacher l'événement de clic pour fermer le menu via la croix ou le backdrop
    closes.forEach(close => {
        close.addEventListener("click", closeMenu);
    });

    // Fermeture du menu lorsque l'utilisateur clique sur un lien
    const links = document.querySelectorAll(".navbar-menu a");
    links.forEach(link => {
        link.addEventListener("click", function(event) {
            event.preventDefault(); // Empêche le rechargement de la page

            // Fermer le menu
            closeMenu();

            // Naviguer vers la nouvelle page manuellement
            const targetPage = this.getAttribute("href");
            if (targetPage && targetPage !== "#") {
                window.location.href = targetPage;
            }
        });
    });

    // Fermeture du menu si on clique en dehors du menu dropdown
    document.addEventListener("click", function(event) {
        const isClickInsideMenu = event.target.closest(".navbar-menu");
        const isClickInsideBurger = event.target.closest(".navbar-burger");

        if (!isClickInsideMenu && !isClickInsideBurger) {
            closeMenu();
        }
    });

    // Réinitialiser le menu après un changement de page
    window.addEventListener("pageshow", function () {
        closeMenu();
    });
});
       