<?php
// ===== Gabarits HTML communs (en-tête, pied de page, espaces) =====
function page_start($titre, $active = '') { $u = user(); $f = flash(); ?>
<!DOCTYPE html>
<html lang="fr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($titre) ?> – COLAIMO</title><link rel="icon" href="<?= url('img/logo.jpg') ?>">
<link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600&family=Nunito:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= url('css/style.css') ?>"><script>window.DEFAULT_IMG = <?= json_encode(url('img/produits/default.svg')) ?>;</script>
<script src="<?= url('js/panier.js') ?>"></script><script src="<?= url('js/site.js') ?>" defer></script></head><body>
<header class="top"><div class="wrap"><a href="<?= url('index.php') ?>"><img src="<?= url('img/logo.jpg') ?>" alt="COLAIMO"></a>
<button class="burger" type="button" aria-label="Menu" aria-expanded="false">☰</button>
<nav id="mainnav">
<?php foreach (['index.php' => 'Accueil', 'histoire.php' => 'Histoire', 'produits.php' => 'Produits'] as $h => $l): ?>
<a href="<?= url($h) ?>" class="<?= $active === $h ? 'cur' : '' ?>"><?= $l ?></a>
<?php endforeach; ?>
<a href="<?= url('panier.php') ?>">Panier <span class="cartbadge" id="cartcount">0</span></a>
<?php if ($u): ?><a href="<?= url($u['role'] . '/index.php') ?>">Mon espace</a><a href="<?= url('logout.php') ?>">Déconnexion</a>
<?php else: ?><a class="btn sm" href="<?= url('login.php') ?>">Connexion</a><?php endif; ?>
</nav></div></header>
<?php if ($f): ?><div class="wrap"><p class="flash <?= e($f[1]) ?>"><?= e($f[0]) ?></p></div><?php endif; ?>
<?php }

function page_end() { ?>
<footer><div class="wrap"><div class="grid"><div><b>COLAIMO</b><br>Coopérative Laitière du Maroc Oriental<br>Route El Aounia, Hay Takadoum – BP 3133 – 60000 Oujda</div>
<div>Tél. : +212 (0)5 36 74 03 66<br>Fax : +212 (0)5 36 74 12 87<br><a href="mailto:info@colaimo.com">info@colaimo.com</a></div>
<div><a href="<?= url('produits.php') ?>">Produits</a><br><a href="<?= url('histoire.php') ?>">Histoire</a><br><a href="<?= url('login.php') ?>">Espaces en ligne</a></div></div>
<p>© COLAIMO – Tous droits réservés.</p></div></footer><div id="toast" role="status" aria-live="polite"></div></body></html>
<?php }

function espace_start($role, $titre) {
  require_role($role); $cur = basename($_SERVER['SCRIPT_NAME']); page_start($titre); ?>
<div class="wrap layout"><aside class="side"><p class="muted"><?= e(user()['nom']) ?><br><?= e(ucfirst($role)) ?></p>
<?php foreach (MENUS[$role] as $f => $l): ?><a href="<?= url("$role/$f") ?>" class="<?= $cur === $f ? 'on' : '' ?>"><?= e($l) ?></a><?php endforeach; ?>
</aside><main class="panel"><h1><?= e($titre) ?></h1>
<?php }

function espace_end() { ?></main></div><?php page_end(); }

function product_card($p) { $dispo = $p['disponible'] && $p['stock'] > 0; ?>
<div class="card prod"><a href="<?= url('produit.php?id=' . (int)$p['id']) ?>"><span class="pwrap"><img class="pimg" loading="lazy" src="<?= e(img($p['image'])) ?>" alt="<?= e($p['nom']) ?>"></span></a>
<small><?= e($p['marque']) ?> · <?= e($p['categorie']) ?></small><h3><?= e($p['nom']) ?></h3><p class="muted"><?= e($p['description']) ?></p>
<div class="row between"><b class="price"><?= money($p['prix']) ?></b>
<?php if ($dispo): ?><button type="button" class="btn sm add" data-id="<?= (int)$p['id'] ?>" data-nom="<?= e($p['nom']) ?>" data-prix="<?= e($p['prix']) ?>" data-image="<?= e(img($p['image'])) ?>">Ajouter au panier</button>
<?php else: ?><?= pill('Indisponible') ?><?php endif; ?></div></div>
<?php }
