/* Comportements d'interface communs à toutes les pages */
(() => {
  const $$ = (s, r = document) => [...r.querySelectorAll(s)];

  // En-tête : ombre au défilement
  const top = document.querySelector('header.top');
  const onScroll = () => top && top.classList.toggle('scrolled', scrollY > 8);
  addEventListener('scroll', onScroll, { passive: true }); onScroll();

  // Menu mobile
  const burger = document.querySelector('.burger'), nav = document.getElementById('mainnav');
  if (burger && nav) burger.addEventListener('click', () => {
    const open = nav.classList.toggle('open'); burger.setAttribute('aria-expanded', open);
  });

  // Image de secours si une photo est introuvable
  document.addEventListener('error', e => {
    const i = e.target;
    if (i.tagName === 'IMG' && window.DEFAULT_IMG && !i.dataset.fb) { i.dataset.fb = 1; i.src = window.DEFAULT_IMG; }
  }, true);

  // Apparition douce des cartes au défilement
  if ('IntersectionObserver' in window && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
    const io = new IntersectionObserver(es => es.forEach(en => {
      if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
    }), { threshold: .12 });
    $$('.card, .tl > div, .kpis > div').forEach((el, i) => {
      el.classList.add('reveal'); el.style.setProperty('--d', (i % 4) * 60 + 'ms'); io.observe(el);
    });
  }

  // Catalogue : filtres automatiques (menus) et recherche avec délai
  const f = document.querySelector('form.filters');
  if (f) {
    $$('select', f).forEach(s => s.addEventListener('change', () => f.submit()));
    const q = f.querySelector('input[name=q]'); let t;
    if (q) q.addEventListener('input', () => { clearTimeout(t); t = setTimeout(() => f.submit(), 600); });
  }

  // Aperçu de la photo choisie avant l'envoi
  $$('input[type=file][accept^="image"]').forEach(inp => inp.addEventListener('change', () => {
    let p = inp.parentElement.querySelector('.preview');
    if (!p) { p = document.createElement('img'); p.className = 'preview'; p.alt = 'Aperçu'; inp.parentElement.appendChild(p); }
    const file = inp.files[0];
    if (!file) { p.remove(); return; }
    if (file.size > 2 * 1024 * 1024) { toast('Photo trop lourde (2 Mo max).', true); inp.value = ''; p.remove(); return; }
    p.src = URL.createObjectURL(file);
  }));

  // Messages flash : disparition automatique
  $$('.flash').forEach(m => setTimeout(() => { m.style.transition = 'opacity .5s'; m.style.opacity = 0; setTimeout(() => m.remove(), 500); }, 5000));

  // Évite le double envoi des formulaires
  $$('form[method=post]').forEach(fm => fm.addEventListener('submit', e => {
    if (e.defaultPrevented) return;
    const b = fm.querySelector('button:not([type=button])'); if (b) setTimeout(() => b.disabled = true, 0);
  }));
})();
