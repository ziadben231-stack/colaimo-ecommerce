<?php require '../includes/config.php'; require '../includes/layout.php'; require_role('admin');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf(); $a = post('action'); $id = (int)post('id');
  if ($a === 'save') {
    $d = [post('type'), post('marque'), post('modele'), post('serie'), post('departement'), post('local'), (int)post('user_id') ?: null, post('etat'), post('notes')];
    if ($id) q('UPDATE machines SET type=?,marque=?,modele=?,serie=?,departement=?,`local`=?,user_id=?,etat=?,notes=? WHERE id=?', array_merge($d, [$id]));
    else q('INSERT INTO machines(type,marque,modele,serie,departement,`local`,user_id,etat,notes) VALUES(?,?,?,?,?,?,?,?,?)', $d);
    flash('Machine enregistrée.');
  }
  if ($a === 'delete') { q('DELETE FROM machines WHERE id=?', [$id]); flash('Machine supprimée.'); }
  redirect(url('admin/machines.php'));
}
$edit = isset($_GET['edit']) ? q('SELECT * FROM machines WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$emps = [0 => '— Aucun —'] + array_column(q("SELECT id,nom FROM users WHERE role='employe' ORDER BY nom")->fetchAll(), 'nom', 'id');
$rows = q('SELECT m.*,u.nom employe FROM machines m LEFT JOIN users u ON u.id=m.user_id ORDER BY m.id')->fetchAll();
espace_start('admin', 'Parc informatique'); ?>
<form method="post" class="card form" style="margin-bottom:24px"><?= csrf_field() ?><h3><?= $edit ? 'Modifier la machine' : 'Ajouter une machine' ?></h3>
<input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>"><div class="cols">
<label>Type<select name="type"><?php opts(TYPES, $edit['type'] ?? ''); ?></select></label><label>Marque<input name="marque" required value="<?= e($edit['marque'] ?? '') ?>"></label>
<label>Modèle<input name="modele" value="<?= e($edit['modele'] ?? '') ?>"></label><label>N° de série<input name="serie" value="<?= e($edit['serie'] ?? '') ?>"></label>
<label>Département<select name="departement"><?php opts(DEPTS, $edit['departement'] ?? ''); ?></select></label>
<label>Local / salle<input name="local" list="locaux" value="<?= e($edit['local'] ?? '') ?>"></label><datalist id="locaux"><?php foreach (LOCAUX as $l): ?><option value="<?= e($l) ?>"><?php endforeach; ?></datalist>
<label>Employé affecté<select name="user_id"><?php opts($emps, $edit['user_id'] ?? 0, true); ?></select></label><label>État<select name="etat"><?php opts(ETATS, $edit['etat'] ?? 'En service'); ?></select></label></div>
<label>Notes<textarea name="notes" rows="2"><?= e($edit['notes'] ?? '') ?></textarea></label>
<div class="row"><button class="btn">Enregistrer</button><?php if ($edit): ?><a class="btn ghost" href="<?= url('admin/machines.php') ?>">Annuler</a><?php endif; ?></div></form>
<table><tr><th>Type</th><th>Machine</th><th>Département / local</th><th>Employé</th><th>État</th><th></th></tr>
<?php foreach ($rows as $m): ?><tr><td><?= e($m['type']) ?></td><td><?= e($m['marque'] . ' ' . $m['modele']) ?><br><span class="muted"><?= e($m['serie']) ?></span></td><td><?= e($m['departement']) ?><br><span class="muted"><?= e($m['local']) ?></span></td>
<td><?= e($m['employe'] ?? '—') ?></td><td><?= pill($m['etat']) ?></td><td class="row"><a class="btn sm" href="?edit=<?= (int)$m['id'] ?>">Modifier</a><?php postbtn('delete', $m['id'], 'Supprimer', 'sm red', 'Supprimer cette machine ?'); ?></td></tr><?php endforeach; ?></table>
<?php espace_end(); ?>
