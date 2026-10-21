<?php require 'includes/config.php'; require 'includes/layout.php';
$vedettes = q('SELECT * FROM produits WHERE disponible=1 AND stock>0 ORDER BY id LIMIT 4')->fetchAll();
page_start('Accueil', 'index.php'); ?>
<div class="hero"><div class="wrap hero-in"><div><h1>Une coopérative qui s'engage pour l'avenir.</h1>
<p>Du pré à votre table : la coopérative laitière qui vous régale, depuis plus de 70 ans.</p>
<a class="btn sun" href="<?= url('produits.php') ?>">Voir nos produits</a> <a class="btn ghost" style="color:#fff;box-shadow:inset 0 0 0 2px #fff" href="<?= url('histoire.php') ?>">Notre histoire</a></div>
<div class="hero-pics" aria-hidden="true"><img src="<?= url('img/produits/raibi-grenadine.jpg') ?>" alt=""><img src="<?= url('img/produits/lait.jpg') ?>" alt=""><img src="<?= url('img/produits/nature.jpg') ?>" alt=""></div></div></div>

<section><div class="wrap"><h2>Qui sommes-nous</h2>
<p style="max-width:780px">La Coopérative Laitière du Maroc Oriental est le plus grand producteur de lait et de produits dérivés de la région. Basée à Oujda, elle produit du lait, du lait fermenté, des yaourts et du beurre, et exerce à titre secondaire une activité d'aliment de bétail. Grâce à une flotte de camions frigorifiques, COLAIMO respecte la chaîne du froid et couvre l'Oriental, la région de Fès-Meknès et les provinces d'Al Hoceïma et d'Errachidia.</p></div></section>

<section class="alt"><div class="wrap"><div class="row between"><h2>Nos produits à la une</h2><a class="btn" href="<?= url('produits.php') ?>">Tout le catalogue</a></div>
<div class="grid"><?php foreach ($vedettes as $p) product_card($p); ?></div></div></section>

<section><div class="wrap"><h2>COLAIMO en chiffres</h2><div class="grid">
<div class="card"><div class="stat">+5 000</div>coopérateurs (éleveurs adhérents)</div>
<div class="card"><div class="stat">73</div>centres de collecte</div>
<div class="card"><div class="stat">~500</div>collaborateurs</div>
<div class="card"><div class="stat">90 M DH</div>de capital social (2017)</div></div></div></section>

<section class="alt spaces"><div class="wrap"><h2>Nos espaces en ligne</h2><div class="grid">
<div class="card"><h3>Espace Client</h3><p>Achetez nos produits, suivez vos commandes et envoyez vos réclamations.</p><a class="btn" href="<?= url('login.php?role=client') ?>">Entrer</a></div>
<div class="card"><h3>Espace Employé</h3><p>Gérez la disponibilité des produits, consultez votre machine et déclarez une alerte.</p><a class="btn" href="<?= url('login.php?role=employe') ?>">Entrer</a></div>
<div class="card"><h3>Espace Admin</h3><p>Pilotez les produits, commandes, utilisateurs et le parc informatique.</p><a class="btn" href="<?= url('login.php?role=admin') ?>">Entrer</a></div></div></div></section>
<?php page_end(); ?>
