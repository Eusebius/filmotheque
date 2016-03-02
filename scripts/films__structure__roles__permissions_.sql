-- phpMyAdmin SQL Dump
-- version 4.5.3.1deb1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mer 02 Mars 2016 à 19:03
-- Version du serveur :  5.6.28-1
-- Version de PHP :  5.6.14-1

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `films`
--

-- --------------------------------------------------------

--
-- Structure de la table `borrowers`
--

CREATE TABLE IF NOT EXISTS `borrowers` (
  `id_borrower` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `borrowername` varchar(128) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_borrower`),
  UNIQUE KEY `borrowername` (`borrowername`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `borrowers`:
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `category` varchar(128) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `categories`:
--

-- --------------------------------------------------------

--
-- Structure de la table `experience`
--

CREATE TABLE IF NOT EXISTS `experience` (
  `id_movie` int(10) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `lastseen` date DEFAULT NULL,
  PRIMARY KEY (`id_movie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `experience`:
--   `id_movie`
--       `movies` -> `id_movie`
--

-- --------------------------------------------------------

--
-- Structure de la table `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `language` varchar(10) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `languages`:
--

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

CREATE TABLE IF NOT EXISTS `media` (
  `id_medium` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_movie` int(10) UNSIGNED NOT NULL,
  `type` varchar(128) COLLATE utf8_bin NOT NULL,
  `height` int(10) UNSIGNED DEFAULT NULL,
  `width` int(10) UNSIGNED DEFAULT NULL,
  `comment` text COLLATE utf8_bin,
  `shelfmark` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id_medium`),
  UNIQUE KEY `shelfmark` (`shelfmark`),
  KEY `id_movie` (`id_movie`)
) ENGINE=InnoDB AUTO_INCREMENT=1109 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `media`:
--   `id_movie`
--       `movies` -> `id_movie`
--

-- --------------------------------------------------------

--
-- Structure de la table `media-audio`
--

CREATE TABLE IF NOT EXISTS `media-audio` (
  `id_medium` int(10) UNSIGNED NOT NULL,
  `language` varchar(10) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_medium`,`language`),
  KEY `language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `media-audio`:
--   `id_medium`
--       `media` -> `id_medium`
--   `language`
--       `languages` -> `language`
--

-- --------------------------------------------------------

--
-- Structure de la table `media-borrowers`
--

CREATE TABLE IF NOT EXISTS `media-borrowers` (
  `id_medium` int(10) UNSIGNED NOT NULL,
  `id_borrower` int(10) UNSIGNED NOT NULL,
  `loandate` date NOT NULL DEFAULT '0000-00-00',
  `backdate` date DEFAULT NULL,
  PRIMARY KEY (`id_medium`,`id_borrower`,`loandate`),
  KEY `id_borrower` (`id_borrower`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `media-borrowers`:
--   `id_medium`
--       `media` -> `id_medium`
--   `id_borrower`
--       `borrowers` -> `id_borrower`
--

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `media-quality`
--
CREATE TABLE IF NOT EXISTS `media-quality` (
`id_medium` int(10) unsigned
,`quality` varchar(32)
);

-- --------------------------------------------------------

--
-- Structure de la table `media-subs`
--

CREATE TABLE IF NOT EXISTS `media-subs` (
  `id_medium` int(10) UNSIGNED NOT NULL,
  `language` varchar(10) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_medium`,`language`),
  KEY `language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `media-subs`:
--   `id_medium`
--       `media` -> `id_medium`
--   `language`
--       `languages` -> `language`
--

-- --------------------------------------------------------

--
-- Structure de la table `movies`
--

CREATE TABLE IF NOT EXISTS `movies` (
  `id_movie` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(1024) COLLATE utf8_bin NOT NULL,
  `year` int(11) DEFAULT NULL,
  `imdb_id` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `originaltitle` varchar(1024) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id_movie`),
  KEY `title` (`title`(255))
) ENGINE=InnoDB AUTO_INCREMENT=9160 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `movies`:
--

-- --------------------------------------------------------

--
-- Structure de la table `movies-actors`
--

CREATE TABLE IF NOT EXISTS `movies-actors` (
  `id_movie` int(10) UNSIGNED NOT NULL,
  `id_person` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_movie`,`id_person`),
  KEY `id_person` (`id_person`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `movies-actors`:
--   `id_movie`
--       `movies` -> `id_movie`
--   `id_person`
--       `persons` -> `id_person`
--

-- --------------------------------------------------------

--
-- Structure de la table `movies-categories`
--

CREATE TABLE IF NOT EXISTS `movies-categories` (
  `id_movie` int(10) UNSIGNED NOT NULL,
  `category` varchar(128) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_movie`,`category`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `movies-categories`:
--   `id_movie`
--       `movies` -> `id_movie`
--   `category`
--       `categories` -> `category`
--

-- --------------------------------------------------------

--
-- Structure de la table `movies-makers`
--

CREATE TABLE IF NOT EXISTS `movies-makers` (
  `id_movie` int(10) UNSIGNED NOT NULL,
  `id_person` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_movie`,`id_person`),
  KEY `id_person` (`id_person`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `movies-makers`:
--   `id_movie`
--       `movies` -> `id_movie`
--   `id_person`
--       `persons` -> `id_person`
--

-- --------------------------------------------------------

--
-- Structure de la table `movies-shortlists`
--

CREATE TABLE IF NOT EXISTS `movies-shortlists` (
  `id_movie` int(10) UNSIGNED NOT NULL,
  `id_shortlist` int(10) UNSIGNED NOT NULL,
  UNIQUE KEY `id_movie` (`id_movie`,`id_shortlist`),
  KEY `id_shortlist` (`id_shortlist`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `movies-shortlists`:
--   `id_movie`
--       `movies` -> `id_movie`
--   `id_shortlist`
--       `shortlists` -> `id_shortlist`
--

-- --------------------------------------------------------

--
-- Structure de la table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `permission` varchar(32) COLLATE utf8_bin NOT NULL,
  `permdescription` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `permissions`:
--

--
-- Contenu de la table `permissions`
--

REPLACE INTO `permissions` (`permission`, `permdescription`) VALUES
('admin', 'Allow administration of the website (including user, role and permission management)'),
('lastseen', 'Allow read access to lastseen information'),
('rating', 'Allow read access to rating information'),
('read', 'Read access to basic information (does not cover shortlists, rating or lastseen)'),
('shortlists', 'Allow read access to shortlist information'),
('write', 'Write access to all information');

-- --------------------------------------------------------

--
-- Structure de la table `persons`
--

CREATE TABLE IF NOT EXISTS `persons` (
  `id_person` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `surname_afac` varchar(1024) COLLATE utf8_bin NOT NULL,
  `firstnames_afac` varchar(1024) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(1024) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_person`),
  KEY `surname` (`surname_afac`(255)),
  KEY `name` (`name`(255))
) ENGINE=InnoDB AUTO_INCREMENT=1820 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `persons`:
--

-- --------------------------------------------------------

--
-- Structure de la table `quality`
--

CREATE TABLE IF NOT EXISTS `quality` (
  `quality` varchar(32) COLLATE utf8_bin NOT NULL,
  `minwidth` int(11) DEFAULT NULL,
  `minheight` int(11) DEFAULT NULL,
  `maxwidth` int(11) DEFAULT NULL,
  `maxheight` int(11) DEFAULT NULL,
  PRIMARY KEY (`quality`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `quality`:
--

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `role` varchar(32) COLLATE utf8_bin NOT NULL,
  `description` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `roles`:
--

--
-- Contenu de la table `roles`
--

REPLACE INTO `roles` (`role`, `description`) VALUES
('admin', 'Website administrators'),
('ro', 'Users with basic read-only rights'),
('rw', 'Users with full read/write access, but no administration rights');

-- --------------------------------------------------------

--
-- Structure de la table `roles-permissions`
--

CREATE TABLE IF NOT EXISTS `roles-permissions` (
  `role` varchar(32) COLLATE utf8_bin NOT NULL,
  `permission` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`role`,`permission`) USING BTREE,
  KEY `roles-permissions_ibfk_2` (`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `roles-permissions`:
--   `role`
--       `roles` -> `role`
--   `permission`
--       `permissions` -> `permission`
--

--
-- Contenu de la table `roles-permissions`
--

REPLACE INTO `roles-permissions` (`role`, `permission`) VALUES
('admin', 'admin'),
('admin', 'lastseen'),
('rw', 'lastseen'),
('admin', 'rating'),
('rw', 'rating'),
('admin', 'read'),
('ro', 'read'),
('rw', 'read'),
('admin', 'shortlists'),
('rw', 'shortlists'),
('admin', 'write'),
('rw', 'write');

-- --------------------------------------------------------

--
-- Structure de la table `shortlists`
--

CREATE TABLE IF NOT EXISTS `shortlists` (
  `id_shortlist` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `listname` varchar(128) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_shortlist`),
  UNIQUE KEY `name` (`listname`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `shortlists`:
--

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `login` varchar(255) COLLATE utf8_bin NOT NULL,
  `email` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `password` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `users`:
--

-- --------------------------------------------------------

--
-- Structure de la table `users-roles`
--

CREATE TABLE IF NOT EXISTS `users-roles` (
  `login` varchar(255) COLLATE utf8_bin NOT NULL,
  `role` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`login`,`role`),
  KEY `users-roles_ibfk_2` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `users-roles`:
--   `login`
--       `users` -> `login`
--   `role`
--       `roles` -> `role`
--

-- --------------------------------------------------------

--
-- Structure de la vue `media-quality`
--
DROP TABLE IF EXISTS `media-quality`;

CREATE ALGORITHM=UNDEFINED DEFINER=`films`@`localhost` SQL SECURITY DEFINER VIEW `media-quality`  AS  select `media`.`id_medium` AS `id_medium`,`quality`.`quality` AS `quality` from (`media` join `quality`) where (((`media`.`width` < `quality`.`maxwidth`) and (`media`.`height` < `quality`.`maxheight`) and ((`media`.`width` >= `quality`.`minwidth`) or (`media`.`height` >= `quality`.`minheight`))) or (isnull(`media`.`height`) and (`media`.`width` < `quality`.`maxwidth`) and (`media`.`width` >= `quality`.`minwidth`)) or (isnull(`media`.`width`) and (`media`.`height` < `quality`.`maxheight`) and (`media`.`height` >= `quality`.`minheight`)) or (isnull(`media`.`height`) and isnull(`quality`.`minheight`) and isnull(`media`.`width`) and isnull(`quality`.`minwidth`))) ;

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `experience`
--
ALTER TABLE `experience`
  ADD CONSTRAINT `experience_ibfk_1` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media-audio`
--
ALTER TABLE `media-audio`
  ADD CONSTRAINT `media@002daudio_ibfk_3` FOREIGN KEY (`id_medium`) REFERENCES `media` (`id_medium`) ON DELETE CASCADE,
  ADD CONSTRAINT `media@002daudio_ibfk_4` FOREIGN KEY (`language`) REFERENCES `languages` (`language`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media-borrowers`
--
ALTER TABLE `media-borrowers`
  ADD CONSTRAINT `media@002dborrowers_ibfk_3` FOREIGN KEY (`id_medium`) REFERENCES `media` (`id_medium`) ON DELETE CASCADE,
  ADD CONSTRAINT `media@002dborrowers_ibfk_4` FOREIGN KEY (`id_borrower`) REFERENCES `borrowers` (`id_borrower`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media-subs`
--
ALTER TABLE `media-subs`
  ADD CONSTRAINT `media@002dsubs_ibfk_3` FOREIGN KEY (`id_medium`) REFERENCES `media` (`id_medium`) ON DELETE CASCADE,
  ADD CONSTRAINT `media@002dsubs_ibfk_4` FOREIGN KEY (`language`) REFERENCES `languages` (`language`) ON DELETE CASCADE;

--
-- Contraintes pour la table `movies-actors`
--
ALTER TABLE `movies-actors`
  ADD CONSTRAINT `movies@002dactors_ibfk_3` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE,
  ADD CONSTRAINT `movies@002dactors_ibfk_4` FOREIGN KEY (`id_person`) REFERENCES `persons` (`id_person`) ON DELETE CASCADE;

--
-- Contraintes pour la table `movies-categories`
--
ALTER TABLE `movies-categories`
  ADD CONSTRAINT `movies@002dcategories_ibfk_5` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE,
  ADD CONSTRAINT `movies@002dcategories_ibfk_6` FOREIGN KEY (`category`) REFERENCES `categories` (`category`) ON DELETE CASCADE;

--
-- Contraintes pour la table `movies-makers`
--
ALTER TABLE `movies-makers`
  ADD CONSTRAINT `movies@002dmakers_ibfk_3` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE,
  ADD CONSTRAINT `movies@002dmakers_ibfk_4` FOREIGN KEY (`id_person`) REFERENCES `persons` (`id_person`) ON DELETE CASCADE;

--
-- Contraintes pour la table `movies-shortlists`
--
ALTER TABLE `movies-shortlists`
  ADD CONSTRAINT `movies@002dshortlists_ibfk_3` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE,
  ADD CONSTRAINT `movies@002dshortlists_ibfk_4` FOREIGN KEY (`id_shortlist`) REFERENCES `shortlists` (`id_shortlist`) ON DELETE CASCADE;

--
-- Contraintes pour la table `roles-permissions`
--
ALTER TABLE `roles-permissions`
  ADD CONSTRAINT `roles-permissions_ibfk_1` FOREIGN KEY (`role`) REFERENCES `roles` (`role`),
  ADD CONSTRAINT `roles-permissions_ibfk_2` FOREIGN KEY (`permission`) REFERENCES `permissions` (`permission`);

--
-- Contraintes pour la table `users-roles`
--
ALTER TABLE `users-roles`
  ADD CONSTRAINT `users-roles_ibfk_1` FOREIGN KEY (`login`) REFERENCES `users` (`login`),
  ADD CONSTRAINT `users-roles_ibfk_2` FOREIGN KEY (`role`) REFERENCES `roles` (`role`);
SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
