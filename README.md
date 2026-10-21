# COLAIMO – Site vitrine, boutique en ligne et gestion du parc informatique

Projet PHP / MySQL (phpMyAdmin) + JavaScript (panier en localStorage).

## Installation (XAMPP / WAMP)
1. Copiez le dossier `colaimo` dans `htdocs` (XAMPP) ou `www` (WAMP). Démarrez Apache et MySQL.
2. Dans phpMyAdmin, onglet **Importer** : choisissez `database.sql` (crée la base `colaimo`, les tables et les données de démonstration).
3. Si besoin, adaptez l'utilisateur / mot de passe MySQL en haut de `includes/config.php`.
4. Ouvrez `http://localhost/colaimo/`.

Comptes de démonstration : `admin / admin123` · `employe / emp123` · `client / client123`
(la page de connexion propose trois boutons qui remplissent l'identifiant automatiquement).

## Structure
```
index.php histoire.php produits.php produit.php   Site public
panier.php commander.php                          Panier (localStorage) + validation serveur
login.php register.php logout.php                 Authentification (sessions, bcrypt, jeton CSRF)
includes/  config.php (BDD, helpers, sécurité)  layout.php (gabarits HTML)  produits_crud.php
admin/     index produits commandes utilisateurs machines alertes reclamations plans
employe/   index produits parc
client/    index (commandes) reclamations
css/ js/ img/ uploads/produits/ (photos envoyées)  database.sql
```

## Fonctionnalités par rôle
- **Visiteur** : catalogue (recherche, filtre marque/catégorie), fiche produit, panier, création de compte.
- **Client** : achète les produits (commande validée en base, stock décrémenté), suit ses commandes, envoie des réclamations.
- **Employé** : ajoute / supprime des produits (avec photo), les déclare **disponibles ou indisponibles** ; voit sa machine du parc, met à jour son état et déclare des alertes.
- **Admin** : gère **tous** les produits (ajout, modification, photo, prix, stock, suppression), les commandes (statuts, remise en stock si annulation), les utilisateurs, le parc informatique, les alertes, les réclamations ; consulte les plans.

## Base de données (8 tables)
`users`, `produits`, `commandes`, `commande_lignes`, `machines`, `alertes`, `reclamations` (+ relations par clés étrangères).

## Sécurité (points à citer en soutenance)
Requêtes préparées PDO (anti injection SQL), `htmlspecialchars` sur toutes les sorties (anti XSS), mots de passe `password_hash` (bcrypt), jeton CSRF sur les formulaires, droits vérifiés côté serveur sur chaque page, prix et stocks revérifiés côté serveur à la commande (transaction SQL), envoi de photos contrôlé (type MIME et taille).

## Remarques
- Les photos des produits sont celles de COLAIMO (dossier `img/produits/`). On peut les remplacer depuis l'espace Admin ou Employé (champ « Photo »).
- Les prix, stocks et produits de départ sont des exemples fictifs.
