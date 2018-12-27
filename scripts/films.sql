-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Client :  localhost:3306
-- Généré le :  Jeu 27 Décembre 2018 à 18:36
-- Version du serveur :  10.1.26-MariaDB-0+deb9u1
-- Version de PHP :  7.0.33-0+deb9u1

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
CREATE DATABASE IF NOT EXISTS `films` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `films`;

-- --------------------------------------------------------

--
-- Structure de la table `audiocodecs`
--

CREATE TABLE IF NOT EXISTS `audiocodecs` (
  `audiocodec` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`audiocodec`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `audiocodecs`
--

INSERT INTO `audiocodecs` (`audiocodec`) VALUES
('AAC'),
('AC3'),
('DTS'),
('FLAC'),
('MP3'),
('Vorbis'),
('WMA');

-- --------------------------------------------------------

--
-- Structure de la table `borrowers`
--

CREATE TABLE IF NOT EXISTS `borrowers` (
  `id_borrower` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `borrowername` varchar(128) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_borrower`),
  UNIQUE KEY `borrowername` (`borrowername`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `category` varchar(128) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `categories`
--

INSERT INTO `categories` (`category`) VALUES
('Drame'),
('Guerre'),
('Romantique');

-- --------------------------------------------------------

--
-- Structure de la table `containers`
--

CREATE TABLE IF NOT EXISTS `containers` (
  `container` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`container`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `containers`
--

INSERT INTO `containers` (`container`) VALUES
('AVI'),
('BluRay'),
('DVD'),
('MKV'),
('MPEG4');

-- --------------------------------------------------------

--
-- Structure de la table `experience`
--

CREATE TABLE IF NOT EXISTS `experience` (
  `id_movie` int(10) UNSIGNED NOT NULL,
  `rating` tinyint(3) UNSIGNED DEFAULT NULL,
  `lastseen` date DEFAULT NULL,
  PRIMARY KEY (`id_movie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `language` varchar(10) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `languages`
--

INSERT INTO `languages` (`language`) VALUES
('en'),
('fr');

-- --------------------------------------------------------

--
-- Structure de la table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `level` enum('info','warning','error','fatal') COLLATE utf8_bin NOT NULL DEFAULT 'info',
  `component` varchar(32) COLLATE utf8_bin NOT NULL,
  `user` varchar(255) COLLATE utf8_bin NOT NULL,
  `message` varchar(255) COLLATE utf8_bin NOT NULL,
  KEY `level` (`level`),
  KEY `component` (`component`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

CREATE TABLE IF NOT EXISTS `media` (
  `id_medium` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_movie` int(10) UNSIGNED NOT NULL,
  `container` varchar(32) COLLATE utf8_bin NOT NULL,
  `videocodec` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `audiocodec` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `height` int(10) UNSIGNED DEFAULT NULL,
  `width` int(10) UNSIGNED DEFAULT NULL,
  `comment` text COLLATE utf8_bin,
  `shelfmark` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id_medium`),
  UNIQUE KEY `shelfmark` (`shelfmark`),
  KEY `id_movie` (`id_movie`),
  KEY `videocodec` (`videocodec`),
  KEY `audiocodec` (`audiocodec`),
  KEY `container` (`container`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `media-quality`
-- (Voir ci-dessous la vue réelle)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
-- Contenu de la table `permissions`
--

INSERT INTO `permissions` (`permission`, `permdescription`) VALUES
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
  `name` varchar(1024) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_person`),
  KEY `name` (`name`(255))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `persons`
--

INSERT INTO `persons` (`id_person`, `name`) VALUES
(1820, 'Michael Curtiz'),
(1821, 'Humphrey Bogart'),
(1822, 'Ingrid Bergman');

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
-- Contenu de la table `quality`
--

INSERT INTO `quality` (`quality`, `minwidth`, `minheight`, `maxwidth`, `maxheight`) VALUES
('BluRay', 1280, 800, 1920, 1080),
('DVD', 720, 300, 1280, 800),
('Full HD', 1920, 1080, 999999, 999999),
('indéterminé', NULL, NULL, NULL, NULL),
('moyen', 640, 272, 720, 300),
('médiocre', 0, 0, 640, 272);

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
-- Contenu de la table `roles`
--

INSERT INTO `roles` (`role`, `description`) VALUES
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
-- Contenu de la table `roles-permissions`
--

INSERT INTO `roles-permissions` (`role`, `permission`) VALUES
('admin', 'admin'),
('admin', 'lastseen'),
('admin', 'rating'),
('admin', 'read'),
('admin', 'shortlists'),
('admin', 'write'),
('ro', 'read'),
('rw', 'lastseen'),
('rw', 'rating'),
('rw', 'read'),
('rw', 'shortlists'),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
-- Contenu de la table `users`
--

INSERT INTO `users` (`login`, `email`, `password`) VALUES
('admin', NULL, '$2y$10$XTOHjbXWky4JHVUaanvWLuJfNvV58IRd1bUuGQp3XicPgJQmJSNDe');

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
-- Contenu de la table `users-roles`
--

INSERT INTO `users-roles` (`login`, `role`) VALUES
('admin', 'admin');

-- --------------------------------------------------------

--
-- Structure de la table `videocodecs`
--

CREATE TABLE IF NOT EXISTS `videocodecs` (
  `videocodec` varchar(32) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`videocodec`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Contenu de la table `videocodecs`
--

INSERT INTO `videocodecs` (`videocodec`) VALUES
('DivX'),
('H.261'),
('H.262'),
('H.263'),
('H.264'),
('H.265'),
('MPEG4/2'),
('Xvid');

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
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE,
  ADD CONSTRAINT `media_ibfk_2` FOREIGN KEY (`videocodec`) REFERENCES `videocodecs` (`videocodec`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `media_ibfk_3` FOREIGN KEY (`audiocodec`) REFERENCES `audiocodecs` (`audiocodec`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `media_ibfk_4` FOREIGN KEY (`container`) REFERENCES `containers` (`container`) ON UPDATE CASCADE;

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
