<?php require '../includes/config.php'; require '../includes/layout.php'; require_role('client'); $u = user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') { check_csrf(); if (post('sujet') !== '' && post('message') !== '') { q("INSERT INTO reclamations(user_id,sujet,message,statut) VALUES(?,?,?,'Ouverte')", [$u['id'], post('sujet'), post('message')]); flash('Votre demande a été envoyée.'); } redirect(url('client/reclamations.php')); }
$rows = q('SELECT * FROM reclamations WHERE user_id=? ORDER BY id DESC', [$u['id']])->fetchAll();
espace_start('client', 'Réclamations et suggestions'); ?>
<form method="post" class="card form" style="margin-bottom:24px"><?= csrf_field() ?><h3>Nouvelle demande</h3><label>Sujet<input name="sujet" required></label><label>Message<textarea name="message" rows="3" required></textarea></label><button class="btn">Envoyer</button></form>
<?php foreach ($rows as $r): ?><div class="card" style="margin-bottom:14px"><div class="row between"><b><?= e($r['sujet']) ?></b><?= pill($r['statut']) ?></div><p class="muted"><?= e($r['date']) ?></p><p><?= nl2br(e($r['message'])) ?></p>
<?php if ($r['reponse']): ?><p><b>Réponse de COLAIMO :</b> <?= nl2br(e($r['reponse'])) ?></p><?php endif; ?></div><?php endforeach; ?>
<?php espace_end(); ?>
