<?php
// ===== Configuration générale COLAIMO =====
session_start();
const DB_HOST = 'localhost'; const DB_NAME = 'colaimo'; const DB_USER = 'root'; const DB_PASS = '';  // à adapter (XAMPP : root sans mot de passe)

const MARQUES = ['COLAIMO', 'COLAIMO Nature', 'COLAIMO Raibi', 'Moumtie', 'Sehy'];
const DEPTS = ['Direction Administrative et Financière', 'Direction Commerciale', 'Direction Ressources Humaines', 'Direction Maintenance et travaux neufs', 'Direction de Production', 'Direction des Achats', 'Direction Qualité', 'Direction Amont Laitier'];
const TYPES = ['Poste', 'Portable', 'Imprimante partagée', 'Imprimante locale', 'Switch 24P', 'Switch 6-8P', 'Serveur', 'PDA'];
const ETATS = ['En service', 'En panne', 'En maintenance', 'Hors service'];
const STATUTS_CMD = ['En attente', 'Confirmée', 'Livrée', 'Annulée'];
const LOCAUX = ['Bâtiment A1 – Directeur', 'Bâtiment A1 – Informatique 1', 'Bâtiment A1 – Informatique 2', 'Bâtiment A1 – Comptabilité', 'Bâtiment A1 – Finance', 'Bâtiment A1 – Caisse', 'Bâtiment A1 – Qualité', 'Bâtiment A2 – Facturation', 'Bâtiment A2 – Commercial', 'Bâtiment A2 – Médical', 'Bâtiment A2 – Maintenance', 'Bâtiment A3 – Charge', 'Usine Bâtiment B – Laboratoire 1', 'Usine Bâtiment B – Fabrication', 'Bâtiment C – Magasin', 'Bâtiment D – Parc Auto', 'Salle serveur'];
const MENUS = [
  'admin' => ['index.php' => 'Tableau de bord', 'produits.php' => 'Produits', 'commandes.php' => 'Commandes', 'utilisateurs.php' => 'Utilisateurs', 'machines.php' => 'Parc informatique', 'alertes.php' => 'Alertes', 'reclamations.php' => 'Réclamations', 'plans.php' => 'Plans et organigramme'],
  'employe' => ['index.php' => 'Accueil', 'produits.php' => 'Produits', 'parc.php' => 'Ma machine et alertes'],
  'client' => ['index.php' => 'Mes commandes', 'reclamations.php' => 'Réclamations']];

// Chemin de base du site (fonctionne dans htdocs/colaimo ou à la racine)
$__root = str_replace('\\', '/', (string)realpath(__DIR__ . '/..'));
$__doc = str_replace('\\', '/', (string)realpath($_SERVER['DOCUMENT_ROOT'] ?? ''));
define('BASE', ($__doc !== '' && strpos($__root, $__doc) === 0) ? rtrim(substr($__root, strlen($__doc)), '/') . '/' : '/');

function db(): PDO {
  static $pdo = null;
  if (!$pdo) {
    try {
      $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
    } catch (PDOException $x) {
      http_response_code(500);
      exit('Connexion MySQL impossible : importez database.sql dans phpMyAdmin puis vérifiez includes/config.php.');
    }
  }
  return $pdo;
}
function q($sql, $args = []) { $s = db()->prepare($sql); $s->execute($args); return $s; }
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function url($p = '') { return BASE . $p; }
function redirect($u) { header('Location: ' . $u); exit; }
function post($k, $d = '') { return trim((string)($_POST[$k] ?? $d)); }
function money($v) { return number_format((float)$v, 2, ',', ' ') . ' DH'; }
function img($p) { return url($p ?: 'img/produits/default.svg'); }
function user() { return $_SESSION['user'] ?? null; }
function require_role(...$roles) {
  $u = user();
  if (!$u) redirect(url('login.php'));
  if (!in_array($u['role'], $roles, true)) { http_response_code(403); exit('Accès refusé.'); }
}
function csrf() { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16)); return $_SESSION['csrf']; }
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf() . '">'; }
function check_csrf() {
  $t = $_POST['csrf'] ?? ($_SERVER['HTTP_X_CSRF'] ?? '');
  if (!hash_equals(csrf(), (string)$t)) { http_response_code(400); exit('Jeton de sécurité invalide.'); }
}
function flash($m = null, $t = 'ok') {
  if ($m !== null) { $_SESSION['flash'] = [$m, $t]; return null; }
  $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}
function pill($v) {
  $ok = ['Résolue', 'Traitée', 'Livrée', 'En service', 'Confirmée', 'Disponible'];
  $bad = ['Haute', 'Ouverte', 'En panne', 'Hors service', 'Annulée', 'Indisponible'];
  return '<span class="pill ' . (in_array($v, $ok) ? 'ok' : (in_array($v, $bad) ? 'bad' : 'mid')) . '">' . e($v) . '</span>';
}
function opts($list, $cur = '', $assoc = false) {
  foreach ($list as $k => $v) { if (!$assoc) $k = $v;
    echo '<option value="' . e($k) . '"' . ((string)$k === (string)$cur ? ' selected' : '') . '>' . e($v) . '</option>'; }
}
function postbtn($action, $id, $label, $cls = 'sm', $confirm = '') {
  echo '<form method="post" style="display:inline"' . ($confirm ? ' onsubmit="return confirm(\'' . e($confirm) . '\')"' : '') . '>' . csrf_field()
    . '<input type="hidden" name="action" value="' . e($action) . '"><input type="hidden" name="id" value="' . (int)$id . '"><button class="btn ' . e($cls) . '">' . e($label) . '</button></form>';
}
function upload_image($f) {
  if (empty($f['name']) || ($f['error'] ?? 1) !== UPLOAD_ERR_OK || $f['size'] > 2 * 1024 * 1024) return null;
  $ok = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
  $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
  if (!isset($ok[$mime])) return null;
  $dir = __DIR__ . '/../uploads/produits/'; if (!is_dir($dir)) mkdir($dir, 0775, true);
  $name = bin2hex(random_bytes(8)) . '.' . $ok[$mime];
  return move_uploaded_file($f['tmp_name'], $dir . $name) ? 'uploads/produits/' . $name : null;
}
