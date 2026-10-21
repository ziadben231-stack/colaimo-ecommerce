<?php require '../includes/config.php'; require '../includes/layout.php'; require_role('employe'); $u = user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf(); $a = post('action');
  if ($a === 'machine') { q('UPDATE machines SET etat=?,notes=? WHERE id=? AND user_id=?', [in_array(post('etat'), ETATS) ? post('etat') : 'En service', post('notes'), (int)post('id'), $u['id']]); flash('Machine mise à jour.'); }
  if ($a === 'alerte' && post('titre') !== '' && q('SELECT 1 FROM machines WHERE id=? AND user_id=?', [(int)post('machine_id'), $u['id']])->fetch()) {
    q('INSERT INTO alertes(machine_id,user_id,titre,description,priorite,statut) VALUES(?,?,?,?,?,?)', [(int)post('machine_id'), $u['id'], post('titre'), post('description'), in_array(post('priorite'), ['Basse', 'Moyenne', 'Haute']) ? post('priorite') : 'Moyenne', 'Ouverte']); flash('Alerte envoyée au technicien.');
  }
  redirect(url('employe/parc.php'));
}
$ms = q('SELECT * FROM machines WHERE user_id=?', [$u['id']])->fetchAll();
$als = q('SELECT a.*,m.type,m.marque FROM alertes a LEFT JOIN machines m ON m.id=a.machine_id WHERE a.user_id=? ORDER BY a.id DESC', [$u['id']])->fetchAll();
espace_start('employe', 'Ma machine et alertes'); ?>
<?php if (!$ms): ?><p class="muted">Aucune machine ne vous est affectée : contactez le technicien.</p><?php endif; ?>
<?php foreach ($ms as $m): ?><form method="post" class="card form" style="margin-bottom:14px"><?= csrf_field() ?><input type="hidden" name="action" value="machine"><input type="hidden" name="id" value="<?= (int)$m['id'] ?>">
<b><?= e($m['type'] . ' ' . $m['marque'] . ' ' . $m['modele']) ?></b><span class="muted"><?= e($m['departement']) ?> · <?= e($m['local']) ?> · série <?= e($m['serie']) ?></span>
<div class="cols"><label>État<select name="etat"><?php opts(ETATS, $m['etat']); ?></select></label><label>Notes<input name="notes" value="<?= e($m['notes']) ?>"></label></div><button class="btn sm">Mettre à jour</button></form><?php endforeach; ?>
<?php if ($ms): ?><form method="post" class="card form" style="margin:24px 0"><?= csrf_field() ?><h3>Déclarer une alerte</h3><input type="hidden" name="action" value="alerte"><div class="cols">
<label>Machine<select name="machine_id"><?php foreach ($ms as $m): ?><option value="<?= (int)$m['id'] ?>"><?= e($m['type'] . ' ' . $m['marque']) ?></option><?php endforeach; ?></select></label>
<label>Titre<input name="titre" required></label><label>Priorité<select name="priorite"><?php opts(['Basse', 'Moyenne', 'Haute'], 'Moyenne'); ?></select></label></div>
<label>Description<textarea name="description" rows="2"></textarea></label><button class="btn">Envoyer l'alerte</button></form><?php endif; ?>
<h3>Mes alertes</h3><table><tr><th>Date</th><th>Machine</th><th>Alerte</th><th>Priorité</th><th>Statut</th></tr>
<?php foreach ($als as $a): ?><tr><td><?= e($a['date']) ?></td><td><?= e($a['type'] . ' ' . $a['marque']) ?></td><td><b><?= e($a['titre']) ?></b><br><?= e($a['description']) ?></td><td><?= pill($a['priorite']) ?></td><td><?= pill($a['statut']) ?></td></tr><?php endforeach; ?>
<?php if (!$als): ?><tr><td colspan="5" class="muted">Aucune alerte.</td></tr><?php endif; ?></table>
<?php espace_end(); ?>
