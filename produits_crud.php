<?php
// Gestion des produits partagée : $full = true (admin : tout) / false (employé : ajouter, supprimer, disponible/indisponible)
function produits_page($full) {
  $role = $full ? 'admin' : 'employe'; require_role($role);
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf(); $a = post('action'); $id = (int)post('id');
    if ($a === 'save') {
      $nom = post('nom'); $prix = max(0, (float)str_replace(',', '.', post('prix', '0'))); $stock = max(0, (int)post('stock', '0'));
      if ($nom === '') flash('Le nom du produit est obligatoire.', 'bad');
      else {
        $d = [in_array(post('marque'), MARQUES) ? post('marque') : 'COLAIMO', $nom, post('categorie'), post('description'), $prix, $stock, isset($_POST['disponible']) ? 1 : 0];
        $img = upload_image($_FILES['image'] ?? []);
        if ($id && $full) {
          q('UPDATE produits SET marque=?,nom=?,categorie=?,description=?,prix=?,stock=?,disponible=? WHERE id=?', array_merge($d, [$id]));
          if ($img) q('UPDATE produits SET image=? WHERE id=?', [$img, $id]);
          flash('Produit modifié.');
        } else {
          q('INSERT INTO produits(marque,nom,categorie,description,prix,stock,disponible,image,cree_par) VALUES(?,?,?,?,?,?,?,?,?)', array_merge($d, [$img, user()['id']]));
          flash('Produit ajouté.');
        }
      }
    }
    if ($a === 'toggle') {
      q('UPDATE produits SET disponible=1-disponible WHERE id=?', [$id]);
      $p = q('SELECT disponible,stock FROM produits WHERE id=?', [$id])->fetch();
      flash($p && $p['disponible'] && $p['stock'] < 1 ? 'Marqué disponible, mais le stock est à 0 : le produit reste non achetable.' : 'Disponibilité mise à jour.');
    }
    if ($a === 'delete') {
      $p = q('SELECT image FROM produits WHERE id=?', [$id])->fetch();
      if ($p && strpos((string)$p['image'], 'uploads/') === 0) @unlink(__DIR__ . '/../' . $p['image']);
      q('DELETE FROM produits WHERE id=?', [$id]); flash('Produit supprimé.');
    }
    redirect(url($role . '/produits.php'));
  }
  $edit = $full && isset($_GET['edit']) ? q('SELECT * FROM produits WHERE id=?', [(int)$_GET['edit']])->fetch() : null;
  $rows = q('SELECT * FROM produits ORDER BY marque, nom')->fetchAll();
  espace_start($role, 'Gestion des produits'); ?>
<form method="post" enctype="multipart/form-data" class="card form" style="margin-bottom:24px"><?= csrf_field() ?>
<h3><?= $edit ? 'Modifier « ' . e($edit['nom']) . ' »' : 'Ajouter un produit' ?></h3><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id'] ?? 0) ?>">
<div class="cols">
<label>Marque<select name="marque"><?php opts(MARQUES, $edit['marque'] ?? ''); ?></select></label>
<label>Nom du produit<input name="nom" required value="<?= e($edit['nom'] ?? '') ?>"></label>
<label>Catégorie<input name="categorie" value="<?= e($edit['categorie'] ?? '') ?>" placeholder="Lait frais, Yaourts…"></label>
<label>Prix (DH)<input name="prix" type="number" step="0.01" min="0" required value="<?= e($edit['prix'] ?? '') ?>"></label>
<label>Stock<input name="stock" type="number" min="0" required value="<?= e($edit['stock'] ?? '0') ?>"></label>
<label>Photo (JPG, PNG, WebP – 2 Mo max)<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label></div>
<label>Description<textarea name="description" rows="2"><?= e($edit['description'] ?? '') ?></textarea></label>
<label class="check"><input type="checkbox" name="disponible" <?= !$edit || $edit['disponible'] ? 'checked' : '' ?>> Disponible à la vente</label>
<div class="row"><button class="btn"><?= $edit ? 'Enregistrer' : 'Ajouter' ?></button><?php if ($edit): ?><a class="btn ghost" href="<?= url('admin/produits.php') ?>">Annuler</a><?php endif; ?></div></form>
<table><tr><th></th><th>Produit</th><th>Prix</th><th>Stock</th><th>Statut</th><th></th></tr>
<?php foreach ($rows as $p): ?><tr><td><img src="<?= e(img($p['image'])) ?>" alt="" width="56"></td>
<td><b><?= e($p['nom']) ?></b><br><span class="muted"><?= e($p['marque']) ?> · <?= e($p['categorie']) ?></span></td><td><?= money($p['prix']) ?></td><td><?= (int)$p['stock'] ?></td>
<td><?= pill($p['disponible'] ? 'Disponible' : 'Indisponible') ?></td>
<td class="row"><?php postbtn('toggle', $p['id'], $p['disponible'] ? 'Rendre indisponible' : 'Rendre disponible');
 if ($full): ?><a class="btn sm" href="?edit=<?= (int)$p['id'] ?>">Modifier</a><?php endif;
 postbtn('delete', $p['id'], 'Supprimer', 'sm red', 'Supprimer ce produit ?'); ?></td></tr><?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="6" class="muted">Aucun produit.</td></tr><?php endif; ?></table>
<?php espace_end(); }
