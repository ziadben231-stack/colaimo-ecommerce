<?php require 'includes/config.php'; require 'includes/layout.php'; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf();
  if (post('nom') === '' || post('login') === '') $err = 'Nom et identifiant obligatoires.';
  elseif (strlen($_POST['password'] ?? '') < 6) $err = 'Le mot de passe doit contenir au moins 6 caractères.';
  elseif (($_POST['password'] ?? '') !== ($_POST['password2'] ?? '')) $err = 'Les mots de passe ne correspondent pas.';
  elseif (q('SELECT 1 FROM users WHERE login=?', [post('login')])->fetch()) $err = 'Cet identifiant existe déjà.';
  else {
    q("INSERT INTO users(nom,login,password,role,telephone) VALUES(?,?,?,'client',?)", [post('nom'), post('login'), password_hash($_POST['password'], PASSWORD_DEFAULT), post('telephone')]);
    $u = q('SELECT * FROM users WHERE id=?', [db()->lastInsertId()])->fetch(); unset($u['password']);
    session_regenerate_id(true); $_SESSION['user'] = $u; flash('Bienvenue chez COLAIMO !'); redirect(url('produits.php'));
  }
}
page_start('Créer un compte'); ?>
<section><div class="wrap"><div class="login"><form class="card form" method="post"><h1>Créer un compte client</h1><?= csrf_field() ?>
<label>Nom complet<input name="nom" required value="<?= e(post('nom')) ?>"></label>
<label>Identifiant<input name="login" required value="<?= e(post('login')) ?>"></label>
<label>Téléphone<input name="telephone" value="<?= e(post('telephone')) ?>"></label>
<label>Mot de passe (6 caractères min.)<input type="password" name="password" required></label>
<label>Confirmer le mot de passe<input type="password" name="password2" required></label>
<button class="btn">Créer mon compte</button><?php if ($err): ?><div class="msg"><?= e($err) ?></div><?php endif; ?></form></div></div></section>
<?php page_end(); ?>
