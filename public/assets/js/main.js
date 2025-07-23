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
   * Header : Cacher en scrollant vers le bas, montrer en scrollant vers le haut
   */
  let lastScrollTop = 0;
  const header = select('#header');
  if (header) {
    window.addEventListener('scroll', function() {
      let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
      if (scrollTop > lastScrollTop && scrollTop > header.offsetHeight) {
        header.classList.add('header-hidden'); // Ajoute la classe pour cacher
      } else {
        header.classList.remove('header-hidden'); // Enlève la classe pour montrer
      }
      // Logique pour le changement de style au scroll (classe .scrolled)
      if (scrollTop > 100) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
      lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    }, false);
  }

  /**
   * Logique pour le menu mobile (Panneau glissant)
   */
  on('click', '.mobile-nav-toggle', function(e) {
    select('body').classList.toggle('mobile-nav-active');
  });

  /** AOS (Animations au scroll) */
  window.addEventListener('load', () => {
    AOS.init({
      duration: 1000,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  });

  // Logique pour la carte Leaflet (déplacée ici pour centralisation)
  const contactMapDiv = select('#contact-map');
  if (contactMapDiv) {
    // ... votre code de carte Leaflet ...
  }
  
})();