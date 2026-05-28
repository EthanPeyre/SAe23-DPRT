-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 28, 2026 at 05:28 
-- Server version: 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sae23`
--

-- --------------------------------------------------------

--
-- Table structure for table `Administration`
--

CREATE TABLE IF NOT EXISTS `Administration` (
  `login` varchar(30) NOT NULL,
  `mdp` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `batiment`
--

CREATE TABLE IF NOT EXISTS `batiment` (
  `id_bat` varchar(30) NOT NULL,
  `nom_bat` varchar(30) NOT NULL,
  `ges_login` varchar(30) NOT NULL,
  `ges_mdp` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `capteur`
--

CREATE TABLE IF NOT EXISTS `capteur` (
  `nom_salle` varchar(30) NOT NULL,
  `nom_capt` varchar(30) NOT NULL,
  `type_capt` varchar(30) NOT NULL,
  `unite` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mesure`
--

CREATE TABLE IF NOT EXISTS `mesure` (
  `nom_capt` varchar(30) NOT NULL,
  `id_mes` int(11) NOT NULL,
  `date` date NOT NULL,
  `horaire` time NOT NULL,
  `valeur` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `salle`
--

CREATE TABLE IF NOT EXISTS `salle` (
  `id_bat` varchar(30) NOT NULL,
  `nom_salle` varchar(30) NOT NULL,
  `type_salle` varchar(30) NOT NULL,
  `capacite` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Administration`
--
ALTER TABLE `Administration`
 ADD PRIMARY KEY (`login`);

--
-- Indexes for table `batiment`
--
ALTER TABLE `batiment`
 ADD PRIMARY KEY (`id_bat`), ADD KEY `FOREIGN` (`id_bat`);

--
-- Indexes for table `capteur`
--
ALTER TABLE `capteur`
 ADD PRIMARY KEY (`nom_capt`), ADD KEY `nom_salle` (`nom_salle`);

--
-- Indexes for table `mesure`
--
ALTER TABLE `mesure`
 ADD PRIMARY KEY (`id_mes`), ADD KEY `nom_capt` (`nom_capt`);

--
-- Indexes for table `salle`
--
ALTER TABLE `salle`
 ADD PRIMARY KEY (`nom_salle`), ADD KEY `id_bat` (`id_bat`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `capteur`
--
ALTER TABLE `capteur`
ADD CONSTRAINT `capteur_ibfk_1` FOREIGN KEY (`nom_salle`) REFERENCES `salle` (`nom_salle`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mesure`
--
ALTER TABLE `mesure`
ADD CONSTRAINT `mesure_ibfk_1` FOREIGN KEY (`nom_capt`) REFERENCES `capteur` (`nom_capt`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `salle`
--
ALTER TABLE `salle`
ADD CONSTRAINT `salle_ibfk_1` FOREIGN KEY (`id_bat`) REFERENCES `batiment` (`id_bat`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
