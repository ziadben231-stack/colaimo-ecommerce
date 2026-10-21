<?php require 'includes/config.php'; require 'includes/layout.php';
$want = in_array($_GET['role'] ?? '', ['client', 'employe', 'admin']) ? $_GET['role'] : ''; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf(); $want = post('want');
  $u = q('SELECT * FROM users WHERE login=?', [post('login')])->fetch();
  if (!$u || !password_verify($_POST['password'] ?? '', $u['password'])) $err = 'Identifiant ou mot de passe incorrect.';
  elseif ($want && $u['role'] !== $want) $err = 'Ce compte n\'est pas un compte ' . $want . '.';
  else {
    session_regenerate_id(true); unset($u['password']); $_SESSION['user'] = $u;
    redirect(($_GET['next'] ?? '') === 'panier.php' && $u['role'] === 'client' ? url('panier.php') : url($u['role'] . '/index.php'));
  }
}
page_start('Connexion'); ?>
<section><div class="wrap"><div class="login"><div class="card form"><h1>Connexion</h1>
<p class="muted">Choisissez votre espace : l'identifiant est rempli pour vous (comptes de démonstration).</p>
<div class="roles"><?php foreach (['client' => 'Client', 'employe' => 'Employé', 'admin' => 'Admin'] as $k => $l): ?><button type="button" class="btn ghost" data-role="<?= $k ?>"><?= $l ?></button><?php endforeach; ?></div>
<form method="post"><?= csrf_field() ?><input type="hidden" name="want" id="want" value="<?= e($want) ?>">
<label>Identifiant<input name="login" id="login" list="ids" required autocomplete="username"></label>
<datalist id="ids"><option value="client"><option value="employe"><option value="admin"></datalist>
<label>Mot de passe<input name="password" id="pw" type="password" required autocomplete="current-password"></label>
<button class="btn">Se connecter</button><?php if ($err): ?><div class="msg"><?= e($err) ?></div><?php endif; ?></form>
<p>Pas de compte ? <a href="<?= url('register.php') ?>">Créer un compte client</a></p></div></div></div></section>
<script>
const DEMO = { client: ['client', 'client123'], employe: ['employe', 'emp123'], admin: ['admin', 'admin123'] };
function pick(k) { document.getElementById('want').value = k; document.getElementById('login').value = DEMO[k][0]; document.getElementById('pw').value = DEMO[k][1];
  document.querySelectorAll('[data-role]').forEach(b => b.classList.toggle('on', b.dataset.role === k)); }
document.querySelectorAll('[data-role]').forEach(b => b.onclick = () => pick(b.dataset.role));
<?php if ($want && !$err): ?>pick(<?= json_encode($want) ?>);<?php endif; ?>
</script>
<?php page_end(); ?>
