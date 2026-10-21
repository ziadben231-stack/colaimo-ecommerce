<?php require 'includes/config.php'; require 'includes/layout.php';
$p = q('SELECT * FROM produits WHERE id=?', [(int)($_GET['id'] ?? 0)])->fetch();
if (!$p) { http_response_code(404); page_start('Introuvable'); echo '<section><div class="wrap"><h1>Produit introuvable</h1><a class="btn" href="' . url('produits.php') . '">Retour au catalogue</a></div></section>'; page_end(); exit; }
$dispo = $p['disponible'] && $p['stock'] > 0;
$autres = q('SELECT * FROM produits WHERE id<>? AND (marque=? OR categorie=?) LIMIT 3', [$p['id'], $p['marque'], $p['categorie']])->fetchAll();
page_start($p['nom'], 'produits.php'); ?>
<section><div class="wrap"><p><a href="<?= url('produits.php') ?>">← Retour aux produits</a></p>
<div class="detail"><span class="pwrap big"><img class="pimg" src="<?= e(img($p['image'])) ?>" alt="<?= e($p['nom']) ?>"></span>
<div><small class="muted"><?= e($p['marque']) ?> · <?= e($p['categorie']) ?></small><h1><?= e($p['nom']) ?></h1><p><?= nl2br(e($p['description'])) ?></p>
<p class="stat"><?= money($p['prix']) ?></p><p><?= $dispo ? pill('Disponible') . ' <span class="muted">' . (int)$p['stock'] . ' en stock</span>' : pill('Indisponible') ?></p>
<?php if ($dispo): ?><button type="button" class="btn add" data-id="<?= (int)$p['id'] ?>" data-nom="<?= e($p['nom']) ?>" data-prix="<?= e($p['prix']) ?>" data-image="<?= e(img($p['image'])) ?>">Ajouter au panier</button><?php endif; ?></div></div>
<?php if ($autres): ?><h2 style="margin-top:40px">Vous aimerez aussi</h2><div class="grid"><?php foreach ($autres as $o) product_card($o); ?></div><?php endif; ?></div></section>
<?php page_end(); ?>
