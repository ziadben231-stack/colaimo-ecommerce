<?php require 'includes/config.php';
header('Content-Type: application/json; charset=utf-8');
function fail($m, $c = 400) { http_response_code($c); echo json_encode(['error' => $m]); exit; }
$u = user();
if (!$u || $u['role'] !== 'client') fail('Connectez-vous avec un compte client pour commander.', 401);
check_csrf();
$in = json_decode(file_get_contents('php://input'), true) ?: [];
$items = $in['items'] ?? []; $adresse = trim($in['adresse'] ?? ''); $tel = trim($in['telephone'] ?? '');
if (!$items || $adresse === '' || $tel === '') fail('Panier, adresse et téléphone obligatoires.');
$pdo = db(); $pdo->beginTransaction();
try {
  $total = 0; $lignes = []; $agg = [];
  foreach ($items as $it) { $i = (int)($it['id'] ?? 0); $agg[$i] = min(99, ($agg[$i] ?? 0) + max(1, (int)($it['qte'] ?? 1))); }
  foreach ($agg as $pid => $qte) {
    $p = q('SELECT * FROM produits WHERE id=? FOR UPDATE', [$pid])->fetch();
    if (!$p || !$p['disponible'] || $p['stock'] < $qte) throw new Exception('« ' . ($p['nom'] ?? 'Produit') . ' » n\'est plus disponible en quantité suffisante.');
    $total += $p['prix'] * $qte; $lignes[] = [$p, $qte];
  }
  q('INSERT INTO commandes(user_id,total,statut,adresse,telephone) VALUES(?,?,?,?,?)', [$u['id'], $total, 'En attente', $adresse, $tel]);
  $cid = (int)$pdo->lastInsertId();
  foreach ($lignes as [$p, $qte]) {
    q('INSERT INTO commande_lignes(commande_id,produit_id,nom,prix,qte) VALUES(?,?,?,?,?)', [$cid, $p['id'], $p['nom'], $p['prix'], $qte]);
    q('UPDATE produits SET stock=stock-?, disponible=IF(stock>0,disponible,0) WHERE id=?', [$qte, $p['id']]); // stock = valeur déjà décrémentée
  }
  $pdo->commit(); echo json_encode(['id' => $cid]);
} catch (Exception $x) { $pdo->rollBack(); fail($x->getMessage(), 409); }
