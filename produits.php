<?php require 'includes/config.php'; require 'includes/layout.php';
$marque = $_GET['marque'] ?? ''; $cat = $_GET['categorie'] ?? ''; $s = trim($_GET['q'] ?? '');
$w = []; $a = [];
if ($marque !== '') { $w[] = 'marque=?'; $a[] = $marque; }
if ($cat !== '') { $w[] = 'categorie=?'; $a[] = $cat; }
if ($s !== '') { $w[] = '(nom LIKE ? OR description LIKE ?)'; $a[] = "%$s%"; $a[] = "%$s%"; }
$rows = q('SELECT * FROM produits' . ($w ? ' WHERE ' . implode(' AND ', $w) : '') . ' ORDER BY (disponible=1 AND stock>0) DESC, nom', $a);
$cats = q('SELECT DISTINCT categorie FROM produits WHERE categorie<>\'\' ORDER BY categorie')->fetchAll(PDO::FETCH_COLUMN);
page_start('Nos produits', 'produits.php'); ?>
<section><div class="wrap"><h1>Nos produits</h1><p>Lait frais, lait fermenté, yaourts et beurre : du lait pour tous les âges et tous les goûts.</p>
<form class="filters" method="get"><input name="q" placeholder="Rechercher un produit" value="<?= e($s) ?>">
<select name="marque"><option value="">Toutes les marques</option><?php opts(MARQUES, $marque); ?></select>
<select name="categorie"><option value="">Toutes les catégories</option><?php opts($cats, $cat); ?></select><button class="btn">Filtrer</button></form>
<div class="grid"><?php foreach ($rows as $p) product_card($p); ?></div>
<?php if (!$rows): ?><p class="muted">Aucun produit ne correspond à votre recherche.</p><?php endif; ?></div></section>
<?php page_end(); ?>