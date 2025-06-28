(function() {
  "use strict";

  // Helper pour sélectionner des éléments
  const select = (el, all = false) => el.trim() && (all ? [...document.querySelectorAll(el)] : document.querySelector(el));

  // Helper pour attacher des écouteurs d'événements
  const on = (type, el, listener, all = false) => {
    const selectEl = select(el, all);
    if (selectEl) {
      if (all) {
        selectEl.forEach(e => e.addEventListener(type, listener));
      } else {
        selectEl.addEventListener(type, listener);
      }
    }
  };

  /**
   * Header dynamique au défilement
   */
  const header = select('#header');
  if (header) {
    const handleScroll = () => window.scrollY > 100 ? header.classList.add('scrolled') : header.classList.remove('scrolled');
    window.addEventListener('load', handleScroll);
    document.addEventListener('scroll', handleScroll);
  }

  /**
   * Gestion du menu mobile (Hamburger)
   */
  on('click', '.mobile-nav-toggle', function(e) {
    select('body').classList.toggle('mobile-nav-active');
    select('.mobile-nav-show').classList.toggle('d-none');
    select('.mobile-nav-hide').classList.toggle('d-none');
  });

  /**
   * Initialisation des animations au défilement (AOS)
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