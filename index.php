<?php require '../includes/config.php'; require '../includes/layout.php'; require_role('employe'); $u = user();
$n = fn($sql, $a = []) => (int)q($sql, $a)->fetchColumn();
espace_start('employe', 'Bonjour ' . $u['nom']); ?>
<div class="kpis"><div><b><?= $n('SELECT COUNT(*) FROM produits WHERE disponible=1 AND stock>0') ?></b>produits disponibles</div><div><b><?= $n('SELECT COUNT(*) FROM produits WHERE disponible=0 OR stock=0') ?></b>produits indisponibles</div>
<div><b><?= $n('SELECT COUNT(*) FROM machines WHERE user_id=?', [$u['id']]) ?></b>machine(s) affectée(s)</div><div><b><?= $n("SELECT COUNT(*) FROM alertes WHERE user_id=? AND statut<>'Résolue'", [$u['id']]) ?></b>alerte(s) ouverte(s)</div></div>
<p>Depuis cet espace vous pouvez <a href="produits.php">ajouter ou supprimer des produits et les déclarer disponibles ou indisponibles</a>, et <a href="parc.php">suivre votre machine et déclarer une alerte</a>.</p>
<?php espace_end(); ?>
