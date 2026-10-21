<?php require 'includes/config.php'; require 'includes/layout.php'; $u = user(); page_start('Mon panier'); ?>
<section><div class="wrap"><h1>Mon panier</h1><div id="cart"></div>
<?php if ($u && $u['role'] === 'client'): ?>
<form id="chk" class="card form" style="max-width:520px;margin-top:20px"><h3>Livraison</h3>
<label>Adresse de livraison<textarea name="adresse" rows="2" required></textarea></label>
<label>Téléphone<input name="telephone" required pattern="[0-9+ ]{8,15}"></label>
<button class="btn">Valider la commande</button><div class="msg" id="msg"></div></form>
<?php else: ?><p class="card" style="margin-top:20px">Pour commander, <a href="<?= url('login.php?role=client&next=panier.php') ?>">connectez-vous avec un compte client</a> ou <a href="<?= url('register.php') ?>">créez un compte</a>.</p><?php endif; ?>
</div></section>
<script>
const CSRF = <?= json_encode(csrf()) ?>;
const esc = s => String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const fmt = n => n.toFixed(2).replace('.', ',') + ' DH';
function draw() {
  const c = Panier.get(), el = document.getElementById('cart'), f = document.getElementById('chk');
  if (!c.length) { el.innerHTML = '<p class="card">Votre panier est vide. <a href="<?= url('produits.php') ?>">Voir les produits</a></p>'; if (f) f.style.display = 'none'; return; }
  if (f) f.style.display = '';
  el.innerHTML = '<table><tr><th></th><th>Produit</th><th>Prix</th><th>Quantité</th><th>Total</th><th></th></tr>' + c.map((i, k) =>
    `<tr><td><img src="${esc(i.image)}" alt="" width="56"></td><td>${esc(i.nom)}</td><td>${fmt(i.prix)}</td>
    <td><button class="btn sm ghost" data-a="-" data-i="${k}">−</button> ${i.qte} <button class="btn sm ghost" data-a="+" data-i="${k}">+</button></td>
    <td>${fmt(i.prix * i.qte)}</td><td><button class="btn sm red" data-a="x" data-i="${k}">Retirer</button></td></tr>`).join('') +
    `<tr><th colspan="4">Total</th><th colspan="2">${fmt(c.reduce((s, i) => s + i.prix * i.qte, 0))}</th></tr></table>`;
}
document.getElementById('cart').addEventListener('click', e => {
  const b = e.target.closest('[data-a]'); if (!b) return; const c = Panier.get(), i = +b.dataset.i;
  if (b.dataset.a === '+') c[i].qte = Math.min(99, c[i].qte + 1);
  if (b.dataset.a === '-') c[i].qte = Math.max(1, c[i].qte - 1);
  if (b.dataset.a === 'x') c.splice(i, 1);
  Panier.set(c); draw();
});
const chk = document.getElementById('chk');
if (chk) chk.onsubmit = async e => {
  e.preventDefault();
  const r = await fetch('<?= url('commander.php') ?>', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF': CSRF },
    body: JSON.stringify({ items: Panier.get().map(i => ({ id: i.id, qte: i.qte })), adresse: chk.adresse.value, telephone: chk.telephone.value }) });
  const j = await r.json();
  if (!r.ok) { document.getElementById('msg').textContent = j.error; return; }
  Panier.set([]); location.href = '<?= url('client/index.php') ?>?ok=' + j.id;
};
draw();
</script>
<?php page_end(); ?>
