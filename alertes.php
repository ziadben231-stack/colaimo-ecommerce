<?php require '../includes/config.php'; require '../includes/layout.php'; require_role('admin');
if ($_SERVER['REQUEST_METHOD'] === 'POST') { check_csrf(); if (in_array(post('statut'), ['Ouverte', 'En cours', 'Résolue'])) { q('UPDATE alertes SET statut=? WHERE id=?', [post('statut'), (int)post('id')]); flash('Alerte mise à jour.'); } redirect(url('admin/alertes.php')); }
$rows = q('SELECT a.*,u.nom employe,m.type,m.marque,m.modele FROM alertes a JOIN users u ON u.id=a.user_id LEFT JOIN machines m ON m.id=a.machine_id ORDER BY a.id DESC')->fetchAll();
espace_start('admin', 'Alertes du parc'); ?>
<table><tr><th>Date</th><th>Machine</th><th>Alerte</th><th>Priorité</th><th>Par</th><th>Statut</th></tr>
<?php foreach ($rows as $a): ?><tr><td><?= e($a['date']) ?></td><td><?= e(trim($a['type'] . ' ' . $a['marque'] . ' ' . $a['modele'])) ?></td><td><b><?= e($a['titre']) ?></b><br><?= e($a['description']) ?></td><td><?= pill($a['priorite']) ?></td><td><?= e($a['employe']) ?></td>
<td><form method="post" class="row"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$a['id'] ?>"><select name="statut"><?php opts(['Ouverte', 'En cours', 'Résolue'], $a['statut']); ?></select><button class="btn sm">OK</button></form></td></tr><?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="6" class="muted">Aucune alerte.</td></tr><?php endif; ?></table>
<?php espace_end(); ?>
