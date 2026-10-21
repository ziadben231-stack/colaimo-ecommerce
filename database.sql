-- =====================================================================
--  COLAIMO : base de données MySQL / MariaDB
--  Import : phpMyAdmin > Importer > choisir ce fichier
-- =====================================================================
CREATE DATABASE IF NOT EXISTS colaimo
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE colaimo;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS commande_lignes, commandes, alertes, reclamations, machines, produits, users;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------- Utilisateurs (admin, employe, client) ----------
CREATE TABLE users (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  nom            VARCHAR(100) NOT NULL,
  login          VARCHAR(50)  NOT NULL UNIQUE,
  password       VARCHAR(255) NOT NULL,                 -- hash bcrypt (password_hash)
  role           ENUM('admin','employe','client') NOT NULL DEFAULT 'client',
  departement    VARCHAR(100) DEFAULT '',
  telephone      VARCHAR(20)  DEFAULT '',
  date_creation  DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------- Produits ----------
CREATE TABLE produits (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  marque         VARCHAR(40)  NOT NULL,
  nom            VARCHAR(100) NOT NULL,
  categorie      VARCHAR(60)  DEFAULT '',
  description    TEXT,
  prix           DECIMAL(8,2) NOT NULL DEFAULT 0,
  stock          INT          NOT NULL DEFAULT 0,
  disponible     TINYINT(1)   NOT NULL DEFAULT 1,
  image          VARCHAR(255) DEFAULT NULL,
  cree_par       INT NULL,
  date_creation  DATETIME     DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_marque (marque),
  CONSTRAINT fk_produit_user FOREIGN KEY (cree_par) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------- Commandes des clients ----------
CREATE TABLE commandes (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  user_id        INT NOT NULL,
  total          DECIMAL(10,2) NOT NULL,
  statut         VARCHAR(20)   DEFAULT 'En attente',    -- En attente, Confirmée, Livrée, Annulée
  adresse        TEXT,
  telephone      VARCHAR(20),
  date_creation  DATETIME      DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_commande_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE commande_lignes (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  commande_id    INT NOT NULL,
  produit_id     INT NULL,
  nom            VARCHAR(100)  NOT NULL,                -- copie du nom au moment de l'achat
  prix           DECIMAL(8,2)  NOT NULL,                -- copie du prix au moment de l'achat
  qte            INT           NOT NULL,
  CONSTRAINT fk_ligne_commande FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
  CONSTRAINT fk_ligne_produit  FOREIGN KEY (produit_id)  REFERENCES produits(id)  ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------- Parc informatique ----------
CREATE TABLE machines (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  type           VARCHAR(40),
  marque         VARCHAR(60),
  modele         VARCHAR(60),
  serie          VARCHAR(60),
  departement    VARCHAR(100),
  `local`        VARCHAR(100),
  user_id        INT NULL,                              -- employé affecté
  etat           VARCHAR(20) DEFAULT 'En service',
  notes          TEXT,
  CONSTRAINT fk_machine_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE alertes (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  machine_id     INT NULL,
  user_id        INT NOT NULL,
  titre          VARCHAR(150),
  description    TEXT,
  priorite       VARCHAR(10) DEFAULT 'Moyenne',
  statut         VARCHAR(15) DEFAULT 'Ouverte',
  `date`         DATETIME    DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_alerte_machine FOREIGN KEY (machine_id) REFERENCES machines(id) ON DELETE CASCADE,
  CONSTRAINT fk_alerte_user    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------- Réclamations des clients ----------
CREATE TABLE reclamations (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  user_id        INT NOT NULL,
  sujet          VARCHAR(150),
  message        TEXT,
  reponse        TEXT,
  statut         VARCHAR(15) DEFAULT 'Ouverte',
  `date`         DATETIME    DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_reclam_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =====================================================================
--  Données de démonstration
--  Comptes : admin / admin123 · employe / emp123 · client / client123
-- =====================================================================
INSERT INTO users (nom, login, password, role, departement) VALUES
 ('Technicien Informatique', 'admin',   '$2y$12$6x/VE6oFovPUwJnqVFEkZOQkM10nAb.ko/dUmXWYngYgEZ1Ue5gIC', 'admin',   'Direction Administrative et Financière'),
 ('Employé Démo',            'employe', '$2y$12$ZCrX90wAo3f/HuRe8N7/c.1MWzhj.rufgKS6skMz.GhUIaicJgtLu', 'employe', 'Direction Commerciale'),
 ('Client Démo',             'client',  '$2y$12$fwxUbAEooZOTb.vgmsZ4BOsjo41ks1iUVhWrASqcoQYoJyEUG0G2a', 'client',  '');

-- Produits (photos réelles dans img/produits ; prix et stocks fictifs à adapter)
INSERT INTO produits (marque, nom, categorie, description, prix, stock, disponible, image, cree_par) VALUES
 ('COLAIMO',       'Lait frais',               'Lait frais',     'Lait frais COLAIMO, disponible en brique et en sachet.',                     7.00, 120, 1, 'img/produits/lait.jpg',           1),
 ('COLAIMO',       'L''ben',                   'Lait fermenté',  'Lait fermenté L''ben COLAIMO, en brique et en sachet.',                      6.00,  80, 1, 'img/produits/leben.jpg',          1),
 ('COLAIMO',       'Beurre Super Colaimo',     'Beurre',         'Beurre Super Colaimo, disponible en 1 kg, 1/2 kg et 1/4 kg.',               18.00,  40, 1, 'img/produits/beurre-super.png',   1),
 ('COLAIMO Nature','Yaourt nature 0 % sucre',  'Yaourts',        'Yaourt nature ferme, 0 % de sucre ajouté.',                                  2.00, 150, 1, 'img/produits/nature.jpg',         1),
 ('COLAIMO Raibi', 'Raïbi Grenadine',          'Yaourt à boire', 'Raïbi COLAIMO parfum grenadine.',                                            3.50, 200, 1, 'img/produits/raibi-grenadine.jpg', 1),
 ('COLAIMO Raibi', 'Raïbi Pistache',           'Yaourt à boire', 'Raïbi COLAIMO parfum pistache.',                                             3.50, 200, 1, 'img/produits/raibi-pistache.jpg',  1),
 ('Moumtie',       'Moumtie banane',           'Yaourt à boire', 'Moumtie, le yaourt à boire COLAIMO, parfum banane.',                         3.00, 180, 1, 'img/produits/moumtie.png',        1),
 ('Sehy',          'Sehy vanille',             'Yaourts',        'Sehy, yaourt ferme COLAIMO parfum vanille.',                                 2.50, 160, 1, 'img/produits/sehy.jpg',           1);

INSERT INTO machines (type, marque, modele, serie, departement, `local`, user_id, etat, notes) VALUES
 ('Poste',               'HP',      'ProDesk 400', 'EX-0001', 'Direction Commerciale',                  'Bâtiment A2 – Commercial',  2,    'En service', 'Exemple de démonstration'),
 ('Imprimante partagée', 'Canon',   'iR 2425',     'EX-0002', 'Direction Administrative et Financière', 'Bâtiment A1 – Comptabilité', NULL, 'En service', ''),
 ('Switch 24P',          'TP-Link', 'TL-SG1024',   'EX-0003', 'Direction Administrative et Financière', 'Salle serveur',             NULL, 'En service', '');
