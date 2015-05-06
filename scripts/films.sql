-- phpMyAdmin SQL Dump
-- version 4.2.12deb2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Mer 06 Mai 2015 à 06:45
-- Version du serveur :  5.5.42-1
-- Version de PHP :  5.6.7-1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `films`
--

-- --------------------------------------------------------

--
-- Structure de la table `borrowers`
--

CREATE TABLE IF NOT EXISTS `borrowers` (
`id_borrower` int(10) unsigned NOT NULL,
  `borrowername` varchar(128) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `category` varchar(128) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `experience`
--

CREATE TABLE IF NOT EXISTS `experience` (
  `id_movie` int(10) unsigned NOT NULL,
  `rating` tinyint(3) unsigned NOT NULL,
  `lastseen` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `languages`
--

CREATE TABLE IF NOT EXISTS `languages` (
  `language` varchar(10) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

CREATE TABLE IF NOT EXISTS `media` (
`id_medium` int(10) unsigned NOT NULL,
  `id_movie` int(10) unsigned NOT NULL,
  `type` varchar(128) COLLATE utf8_bin NOT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `width` int(10) unsigned DEFAULT NULL,
  `comment` text COLLATE utf8_bin,
  `shelfmark` varchar(20) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=876 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `media-audio`
--

CREATE TABLE IF NOT EXISTS `media-audio` (
  `id_medium` int(10) unsigned NOT NULL,
  `language` varchar(10) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `media-borrowers`
--

CREATE TABLE IF NOT EXISTS `media-borrowers` (
  `id_medium` int(10) unsigned NOT NULL,
  `id_borrower` int(10) unsigned NOT NULL,
  `loandate` date NOT NULL DEFAULT '0000-00-00',
  `backdate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

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
  `id_medium` int(10) unsigned NOT NULL,
  `language` varchar(10) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `movies`
--

CREATE TABLE IF NOT EXISTS `movies` (
`id_movie` int(10) unsigned NOT NULL,
  `title` varchar(1024) COLLATE utf8_bin NOT NULL,
  `year` int(11) DEFAULT NULL,
  `imdb_id` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `originaltitle` varchar(1024) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8992 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `movies-actors`
--

CREATE TABLE IF NOT EXISTS `movies-actors` (
  `id_movie` int(10) unsigned NOT NULL,
  `id_person` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `movies-categories`
--

CREATE TABLE IF NOT EXISTS `movies-categories` (
  `id_movie` int(10) unsigned NOT NULL,
  `category` varchar(128) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `movies-makers`
--

CREATE TABLE IF NOT EXISTS `movies-makers` (
  `id_movie` int(10) unsigned NOT NULL,
  `id_person` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `movies-shortlists`
--

CREATE TABLE IF NOT EXISTS `movies-shortlists` (
  `id_movie` int(10) unsigned NOT NULL,
  `id_shortlist` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `persons`
--

CREATE TABLE IF NOT EXISTS `persons` (
`id_person` int(10) unsigned NOT NULL,
  `surname_afac` varchar(1024) COLLATE utf8_bin NOT NULL,
  `firstnames_afac` varchar(1024) COLLATE utf8_bin DEFAULT NULL,
  `name` varchar(1024) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1820 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `quality`
--

CREATE TABLE IF NOT EXISTS `quality` (
  `quality` varchar(32) COLLATE utf8_bin NOT NULL,
  `minwidth` int(11) DEFAULT NULL,
  `minheight` int(11) DEFAULT NULL,
  `maxwidth` int(11) DEFAULT NULL,
  `maxheight` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la table `shortlists`
--

CREATE TABLE IF NOT EXISTS `shortlists` (
`id_shortlist` int(10) unsigned NOT NULL,
  `listname` varchar(128) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Structure de la vue `media-quality`
--
DROP TABLE IF EXISTS `media-quality`;

CREATE ALGORITHM=UNDEFINED DEFINER=`films`@`localhost` SQL SECURITY DEFINER VIEW `media-quality` AS select `media`.`id_medium` AS `id_medium`,`quality`.`quality` AS `quality` from (`media` join `quality`) where (((`media`.`width` < `quality`.`maxwidth`) and (`media`.`height` < `quality`.`maxheight`) and ((`media`.`width` >= `quality`.`minwidth`) or (`media`.`height` >= `quality`.`minheight`))) or (isnull(`media`.`height`) and (`media`.`width` < `quality`.`maxwidth`) and (`media`.`width` >= `quality`.`minwidth`)) or (isnull(`media`.`width`) and (`media`.`height` < `quality`.`maxheight`) and (`media`.`height` >= `quality`.`minheight`)) or (isnull(`media`.`height`) and isnull(`quality`.`minheight`) and isnull(`media`.`width`) and isnull(`quality`.`minwidth`)));

--
-- Index pour les tables exportées
--

--
-- Index pour la table `borrowers`
--
ALTER TABLE `borrowers`
 ADD PRIMARY KEY (`id_borrower`), ADD UNIQUE KEY `borrowername` (`borrowername`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
 ADD PRIMARY KEY (`category`);

--
-- Index pour la table `experience`
--
ALTER TABLE `experience`
 ADD PRIMARY KEY (`id_movie`);

--
-- Index pour la table `languages`
--
ALTER TABLE `languages`
 ADD PRIMARY KEY (`language`);

--
-- Index pour la table `media`
--
ALTER TABLE `media`
 ADD PRIMARY KEY (`id_medium`), ADD UNIQUE KEY `shelfmark` (`shelfmark`), ADD KEY `id_movie` (`id_movie`);

--
-- Index pour la table `media-audio`
--
ALTER TABLE `media-audio`
 ADD PRIMARY KEY (`id_medium`,`language`), ADD KEY `language` (`language`);

--
-- Index pour la table `media-borrowers`
--
ALTER TABLE `media-borrowers`
 ADD PRIMARY KEY (`id_medium`,`id_borrower`,`loandate`), ADD KEY `id_borrower` (`id_borrower`), ADD KEY `id_medium` (`id_medium`);

--
-- Index pour la table `media-subs`
--
ALTER TABLE `media-subs`
 ADD PRIMARY KEY (`id_medium`,`language`), ADD KEY `language` (`language`);

--
-- Index pour la table `movies`
--
ALTER TABLE `movies`
 ADD PRIMARY KEY (`id_movie`), ADD KEY `title` (`title`(255)), ADD KEY `imdb_id` (`imdb_id`);

--
-- Index pour la table `movies-actors`
--
ALTER TABLE `movies-actors`
 ADD PRIMARY KEY (`id_movie`,`id_person`), ADD KEY `id_person` (`id_person`);

--
-- Index pour la table `movies-categories`
--
ALTER TABLE `movies-categories`
 ADD PRIMARY KEY (`id_movie`,`category`), ADD KEY `category` (`category`);

--
-- Index pour la table `movies-makers`
--
ALTER TABLE `movies-makers`
 ADD PRIMARY KEY (`id_movie`,`id_person`), ADD KEY `id_person` (`id_person`);

--
-- Index pour la table `movies-shortlists`
--
ALTER TABLE `movies-shortlists`
 ADD UNIQUE KEY `id_movie` (`id_movie`,`id_shortlist`), ADD KEY `id_shortlist` (`id_shortlist`);

--
-- Index pour la table `persons`
--
ALTER TABLE `persons`
 ADD PRIMARY KEY (`id_person`), ADD KEY `surname` (`surname_afac`(255)), ADD KEY `name` (`name`(255));

--
-- Index pour la table `quality`
--
ALTER TABLE `quality`
 ADD PRIMARY KEY (`quality`);

--
-- Index pour la table `shortlists`
--
ALTER TABLE `shortlists`
 ADD PRIMARY KEY (`id_shortlist`), ADD UNIQUE KEY `name` (`listname`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `borrowers`
--
ALTER TABLE `borrowers`
MODIFY `id_borrower` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `media`
--
ALTER TABLE `media`
MODIFY `id_medium` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=876;
--
-- AUTO_INCREMENT pour la table `movies`
--
ALTER TABLE `movies`
MODIFY `id_movie` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8992;
--
-- AUTO_INCREMENT pour la table `persons`
--
ALTER TABLE `persons`
MODIFY `id_person` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1820;
--
-- AUTO_INCREMENT pour la table `shortlists`
--
ALTER TABLE `shortlists`
MODIFY `id_shortlist` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
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
ADD CONSTRAINT `media-audio_ibfk_1` FOREIGN KEY (`id_medium`) REFERENCES `media` (`id_medium`) ON DELETE CASCADE,
ADD CONSTRAINT `media-audio_ibfk_2` FOREIGN KEY (`language`) REFERENCES `languages` (`language`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media-borrowers`
--
ALTER TABLE `media-borrowers`
ADD CONSTRAINT `media-borrowers_ibfk_1` FOREIGN KEY (`id_medium`) REFERENCES `media` (`id_medium`) ON DELETE CASCADE,
ADD CONSTRAINT `media-borrowers_ibfk_2` FOREIGN KEY (`id_borrower`) REFERENCES `borrowers` (`id_borrower`) ON DELETE CASCADE;

--
-- Contraintes pour la table `media-subs`
--
ALTER TABLE `media-subs`
ADD CONSTRAINT `media-subs_ibfk_1` FOREIGN KEY (`id_medium`) REFERENCES `media` (`id_medium`) ON DELETE CASCADE,
ADD CONSTRAINT `media-subs_ibfk_2` FOREIGN KEY (`language`) REFERENCES `languages` (`language`) ON DELETE CASCADE;

--
-- Contraintes pour la table `movies-actors`
--
ALTER TABLE `movies-actors`
ADD CONSTRAINT `movies-actors_ibfk_1` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE,
ADD CONSTRAINT `movies-actors_ibfk_2` FOREIGN KEY (`id_person`) REFERENCES `persons` (`id_person`) ON DELETE CASCADE;

--
-- Contraintes pour la table `movies-categories`
--
ALTER TABLE `movies-categories`
ADD CONSTRAINT `movies-categories_ibfk_3` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE,
ADD CONSTRAINT `movies-categories_ibfk_4` FOREIGN KEY (`category`) REFERENCES `categories` (`category`) ON DELETE CASCADE;

--
-- Contraintes pour la table `movies-makers`
--
ALTER TABLE `movies-makers`
ADD CONSTRAINT `movies-makers_ibfk_1` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE,
ADD CONSTRAINT `movies-makers_ibfk_2` FOREIGN KEY (`id_person`) REFERENCES `persons` (`id_person`) ON DELETE CASCADE;

--
-- Contraintes pour la table `movies-shortlists`
--
ALTER TABLE `movies-shortlists`
ADD CONSTRAINT `movies-shortlists_ibfk_1` FOREIGN KEY (`id_movie`) REFERENCES `movies` (`id_movie`) ON DELETE CASCADE,
ADD CONSTRAINT `movies-shortlists_ibfk_2` FOREIGN KEY (`id_shortlist`) REFERENCES `shortlists` (`id_shortlist`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
