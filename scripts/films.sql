-- phpMyAdmin SQL Dump
-- version 4.2.12deb2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Sam 18 Avril 2015 à 17:12
-- Version du serveur :  5.5.42-1
-- Version de PHP :  5.6.5-2

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
-- Structure de la table `movies`
--

CREATE TABLE IF NOT EXISTS `movies` (
`id_movie` int(10) unsigned NOT NULL,
  `title` varchar(1024) COLLATE utf8_bin NOT NULL,
  `year` int(11) DEFAULT NULL,
  `imdb_id` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `originaltitle` varchar(1024) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8991 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `movies`
--
ALTER TABLE `movies`
 ADD PRIMARY KEY (`id_movie`), ADD KEY `title` (`title`(255)), ADD KEY `imdb_id` (`imdb_id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `movies`
--
ALTER TABLE `movies`
MODIFY `id_movie` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8991;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
