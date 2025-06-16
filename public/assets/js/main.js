(function() {
  "use strict";

  /**
   * Fonction pour sélectionner facilement des éléments
   */
  const select = (el, all = false) => {
    el = el.trim();
    if (all) {
      return [...document.querySelectorAll(el)];
    } else {
      return document.querySelector(el);
    }
  };

  /**
   * Fonction pour attacher des écouteurs d'événements
   */
  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all);
    if (selectEl) {
      if (all) {
        selectEl.forEach(e => e.addEventListener(type, listener));
      } else {
        selectEl.addEventListener(type, listener);
      }
    }
  };

  /**
   * Header dynamique : transparent sur le Hero, solide au défilement.
   * On utilise Intersection Observer pour la performance.
   */
  const header = select('#header');
  const heroSection = select('#hero');

  if (header && heroSection) {
    const headerObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) { // Quand le Hero n'est plus visible à l'écran
            header.classList.add('scrolled');
          } else { // Quand le Hero est visible
            header.classList.remove('scrolled');
          }
        });
      }, { rootMargin: "-90px 0px 0px 0px" } // Se déclenche 90px avant que le Hero ne sorte de l'écran
    );
    headerObserver.observe(heroSection);
  }

  /**
   * Gestion du menu de navigation mobile
   */
  on('click', '.mobile-nav-toggle', function(e) {
    select('body').classList.toggle('mobile-nav-active');
    this.classList.toggle('bi-list');
    this.classList.toggle('bi-x');
  });

  /**
   * Animation au défilement (AOS)
   */
  window.addEventListener('load', () => {
    AOS.init({
      duration: 1000,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  });

})();