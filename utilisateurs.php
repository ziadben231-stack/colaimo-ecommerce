<?php require '../includes/config.php'; require '../includes/layout.php'; require_role('admin'); $me = user();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf(); $a = post('action'); $id = (int)post('id');
  try {
    if ($a === 'save') {
      $role = in_array(post('role'), ['admin', 'employe', 'client']) ? post('role') : 'client'; if ($id === (int)$me['id']) $role = 'admin';
      if (post('nom') === '' || post('login') === '') flash('Nom et identifiant obligatoires.', 'bad');
      elseif ($id) {
        q('UPDATE users SET nom=?,login=?,role=?,departement=?,telephone=? WHERE id=?', [post('nom'), post('login'), $role, post('departement'), post('telephone'), $id]);
        if (post('password') !== '') q('UPDATE users SET password=? WHERE id=?', [password_hash(post('password'), PASSWORD_DEFAULT), $id]);
        flash('Utilisateur modifié.');
      } elseif (strlen(post('password')) < 6) flash('Mot de passe obligatoire (6 caractères min.).', 'bad');
      else { q('INSERT INTO users(nom,login,password,role,departement,telephone) VALUES(?,?,?,?,?,?)', [post('nom'), post('login'), password_hash(post('password'), PASSWORD_DEFAULT), $role, post('departement'), post('telephone')]); flash('Utilisateur ajouté.'); }
    }
    if ($a === 'delete' && $id !== (int)$me['id']) { q('DELETE FROM users WHERE id=?', [$id]); flash('Utilisateur supprimé.'); }
  } catch (PDOException $x) { flash('Identifiant déjà utilisé.', 'bad'); }
  redirect(url('admin/utilisateurs.php'));
}
$edit = isset($_GET['edit']) ? q('SELECT * FROM users WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
$rows = q('SELECT * FROM users ORDER BY role,nom')->fetchAll();
espace_start('admin', 'Utilisateurs'); ?>
<form method="post" class="card form" style="margin-bottom:24px"><?= csrf_field() ?><h3><?= $edit ? 'Modifier l\'utilisateur' : 'Ajouter un utilisateur' ?></h3>
<input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>"><div class="cols">
<label>Nom complet<input name="nom" required value="<?= e($edit['nom'] ?? '') ?>"></label><label>Identifiant<input name="login" required value="<?= e($edit['login'] ?? '') ?>"></label>
<label>Mot de passe<?= $edit ? ' (vide = inchangé)' : '' ?><input type="password" name="password" <?= $edit ? '' : 'required' ?>></label>
<label>Rôle<select name="role"><?php opts(['employe' => 'Employé', 'admin' => 'Admin', 'client' => 'Client'], $edit['role'] ?? 'employe', true); ?></select></label>
<label>Département<select name="departement"><option value=""></option><?php opts(DEPTS, $edit['departement'] ?? ''); ?></select></label>
<label>Téléphone<input name="telephone" value="<?= e($edit['telephone'] ?? '') ?>"></label></div>
<div class="row"><button class="btn">Enregistrer</button><?php if ($edit): ?><a class="btn ghost" href="<?= url('admin/utilisateurs.php') ?>">Annuler</a><?php endif; ?></div></form>
<table><tr><th>Nom</th><th>Identifiant</th><th>Rôle</th><th>Département</th><th></th></tr>
<?php foreach ($rows as $u): ?><tr><td><?= e($u['nom']) ?></td><td><?= e($u['login']) ?></td><td><?= e($u['role']) ?></td><td><?= e($u['departement']) ?></td>
<td class="row"><a class="btn sm" href="?edit=<?= (int)$u['id'] ?>">Modifier</a><?php if ($u['id'] != $me['id']) postbtn('delete', $u['id'], 'Supprimer', 'sm red', 'Supprimer cet utilisateur ?'); ?></td></tr><?php endforeach; ?></table>
<?php espace_end(); ?>
