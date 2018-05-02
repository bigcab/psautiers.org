-- phpMyAdmin SQL Dump
-- version 3.3.10deb1
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Lun 26 Décembre 2011 à 21:54
-- Version du serveur: 5.1.54
-- Version de PHP: 5.3.5-1ubuntu7.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `ppiv`
--

-- --------------------------------------------------------

--
-- Structure de la table `accent_db`
--

CREATE TABLE IF NOT EXISTS `accent_db` (
  `mot` varchar(50) NOT NULL,
  `syllabe` varchar(50) NOT NULL,
  `id_piece` int(11) NOT NULL,
  `vers_n` int(11) NOT NULL,
  `syllabe_n` int(11) NOT NULL,
  `accent` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `accent_mus_db`
--

CREATE TABLE IF NOT EXISTS `accent_mus_db` (
  `mot` varchar(50) NOT NULL,
  `syllabe` varchar(50) NOT NULL,
  `id_piece` int(11) NOT NULL,
  `vers_n` int(11) NOT NULL,
  `syllabe_n` int(11) NOT NULL,
  `accent` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bases`
--

CREATE TABLE IF NOT EXISTS `bases` (
  `id_base` int(50) NOT NULL AUTO_INCREMENT,
  `nom_base` varchar(50) NOT NULL,
  `description` varchar(50) NOT NULL,
  `references` text NOT NULL,
  `guide_pdf` varchar(50) NOT NULL DEFAULT '',
  `owner` int(11) NOT NULL,
  `permissions_groupe` int(11) NOT NULL,
  `permissions_others` int(11) NOT NULL,
  `updated` tinyint(1) NOT NULL DEFAULT '0',
  `export` varchar(50) NOT NULL DEFAULT '',
  `body_background_color` varchar(50) NOT NULL DEFAULT 'rgb(204,220,255)',
  `banner` varchar(50) NOT NULL DEFAULT './images/design/ban5.jpg',
  `mode` varchar(50) NOT NULL DEFAULT 'default',
  `liens` text NOT NULL DEFAULT '',
  KEY `id_base` (`id_base`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `bugs`
--

CREATE TABLE IF NOT EXISTS `bugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` bigint(20) NOT NULL,
  `resolu` tinyint(1) NOT NULL,
  `texte` text NOT NULL,
  `titre` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




-- --------------------------------------------------------

--
-- Structure de la table `ent_db`
--

CREATE TABLE IF NOT EXISTS `ent_db` (
  `mot` varchar(50) NOT NULL,
  `syllabe` varchar(50) NOT NULL,
    `id_piece` int(11) NOT NULL,
  `vers_n` int(11) NOT NULL,
  `syllabe_n` int(11) NOT NULL,
  `grave` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `ent_mus_db`
--

CREATE TABLE IF NOT EXISTS `ent_mus_db` (
  `mot` varchar(50) NOT NULL,
  `syllabe` varchar(50) NOT NULL,
  `id_piece` int(11) NOT NULL,
  `vers_n` int(11) NOT NULL,
  `syllabe_n` int(11) NOT NULL,
  `grave` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `groupes`
--

CREATE TABLE IF NOT EXISTS `groupes` (
  `id_user` int(11) NOT NULL,
  `id_groupe` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `groupe_textes`
--

CREATE TABLE IF NOT EXISTS `groupe_textes` (
  `id_groupe_texte` int(50) NOT NULL AUTO_INCREMENT,
  `nom_groupe_texte` varchar(50) NOT NULL,
  `libel_1` varchar(50) NOT NULL,
  `libel_2` varchar(50) NOT NULL,
  `libel_3` varchar(50) NOT NULL,
  `commentaire` text NOT NULL,
  PRIMARY KEY (`id_groupe_texte`),
  KEY `id_base` (`nom_groupe_texte`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `hiatus_db`
--

CREATE TABLE IF NOT EXISTS `hiatus_db` (
  `mot` varchar(50) CHARACTER SET utf8 NOT NULL,
  `syllabe` varchar(50) CHARACTER SET utf8 NOT NULL,
    `id_piece` int(11) NOT NULL,
  `vers_n` int(11) NOT NULL,
  `syllabe_n` int(11) NOT NULL,
  `start_hiatus` tinyint(1) NOT NULL,
  `end_hiatus` tinyint(1) NOT NULL,
  `start_pos` int(50) NOT NULL,
  `end_pos` int(50) NOT NULL,
  `hiatus_form` varchar(50) CHARACTER SET utf8 NOT NULL,
  `hiatus_string` varchar(50) CHARACTER SET utf8 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `h_db`
--

CREATE TABLE IF NOT EXISTS `h_db` (
  `mot` varchar(50) NOT NULL,
  `syllabe` varchar(50) NOT NULL,
    `id_piece` int(11) NOT NULL,
  `vers_n` int(11) NOT NULL,
  `syllabe_n` int(11) NOT NULL,
  `aspire` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `h_mus_db`
--

CREATE TABLE IF NOT EXISTS `h_mus_db` (
  `mot` varchar(50) NOT NULL,
  `syllabe` varchar(50) NOT NULL,
    `id_piece` int(11) NOT NULL,
  `vers_n` int(11) NOT NULL,
  `syllabe_n` int(11) NOT NULL,
  `aspire` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `melodies`
--

CREATE TABLE IF NOT EXISTS `melodies` (
  `id_melodie` int(50) NOT NULL AUTO_INCREMENT,
  `melodie` text NOT NULL,
  `rythm` varchar(255) NOT NULL,
  `indice_partie` varchar(50) NOT NULL,
  `fichier_mp3` varchar(50) NOT NULL,
  `commentaire` text NOT NULL,
  KEY `id_base` (`id_melodie`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `contenu` text NOT NULL,
  `timestamp` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `parts`
--

CREATE TABLE IF NOT EXISTS `parts` (
  `id_part` int(50) NOT NULL AUTO_INCREMENT,
  `id_piece` int(50) NOT NULL,
  `id_text` varchar(50) NOT NULL,
  `id_melodie` varchar(50) NOT NULL,
  `indice_partie` int(50) NOT NULL,
  KEY `id_part` (`id_part`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `pieces`
--

CREATE TABLE IF NOT EXISTS `pieces` (
  `id_piece` int(50) NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `auteur` varchar(50) NOT NULL,
  `fichier_finale` varchar(50) NOT NULL,
  `fichier_xml` varchar(50) NOT NULL,
  `png_lilypond` varchar(50) NOT NULL,
  `mp3` varchar(50) NOT NULL,
  `fichier_jpg` varchar(50) NOT NULL,
  `image_incipit_jpg` varchar(50) NOT NULL,
  `note_finale` varchar(50) NOT NULL,
  `ambitus` varchar(50) NOT NULL,
  `armure` varchar(50) NOT NULL,
  `cles` varchar(50) NOT NULL,
  `rubrique` varchar(50) NOT NULL,
  `nombre_parties` varchar(50) NOT NULL,
  `concordances` varchar(200) NOT NULL,
  `texte_additionnel` text NOT NULL,
  `code_table_ref_3` varchar(50) NOT NULL,
  `code_table_ref_4` varchar(50) NOT NULL,
  `code_table_ref_5` varchar(50) NOT NULL,
  `comment_public` varchar(50) NOT NULL,
  `comment_reserve` varchar(50) NOT NULL,
  `comment_revision` varchar(50) NOT NULL,
  `compositeur` varchar(50) NOT NULL,
  `timbre` varchar(50) NOT NULL,
  `valide` int(50) NOT NULL DEFAULT '0',
  `date_validation` int(50) NOT NULL,
  `auteur_validation` varchar(50) NOT NULL,
  `nom_auteur_fiche` varchar(50) NOT NULL,
  `psaume` int(1) NOT NULL DEFAULT '0',
  KEY `id_base` (`id_piece`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `recueils`
--

CREATE TABLE IF NOT EXISTS `recueils` (
  `id_recueil` int(50) NOT NULL AUTO_INCREMENT,
  `id_base` int(50) NOT NULL,
  `titre_uniforme` varchar(50) NOT NULL,
  `titre` text NOT NULL,
  `abreviation` varchar(50) NOT NULL,
  `image_titre_recueil_jpg` varchar(50) NOT NULL,
  `image_table_matieres` varchar(60) NOT NULL,
  `imprimeur` varchar(50) NOT NULL,
  `lieu` varchar(50) NOT NULL,
  `timbre` int(11) NOT NULL,
  `solmisation` int(11) NOT NULL,
  `date_impression` varchar(50) NOT NULL,
  `comment_public` text NOT NULL,
  `comment_reserve` text NOT NULL,
  `nom_auteur_fiche` varchar(50) NOT NULL,
  `date_revision` int(11) NOT NULL DEFAULT '0',
  `nom_auteur_revision` varchar(50) NOT NULL,
  `commentaire_revision` text NOT NULL,
  `editeur` varchar(50) NOT NULL,
  `adresse_biblio` varchar(255) NOT NULL,
  `auteur` varchar(50) NOT NULL,
  `compositeur` varchar(50) NOT NULL,
  `description_materielle` text NOT NULL,
  `sources_bibliographiques` text NOT NULL,
  `litterature_secondaire` text NOT NULL,
  `bibliotheque` varchar(70) NOT NULL,
  `cote` varchar(20) NOT NULL,
  `updated` tinyint(1) NOT NULL DEFAULT '0',
  `export` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_recueil`),
  KEY `id_base` (`id_base`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `table_matieres`
--

CREATE TABLE IF NOT EXISTS `table_matieres` (
  `id_recueil` int(50) NOT NULL,
  `rang` int(50) NOT NULL,
  `pagination_ancienne` varchar(10) NOT NULL,
  `id_piece` varchar(50) NOT NULL,
  `notes_biblio_pages_orig` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `textes`
--

CREATE TABLE IF NOT EXISTS `textes` (
  `id_text` int(50) NOT NULL AUTO_INCREMENT,
  `texte` text NOT NULL,
  `auteur` varchar(50) NOT NULL,
  `id_groupe_texte` varchar(50) NOT NULL,
  `references_groupe_texte` varchar(50) NOT NULL,
  `biblio_texte` text NOT NULL,
  PRIMARY KEY (`id_text`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `pseudo` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `permissions` int(11) NOT NULL,
  `ip` varchar(50) NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
INSERT INTO `users` (`id_user`, `pseudo`, `password`, `permissions`, `ip`) VALUES
(4, 'root', '63afc07624f8afd2afa1f066ce3d340f', 1, '127.0.0.1'),
(5, 'alice', '63afc07624f8afd2afa1f066ce3d340f', 1, ''),
(6, 'yoann', '63afc07624f8afd2afa1f066ce3d340f', 1, '127.0.0.1'),
(7, 'test', '098f6bcd4621d373cade4e832627b4f6', 2, '127.0.0.1'),
(8, 'dam', '76ca1ef9eac7ebceeb9267daffd7fe48', 2, '127.0.0.1');

