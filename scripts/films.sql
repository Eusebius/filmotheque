-- phpMyAdmin SQL Dump
-- version 4.0.6deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Jeu 31 Octobre 2013 à 14:32
-- Version du serveur: 5.5.33-1
-- Version de PHP: 5.4.4-15.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `films`
--

-- --------------------------------------------------------

--
-- Structure de la table `borrowers`
--
-- Création: Jeu 17 Octobre 2013 à 12:18
--

DROP TABLE IF EXISTS `borrowers`;
CREATE TABLE IF NOT EXISTS `borrowers` (
  `id_borrower` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `borrowername` varchar(128) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_borrower`),
  UNIQUE KEY `borrowername` (`borrowername`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--
-- Création: Jeu 17 Octobre 2013 à 12:18
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `category` varchar(128) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `experience`
--
-- Création: Jeu 31 Octobre 2013 à 13:27
--

DROP TABLE IF EXISTS `experience`;
CREATE TABLE IF NOT EXISTS `experience` (
  `id_movie` int(10) unsigned NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL,
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
-- Création: Jeu 17 Octobre 2013 à 12:18
--

DROP TABLE IF EXISTS `languages`;
CREATE TABLE IF NOT EXISTS `languages` (
  `language` varchar(10) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `media`
--
-- Création: Jeu 31 Octobre 2013 à 13:28
--

DROP TABLE IF EXISTS `media`;
CREATE TABLE IF NOT EXISTS `media` (
  `id_medium` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_movie` int(10) unsigned NOT NULL,
  `type` varchar(128) COLLATE utf8_bin NOT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `width` int(10) unsigned DEFAULT NULL,
  `comment` text COLLATE utf8_bin,
  `shelfmark` varchar(20) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id_medium`),
  UNIQUE KEY `shelfmark` (`shelfmark`),
  KEY `id_movie` (`id_movie`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=879 ;

--
-- RELATIONS POUR LA TABLE `media`:
--   `id_movie`
--       `movies` -> `id_movie`
--

-- --------------------------------------------------------

--
-- Structure de la table `media-audio`
--
-- Création: Jeu 31 Octobre 2013 à 13:28
--

DROP TABLE IF EXISTS `media-audio`;
CREATE TABLE IF NOT EXISTS `media-audio` (
  `id_medium` int(10) unsigned NOT NULL,
  `language` varchar(10) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_medium`,`language`),
  KEY `language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `media-audio`:
--   `language`
--       `languages` -> `language`
--   `id_medium`
--       `media` -> `id_medium`
--

-- --------------------------------------------------------

--
-- Structure de la table `media-borrowers`
--
-- Création: Jeu 31 Octobre 2013 à 13:26
--

DROP TABLE IF EXISTS `media-borrowers`;
CREATE TABLE IF NOT EXISTS `media-borrowers` (
  `id_medium` int(10) unsigned NOT NULL,
  `id_borrower` int(10) unsigned NOT NULL,
  `loandate` date NOT NULL DEFAULT '0000-00-00',
  `backdate` date DEFAULT NULL,
  PRIMARY KEY (`id_medium`,`id_borrower`,`loandate`),
  KEY `id_borrower` (`id_borrower`),
  KEY `id_medium` (`id_medium`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `media-borrowers`:
--   `id_borrower`
--       `borrowers` -> `id_borrower`
--   `id_medium`
--       `media` -> `id_medium`
--

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `media-quality`
--
DROP VIEW IF EXISTS `media-quality`;
CREATE TABLE IF NOT EXISTS `media-quality` (
`id_medium` int(10) unsigned
,`quality` varchar(32)
);
-- --------------------------------------------------------

--
-- Structure de la table `media-subs`
--
-- Création: Jeu 31 Octobre 2013 à 13:28
--

DROP TABLE IF EXISTS `media-subs`;
CREATE TABLE IF NOT EXISTS `media-subs` (
  `id_medium` int(10) unsigned NOT NULL,
  `language` varchar(10) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_medium`,`language`),
  KEY `language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `media-subs`:
--   `language`
--       `languages` -> `language`
--   `id_medium`
--       `media` -> `id_medium`
--

-- --------------------------------------------------------

--
-- Structure de la table `movies`
--
-- Création: Jeu 17 Octobre 2013 à 12:18
--

DROP TABLE IF EXISTS `movies`;
CREATE TABLE IF NOT EXISTS `movies` (
  `id_movie` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(1024) COLLATE utf8_bin NOT NULL,
  `year` int(11) DEFAULT NULL,
  `imdb_id` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `originaltitle` varchar(1024) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id_movie`),
  UNIQUE KEY `imdb_id` (`imdb_id`),
  KEY `title` (`title`(255))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8992 ;

-- --------------------------------------------------------

--
-- Structure de la table `movies-actors`
--
-- Création: Jeu 31 Octobre 2013 à 13:28
--

DROP TABLE IF EXISTS `movies-actors`;
CREATE TABLE IF NOT EXISTS `movies-actors` (
  `id_movie` int(10) unsigned NOT NULL,
  `id_person` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_movie`,`id_person`),
  KEY `id_person` (`id_person`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `movies-actors`:
--   `id_person`
--       `persons` -> `id_person`
--   `id_movie`
--       `movies` -> `id_movie`
--

-- --------------------------------------------------------

--
-- Structure de la table `movies-categories`
--
-- Création: Jeu 31 Octobre 2013 à 13:29
--

DROP TABLE IF EXISTS `movies-categories`;
CREATE TABLE IF NOT EXISTS `movies-categories` (
  `id_movie` int(10) unsigned NOT NULL,
  `category` varchar(128) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_movie`,`category`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `movies-categories`:
--   `category`
--       `categories` -> `category`
--   `id_movie`
--       `movies` -> `id_movie`
--

-- --------------------------------------------------------

--
-- Structure de la table `movies-makers`
--
-- Création: Jeu 31 Octobre 2013 à 13:29
--

DROP TABLE IF EXISTS `movies-makers`;
CREATE TABLE IF NOT EXISTS `movies-makers` (
  `id_movie` int(10) unsigned NOT NULL,
  `id_person` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id_movie`,`id_person`),
  KEY `id_person` (`id_person`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `movies-makers`:
--   `id_person`
--       `persons` -> `id_person`
--   `id_movie`
--       `movies` -> `id_movie`
--

-- --------------------------------------------------------

--
-- Structure de la table `movies-shortlists`
--
-- Création: Jeu 31 Octobre 2013 à 13:29
--

DROP TABLE IF EXISTS `movies-shortlists`;
CREATE TABLE IF NOT EXISTS `movies-shortlists` (
  `id_movie` int(10) unsigned NOT NULL,
  `id_shortlist` int(10) unsigned NOT NULL,
  UNIQUE KEY `id_movie` (`id_movie`,`id_shortlist`),
  KEY `id_shortlist` (`id_shortlist`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- RELATIONS POUR LA TABLE `movies-shortlists`:
--   `id_shortlist`
--       `shortlists` -> `id_shortlist`
--   `id_movie`
--       `movies` -> `id_movie`
--

-- --------------------------------------------------------

--
-- Structure de la table `persons`
--
-- Création: Jeu 17 Octobre 2013 à 12:18
--

DROP TABLE IF EXISTS `persons`;
CREATE TABLE IF NOT EXISTS `persons` (
  `id_person` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `surname_afac` varchar(1024) COLLATE utf8_bin NOT NULL,
  `firstnames_afac` varchar(1024) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(1024) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_person`),
  KEY `surname` (`surname_afac`(255)),
  KEY `name` (`name`(255))
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1820 ;

-- --------------------------------------------------------

--
-- Structure de la table `quality`
--
-- Création: Jeu 17 Octobre 2013 à 12:18
--

DROP TABLE IF EXISTS `quality`;
CREATE TABLE IF NOT EXISTS `quality` (
  `quality` varchar(32) COLLATE utf8_bin NOT NULL,
  `minwidth` int(11) DEFAULT NULL,
  `minheight` int(11) DEFAULT NULL,
  `maxwidth` int(11) DEFAULT NULL,
  `maxheight` int(11) DEFAULT NULL,
  PRIMARY KEY (`quality`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `shortlists`
--
-- Création: Jeu 17 Octobre 2013 à 12:18
--

DROP TABLE IF EXISTS `shortlists`;
CREATE TABLE IF NOT EXISTS `shortlists` (
  `id_shortlist` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `listname` varchar(128) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id_shortlist`),
  UNIQUE KEY `name` (`listname`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Structure de la vue `media-quality`
--
DROP TABLE IF EXISTS `media-quality`;

CREATE ALGORITHM=UNDEFINED DEFINER=`films`@`localhost` SQL SECURITY DEFINER VIEW `media-quality` AS select `media`.`id_medium` AS `id_medium`,`quality`.`quality` AS `quality` from (`media` join `quality`) where (((`media`.`width` < `quality`.`maxwidth`) and (`media`.`height` < `quality`.`maxheight`) and ((`media`.`width` >= `quality`.`minwidth`) or (`media`.`height` >= `quality`.`minheight`))) or (isnull(`media`.`height`) and (`media`.`width` < `quality`.`maxwidth`) and (`media`.`width` >= `quality`.`minwidth`)) or (isnull(`media`.`width`) and (`media`.`height` < `quality`.`maxheight`) and (`media`.`height` >= `quality`.`minheight`)) or (isnull(`media`.`height`) and isnull(`quality`.`minheight`) and isnull(`media`.`width`) and isnull(`quality`.`minwidth`)));

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `experience`
--
ALTER TABLE `experience`
  ADD CONSTRAINT `experience_ibfk_2` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_2` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media-audio`
--
ALTER TABLE `media-audio`
  ADD CONSTRAINT `media-audio_ibfk_2` FOREIGN KEY (`language`) REFERENCES `languages` (`language`) ON DELETE CASCADE,
  ADD CONSTRAINT `media-audio_ibfk_1` FOREIGN KEY (`id_medium`) REFERENCES `media` (`id_medium`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media-borrowers`
--
ALTER TABLE `media-borrowers`
  ADD CONSTRAINT `media-borrowers_ibfk_2` FOREIGN KEY (`id_borrower`) REFERENCES `borrowers` (`id_borrower`) ON DELETE CASCADE,
  ADD CONSTRAINT `media-borrowers_ibfk_1` FOREIGN KEY (`id_medium`) REFERENCES `media` (`id_medium`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media-subs`
--
ALTER TABLE `media-subs`
  ADD CONSTRAINT `media-subs_ibfk_2` FOREIGN KEY (`language`) REFERENCES `languages` (`language`) ON DELETE CASCADE,
  ADD CONSTRAINT `media-subs_ibfk_1` FOREIGN KEY (`id_medium`) REFERENCES `media` (`id_medium`) ON DELETE CASCADE;

--
-- Contraintes pour la table `movies-actors`
--
ALTER TABLE `movies-actors`
  ADD CONSTRAINT `movies-actors_ibfk_2` FOREIGN KEY (`id_person`) REFERENCES `persons` (`id_person`) ON DELETE CASCADE,
  ADD CONSTRAINT `movies-actors_ibfk_1` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE;

--
-- Contraintes pour la table `movies-categories`
--
ALTER TABLE `movies-categories`
  ADD CONSTRAINT `movies-categories_ibfk_4` FOREIGN KEY (`category`) REFERENCES `categories` (`category`) ON DELETE CASCADE,
  ADD CONSTRAINT `movies-categories_ibfk_3` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE;

--
-- Contraintes pour la table `movies-makers`
--
ALTER TABLE `movies-makers`
  ADD CONSTRAINT `movies-makers_ibfk_2` FOREIGN KEY (`id_person`) REFERENCES `persons` (`id_person`) ON DELETE CASCADE,
  ADD CONSTRAINT `movies-makers_ibfk_1` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE;

--
-- Contraintes pour la table `movies-shortlists`
--
ALTER TABLE `movies-shortlists`
  ADD CONSTRAINT `movies-shortlists_ibfk_2` FOREIGN KEY (`id_shortlist`) REFERENCES `shortlists` (`id_shortlist`) ON DELETE CASCADE,
  ADD CONSTRAINT `movies-shortlists_ibfk_1` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
