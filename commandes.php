<?php require '../includes/config.php'; require '../includes/layout.php'; require_role('admin');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf(); $id = (int)post('id'); $new = post('statut'); $old = q('SELECT statut FROM commandes WHERE id=?', [$id])->fetchColumn();
  if ($old && in_array($new, STATUTS_CMD) && $new !== $old) {
    if ($new === 'Annulée') q('UPDATE produits p JOIN commande_lignes l ON l.produit_id=p.id SET p.stock=p.stock+l.qte WHERE l.commande_id=?', [$id]); // remise en stock
    q('UPDATE commandes SET statut=? WHERE id=?', [$new, $id]); flash('Commande #' . $id . ' : ' . $new);
  }
  redirect(url('admin/commandes.php'));
}
$rows = q('SELECT c.*,u.nom FROM commandes c JOIN users u ON u.id=c.user_id ORDER BY c.id DESC')->fetchAll();
espace_start('admin', 'Commandes'); ?>
<table><tr><th>N°</th><th>Client</th><th>Détail</th><th>Total</th><th>Livraison</th><th>Statut</th></tr>
<?php foreach ($rows as $c): $l = q('SELECT * FROM commande_lignes WHERE commande_id=?', [$c['id']])->fetchAll(); ?>
<tr><td>#<?= (int)$c['id'] ?><br><span class="muted"><?= e($c['date_creation']) ?></span></td><td><?= e($c['nom']) ?></td>
<td><?php foreach ($l as $x): ?><?= (int)$x['qte'] ?> × <?= e($x['nom']) ?><br><?php endforeach; ?></td><td><?= money($c['total']) ?></td>
<td><?= e($c['adresse']) ?><br><span class="muted"><?= e($c['telephone']) ?></span></td>
<td><form method="post" class="row"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><select name="statut"><?php opts(STATUTS_CMD, $c['statut']); ?></select><button class="btn sm">OK</button></form></td></tr>
<?php endforeach; ?><?php if (!$rows): ?><tr><td colspan="6" class="muted">Aucune commande.</td></tr><?php endif; ?></table>
<?php espace_end(); ?>
