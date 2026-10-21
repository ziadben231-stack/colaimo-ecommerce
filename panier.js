/* Panier stocké dans le localStorage du navigateur ; la commande est validée côté serveur (commander.php) */
const Panier = {
  k: 'colaimo_panier',
  get() {
    try {
      const v = JSON.parse(localStorage.getItem(this.k));
      return Array.isArray(v) ? v.filter(i => i && i.id && i.prix >= 0 && i.qte > 0) : [];
    } catch (e) { return []; }
  },
  set(v) { try { localStorage.setItem(this.k, JSON.stringify(v)); } catch (e) {} this.badge(); },
  add(p) {
    const c = this.get(), x = c.find(i => i.id == p.id);
    x ? x.qte = Math.min(99, x.qte + 1) : c.push({ ...p, qte: 1 });
    this.set(c); this.badge(true);
  },
  count() { return this.get().reduce((s, i) => s + i.qte, 0); },
  total() { return this.get().reduce((s, i) => s + i.prix * i.qte, 0); },
  badge(bump) {
    const n = this.count(), b = document.getElementById('cartcount'); if (!b) return;
    b.textContent = n; b.style.display = n ? 'inline-block' : 'none';
    if (bump) { b.classList.remove('bump'); void b.offsetWidth; b.classList.add('bump'); }
  }
};
function toast(msg, bad) {
  const t = document.getElementById('toast'); if (!t) return;
  t.textContent = msg; t.className = 'show' + (bad ? ' bad' : '');
  clearTimeout(toast.t); toast.t = setTimeout(() => t.className = '', 2200);
}
document.addEventListener('DOMContentLoaded', () => Panier.badge());
window.addEventListener('storage', e => { if (e.key === Panier.k) Panier.badge(); });   // synchronise les onglets
document.addEventListener('click', e => {
  const b = e.target.closest('.add'); if (!b || b.disabled) return;
  Panier.add({ id: +b.dataset.id, nom: b.dataset.nom, prix: +b.dataset.prix, image: b.dataset.image });
  toast('« ' + b.dataset.nom + ' » ajouté au panier ✓');
});
