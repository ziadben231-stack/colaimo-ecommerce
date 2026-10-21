<?php require 'includes/config.php'; require 'includes/layout.php'; page_start('Histoire', 'histoire.php'); ?>
<section><div class="wrap"><h1>Historique</h1><div class="tl">
<div><b>1953</b><br>Création de la Société Coopérative Laitière du Maroc Oriental (SOCOLMO) par un groupe d'éleveurs de la banlieue d'Oujda. L'activité démarre en 1956.</div>
<div><b>1994</b><br>SOCOLMO devient la Coopérative Laitière du Maroc Oriental, sous le sigle COLAIMO.</div>
<div><b>COLAIMO II</b><br>Inauguration de l'usine COLAIMO II, un investissement de 50 000 000 DH.</div>
<div><b>22 août 2007</b><br>Certification HACCP selon les exigences du Codex Alimentarius.</div>
<div><b>25 novembre 2008</b><br>Certification ISO 22000 version 2005 (sécurité des denrées alimentaires).</div>
<div><b>Visite royale</b><br>COLAIMO reçoit la visite de Sa Majesté le Roi Mohammed VI.</div></div></div></section>

<section class="alt"><div class="wrap"><h2>Valeurs et engagements</h2><div class="grid">
<div class="card"><h3>Santé</h3>Du lait frais, un yaourt riche, du beurre naturel et des yaourts à boire.</div>
<div class="card"><h3>Nature</h3>Collecte, transformation et mise en vente : le lait est contrôlé de son entrée à sa sortie.</div>
<div class="card"><h3>Solidarité</h3>Partenaire social de l'éducation, COLAIMO accueille des stagiaires et soutient les activités sociales de la région.</div>
<div class="card"><h3>Qualité</h3>Certifiée HACCP et ISO 22000, avec un laboratoire qui contrôle chaque étape.</div></div>
<h3 style="margin-top:36px">Nos marques</h3><div class="tags"><?php foreach (MARQUES as $m): ?><a href="<?= url('produits.php?marque=' . urlencode($m)) ?>"><span><?= e($m) ?></span></a><?php endforeach; ?></div>
<h3 style="margin-top:30px">Ils nous font confiance</h3><div class="tags"><span>Tetra Pak</span><span>Crédit Agricole</span><span>Constantia</span><span>Carrefour</span><span>Marjane</span><span>Aswak Assalam</span><span>Amcor</span></div></div></section>

<section><div class="wrap"><h2>Organigramme</h2><img src="<?= url('img/organigramme.jpg') ?>" alt="Organigramme de COLAIMO" style="border:1px solid var(--line);border-radius:12px"></div></section>
<?php page_end(); ?>
