-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 03 nov. 2026 à 10:55
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `lprs`
--

-- --------------------------------------------------------

--
-- Structure de la table `alumni`
--

DROP TABLE IF EXISTS `alumni`;
CREATE TABLE IF NOT EXISTS `alumni` (
  `id_utilisateur` int NOT NULL,
  `promotion` varchar(50) DEFAULT NULL,
  `cv` varchar(255) DEFAULT NULL,
  `situation_professionnelle` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `alumni`
--

INSERT INTO `alumni` (`id_utilisateur`, `promotion`, `cv`, `situation_professionnelle`) VALUES
(7, 'BTS SIO SLAM', '(CV)', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `appartient_entreprise`
--

DROP TABLE IF EXISTS `appartient_entreprise`;
CREATE TABLE IF NOT EXISTS `appartient_entreprise` (
  `id_utilisateur` int NOT NULL,
  `id_entreprise` int NOT NULL,
  `poste` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`,`id_entreprise`),
  KEY `id_entreprise` (`id_entreprise`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `appartient_entreprise`
--

INSERT INTO `appartient_entreprise` (`id_utilisateur`, `id_entreprise`, `poste`) VALUES
(5, 1, 'a');

-- --------------------------------------------------------

--
-- Structure de la table `canal_forum`
--

DROP TABLE IF EXISTS `canal_forum`;
CREATE TABLE IF NOT EXISTS `canal_forum` (
  `id_canal` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `description` text,
  `type_acces` varchar(50) DEFAULT NULL,
  `roles_createurs` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_canal`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `canal_forum`
--

INSERT INTO `canal_forum` (`id_canal`, `nom`, `description`, `type_acces`, `roles_createurs`) VALUES
(1, 'Général', 'Discussions ouvertes à tous les membres.', 'tous', NULL),
(2, 'Étudiants', 'Espace d\'échange entre étudiants et professeurs.', 'etudiant,professeur', 'etudiant'),
(3, 'Alumnis et entreprises', 'Espace d\'échange entre alumnis et entreprises partenaires.', 'alumni,partenaire', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `candidature`
--

DROP TABLE IF EXISTS `candidature`;
CREATE TABLE IF NOT EXISTS `candidature` (
  `id_candidature` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int DEFAULT NULL,
  `id_offre` int DEFAULT NULL,
  `motivation` text,
  `date_candidature` date DEFAULT NULL,
  `statut` varchar(30) DEFAULT 'En attente',
  PRIMARY KEY (`id_candidature`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `id_offre` (`id_offre`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `entreprise`
--

DROP TABLE IF EXISTS `entreprise`;
CREATE TABLE IF NOT EXISTS `entreprise` (
  `id_entreprise` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `site_web` varchar(255) DEFAULT NULL,
  `secteur_activite` varchar(100) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id_entreprise`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `entreprise`
--

INSERT INTO `entreprise` (`id_entreprise`, `nom`, `adresse`, `code_postal`, `ville`, `site_web`, `secteur_activite`, `description`) VALUES
(1, 'a', 'a', 'a', 'a', 'a.com', 'a', 'a');

-- --------------------------------------------------------

--
-- Structure de la table `etudiant`
--

DROP TABLE IF EXISTS `etudiant`;
CREATE TABLE IF NOT EXISTS `etudiant` (
  `id_utilisateur` int NOT NULL,
  `id_formation` int DEFAULT NULL,
  `num_etudiant` varchar(50) DEFAULT NULL,
  `promotion` varchar(50) DEFAULT NULL,
  `cv` varchar(255) DEFAULT NULL,
  `date_entree` date DEFAULT NULL,
  `statut_scolaire` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`),
  KEY `id_formation` (`id_formation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `etudiant`
--

INSERT INTO `etudiant` (`id_utilisateur`, `id_formation`, `num_etudiant`, `promotion`, `cv`, `date_entree`, `statut_scolaire`) VALUES
(6, 7, NULL, 'BAC STL', '(CV)', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
--

DROP TABLE IF EXISTS `evenement`;
CREATE TABLE IF NOT EXISTS `evenement` (
  `id_evenement` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(150) NOT NULL,
  `type_evenement` varchar(100) DEFAULT NULL,
  `description` text,
  `date_debut` datetime DEFAULT NULL,
  `date_fin` datetime DEFAULT NULL,
  `lieu` varchar(200) DEFAULT NULL,
  `nombre_places` int DEFAULT NULL,
  `elements_requis` text,
  PRIMARY KEY (`id_evenement`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `formation`
--

DROP TABLE IF EXISTS `formation`;
CREATE TABLE IF NOT EXISTS `formation` (
  `id_formation` int NOT NULL AUTO_INCREMENT,
  `nom_formation` varchar(100) NOT NULL,
  `niveau` varchar(50) DEFAULT NULL,
  `duree` varchar(50) DEFAULT NULL,
  `description` text,
  `type_formation` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_formation`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `formation`
--

INSERT INTO `formation` (`id_formation`, `nom_formation`, `niveau`, `duree`, `description`, `type_formation`) VALUES
(1, 'Bac professionnel TRPM', 'Bac', NULL, NULL, 'Professionnel'),
(2, 'Bac professionnel MSPC', 'Bac', NULL, NULL, 'Professionnel'),
(3, 'Bac professionnel CIEL', 'Bac', NULL, NULL, 'Professionnel'),
(4, 'Bac technologique STI2D', 'Bac', NULL, NULL, 'Technologique'),
(5, 'BTS CPRP', 'BTS', NULL, NULL, 'BTS'),
(6, 'BTS MSPC', 'BTS', NULL, NULL, 'BTS'),
(7, 'BTS SIO', 'BTS', NULL, NULL, 'BTS');

-- --------------------------------------------------------

--
-- Structure de la table `gestionnaire`
--

DROP TABLE IF EXISTS `gestionnaire`;
CREATE TABLE IF NOT EXISTS `gestionnaire` (
  `id_utilisateur` int NOT NULL,
  `role` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `gestionnaire`
--

INSERT INTO `gestionnaire` (`id_utilisateur`, `role`) VALUES
(1, 'Gestionnaire');

-- --------------------------------------------------------

--
-- Structure de la table `inscription_evenement`
--

DROP TABLE IF EXISTS `inscription_evenement`;
CREATE TABLE IF NOT EXISTS `inscription_evenement` (
  `id_inscription` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int DEFAULT NULL,
  `id_evenement` int DEFAULT NULL,
  `date_inscription` date DEFAULT NULL,
  `statut` varchar(30) DEFAULT 'Inscrit',
  PRIMARY KEY (`id_inscription`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `id_evenement` (`id_evenement`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sujet_id` int NOT NULL,
  `auteur_id` int NOT NULL,
  `contenu` text NOT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sujet_id` (`sujet_id`),
  KEY `auteur_id` (`auteur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `message_contact`
--

DROP TABLE IF EXISTS `message_contact`;
CREATE TABLE IF NOT EXISTS `message_contact` (
  `id_message` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `sujet` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `date_envoi` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_message`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `offre`
--

DROP TABLE IF EXISTS `offre`;
CREATE TABLE IF NOT EXISTS `offre` (
  `id_offre` int NOT NULL AUTO_INCREMENT,
  `id_entreprise` int DEFAULT NULL,
  `id_auteur` int DEFAULT NULL,
  `titre` varchar(150) NOT NULL,
  `description` text,
  `missions` text,
  `type_offre` varchar(30) DEFAULT NULL,
  `salaire` decimal(10,2) DEFAULT NULL,
  `lieu` varchar(150) DEFAULT NULL,
  `date_publication` date DEFAULT NULL,
  `date_expiration` date DEFAULT NULL,
  `etat` varchar(30) DEFAULT 'Ouverte',
  `profil_cible` varchar(30) NOT NULL DEFAULT 'tous',
  PRIMARY KEY (`id_offre`),
  KEY `id_entreprise` (`id_entreprise`),
  KEY `id_auteur` (`id_auteur`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `offre`
--

INSERT INTO `offre` (`id_offre`, `id_entreprise`, `id_auteur`, `titre`, `description`, `missions`, `type_offre`, `salaire`, `lieu`, `date_publication`, `date_expiration`, `etat`, `profil_cible`) VALUES
(1, 1, 5, 'a', 'a', 'a', 'Stage', 10.00, 'AU', '2026-10-28', '1990-12-12', 'Ouverte', 'tous');

-- --------------------------------------------------------

--
-- Structure de la table `organise`
--

DROP TABLE IF EXISTS `organise`;
CREATE TABLE IF NOT EXISTS `organise` (
  `id_utilisateur` int NOT NULL,
  `id_evenement` int NOT NULL,
  PRIMARY KEY (`id_utilisateur`,`id_evenement`),
  KEY `id_evenement` (`id_evenement`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `partenaire`
--

DROP TABLE IF EXISTS `partenaire`;
CREATE TABLE IF NOT EXISTS `partenaire` (
  `id_utilisateur` int NOT NULL,
  `poste` varchar(100) DEFAULT NULL,
  `service` varchar(100) DEFAULT NULL,
  `motif_inscription` text,
  PRIMARY KEY (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `partenaire`
--

INSERT INTO `partenaire` (`id_utilisateur`, `poste`, `service`, `motif_inscription`) VALUES
(4, 'RH', NULL, 'Mission d\'YHWH'),
(5, 'rh', NULL, 'rh bien'),
(8, 'Concierge', NULL, 'Laver');

-- --------------------------------------------------------

--
-- Structure de la table `post`
--

DROP TABLE IF EXISTS `post`;
CREATE TABLE IF NOT EXISTS `post` (
  `id_post` int NOT NULL AUTO_INCREMENT,
  `id_canal` int DEFAULT NULL,
  `id_utilisateur` int DEFAULT NULL,
  `titre` varchar(150) NOT NULL,
  `contenu` text NOT NULL,
  `date_publication` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_post`),
  KEY `id_canal` (`id_canal`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `post`
--

INSERT INTO `post` (`id_post`, `id_canal`, `id_utilisateur`, `titre`, `contenu`, `date_publication`) VALUES
(1, 1, 5, 'RZQR', 'RZQR', '2026-10-28 16:55:26'),
(2, 1, 5, 'TITRE', 'SOUS TITRE', '2026-10-28 16:56:11');

-- --------------------------------------------------------

--
-- Structure de la table `professeur`
--

DROP TABLE IF EXISTS `professeur`;
CREATE TABLE IF NOT EXISTS `professeur` (
  `id_utilisateur` int NOT NULL,
  `specialite` varchar(100) DEFAULT NULL,
  `grade` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `professeur`
--

INSERT INTO `professeur` (`id_utilisateur`, `specialite`, `grade`) VALUES
(2, 'Mathématiques', NULL),
(3, 'cyber', NULL),
(9, 'math', NULL),
(10, 'math', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `professeur_formation`
--

DROP TABLE IF EXISTS `professeur_formation`;
CREATE TABLE IF NOT EXISTS `professeur_formation` (
  `id_utilisateur` int NOT NULL,
  `id_formation` int NOT NULL,
  PRIMARY KEY (`id_utilisateur`,`id_formation`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `professeur_formation`
--

INSERT INTO `professeur_formation` (`id_utilisateur`, `id_formation`) VALUES
(3, 3),
(3, 7),
(9, 1),
(10, 7);

-- --------------------------------------------------------

--
-- Structure de la table `reinitialisation_mdp`
--

DROP TABLE IF EXISTS `reinitialisation_mdp`;
CREATE TABLE IF NOT EXISTS `reinitialisation_mdp` (
  `id_utilisateur` int NOT NULL,
  `jeton_hash` char(64) NOT NULL,
  `expiration` datetime NOT NULL,
  PRIMARY KEY (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reponse`
--

DROP TABLE IF EXISTS `reponse`;
CREATE TABLE IF NOT EXISTS `reponse` (
  `id_reponse` int NOT NULL AUTO_INCREMENT,
  `id_post` int DEFAULT NULL,
  `id_utilisateur` int DEFAULT NULL,
  `contenu` text NOT NULL,
  `date_publication` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_reponse`),
  KEY `id_post` (`id_post`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `reponse`
--

INSERT INTO `reponse` (`id_reponse`, `id_post`, `id_utilisateur`, `contenu`, `date_publication`) VALUES
(1, 1, 5, 'TEST', '2026-10-28 16:55:44'),
(2, 1, 5, 'TEZT', '2026-10-28 16:55:48');

-- --------------------------------------------------------

--
-- Structure de la table `sujets`
--

DROP TABLE IF EXISTS `sujets`;
CREATE TABLE IF NOT EXISTS `sujets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `section` enum('general','eleves','professeurs') NOT NULL,
  `titre` varchar(150) NOT NULL,
  `auteur_id` int NOT NULL,
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `auteur_id` (`auteur_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `type_profil` varchar(30) NOT NULL,
  `statut_validation` varchar(30) DEFAULT 'En attente',
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom`, `prenom`, `email`, `mot_de_passe`, `type_profil`, `statut_validation`) VALUES
(9, 'Matias', 'Dylan', 'D.Matias@lprs.fr', '$2y$10$x17SkQbXJQhMD/h53zqFn.fFQnw0JvKWyzh1WjOOwI/iMvQybo6Jm', 'professeur', 'Validé'),
(6, 'Marco', 'Mendes', 'M.MENDES@lprs.fr', '$2y$10$e5bhHmN3SP3llleOKx9pJOecLUHs2ieY1s4jGKoaDhoO1btMQJZwy', 'etudiant', 'Validé'),
(7, 'Nguyen', 'Mathis', 'M.Nguyen@lprs.fr', '$2y$10$XrN3KUms6yvs84B0ilBj4.3nzUa8lHk/yN4X/6AFYfBWhBtzaITaq', 'alumni', 'Validé'),
(8, 'Delorme', 'Kevyn', 'K.Delorme@lprs.fr', '$2y$10$4bx6/5tszGYfrQkOn2htq.gipeznw9prRMyOy/kDQd3gulfe1fOZ2', 'partenaire', 'Validé'),
(10, 'admin', 'root', 'admin@lprs.fr', '$2y$10$SdaVPOoi8tyrWSv6Trb8teQKaQIQveVXUAt4cVtHM2cfcYSibkTaa', 'gestionnaire', 'Validé');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
