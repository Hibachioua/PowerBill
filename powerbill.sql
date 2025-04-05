-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 03, 2025 at 01:57 AM
-- Server version: 8.0.36
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `powerbill2`
--

-- --------------------------------------------------------

--
-- Table structure for table `agent`
--

DROP TABLE IF EXISTS `agent`;
CREATE TABLE IF NOT EXISTS `agent` (
  `ID_Agent` int NOT NULL,
  `ID_Utilisateur` int NOT NULL,
  `Téléphone` varchar(50) NOT NULL,
  PRIMARY KEY (`ID_Agent`),
  KEY `ID_Utilisateur` (`ID_Utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `ID_Client` int NOT NULL AUTO_INCREMENT,
  `ID_Utilisateur` int NOT NULL,
  `CIN` varchar(20) NOT NULL,
  `Adresse` text NOT NULL,
  `Nom` varchar(100) NOT NULL,
  `Prenom` varchar(100) NOT NULL,
  PRIMARY KEY (`ID_Client`),
  UNIQUE KEY `CIN` (`CIN`),
  KEY `ID_Utilisateur` (`ID_Utilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`ID_Client`, `ID_Utilisateur`, `CIN`, `Adresse`, `Nom`, `Prenom`) VALUES
(1, 1, 'AB123456', '15 Rue des Lilas, Casablanca', 'Dupont', 'Jean'),
(2, 2, 'CD789012', '28 Avenue Mohammed V, Rabat', 'Martin', 'Sophie');

-- --------------------------------------------------------

--
-- Table structure for table `compteur`
--

DROP TABLE IF EXISTS `compteur`;
CREATE TABLE IF NOT EXISTS `compteur` (
  `ID_Compteur` int NOT NULL AUTO_INCREMENT,
  `ID_Client` int NOT NULL,
  PRIMARY KEY (`ID_Compteur`),
  KEY `ID_Client` (`ID_Client`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `compteur`
--

INSERT INTO `compteur` (`ID_Compteur`, `ID_Client`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `consommation`
--

DROP TABLE IF EXISTS `consommation`;
CREATE TABLE IF NOT EXISTS `consommation` (
  `ID_Consommation` int NOT NULL AUTO_INCREMENT,
  `ID_Compteur` int NOT NULL,
  `Mois` int NOT NULL,
  `Annee` int NOT NULL,
  `Qté_consommé` decimal(10,2) NOT NULL,
  `Image_Compteur` varchar(255) NOT NULL,
  `status` enum('anomalie','pas d''anomalie') DEFAULT 'pas d''anomalie',
  PRIMARY KEY (`ID_Consommation`),
  KEY `ID_Compteur` (`ID_Compteur`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `consommation`
--

INSERT INTO `consommation` (`ID_Consommation`, `ID_Compteur`, `Mois`, `Annee`, `Qté_consommé`, `Image_Compteur`, `status`) VALUES
(1, 1, 1, 2025, 140.00, 'image1_2025.png', 'pas d\'anomalie'),
(2, 1, 2, 2025, 160.00, 'image2_2025.png', 'pas d\'anomalie'),
(3, 1, 4, 2025, 160.00, 'uploads/compteurs/compteur_1_4_2025_67edbf0986c46.png', 'pas d\'anomalie'),
(4, 1, 4, 2025, 160.00, 'uploads/compteurs/compteur_1_4_2025_67edc40df1be0.png', 'pas d\'anomalie'),
(5, 1, 3, 2025, 170.00, 'uploads/compteurs/compteur_1_4_2025_67edd493b1218.png', 'pas d\'anomalie'),
(6, 1, 4, 2025, 165.00, 'uploads/compteurs/compteur_1_4_2025_67edd4dd505f6.png', 'anomalie');

-- --------------------------------------------------------

--
-- Table structure for table `facture`
--

DROP TABLE IF EXISTS `facture`;
CREATE TABLE IF NOT EXISTS `facture` (
  `ID_Facture` int NOT NULL AUTO_INCREMENT,
  `ID_Compteur` int NOT NULL,
  `ID_Consommation` int NOT NULL,
  `Date_émission` date NOT NULL,
  `Mois` int NOT NULL,
  `Annee` int NOT NULL,
  `Prix_HT` decimal(10,2) NOT NULL,
  `Prix_TTC` decimal(10,2) NOT NULL,
  `Statut_paiement` enum('paye','non paye') DEFAULT NULL,
  PRIMARY KEY (`ID_Facture`),
  KEY `ID_Compteur` (`ID_Compteur`),
  KEY `ID_Consommation` (`ID_Consommation`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `facture`
--

INSERT INTO `facture` (`ID_Facture`, `ID_Compteur`, `ID_Consommation`, `Date_émission`, `Mois`, `Annee`, `Prix_HT`, `Prix_TTC`, `Statut_paiement`) VALUES
(1, 1, 1, '2025-02-01', 2, 2025, 128.80, 151.90, 'non paye'),
(2, 1, 2, '2025-03-01', 2, 2025, 110.40, 130.27, 'non paye'),
(5, 1, 3, '2025-04-02', 4, 2025, 139.00, 164.02, 'non paye'),
(6, 1, 4, '2025-04-02', 4, 2025, 139.00, 164.02, 'non paye'),
(7, 1, 5, '2025-04-03', 4, 2025, 150.00, 177.00, 'non paye'),
(8, 1, 6, '2025-04-03', 4, 2025, 144.50, 170.51, 'non paye');

-- --------------------------------------------------------

--
-- Table structure for table `fichier_consommation`
--

DROP TABLE IF EXISTS `fichier_consommation`;
CREATE TABLE IF NOT EXISTS `fichier_consommation` (
  `ID_Fichier` int NOT NULL,
  `ID_Client` int NOT NULL,
  `ID_Agent` int NOT NULL,
  `Consommation` decimal(10,2) NOT NULL,
  `Annee` int NOT NULL,
  `Date_creation` date NOT NULL,
  `Chemin_Fichier` varchar(255) NOT NULL,
  PRIMARY KEY (`ID_Fichier`),
  KEY `ID_Client` (`ID_Client`),
  KEY `ID_Agent` (`ID_Agent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fournisseur`
--

DROP TABLE IF EXISTS `fournisseur`;
CREATE TABLE IF NOT EXISTS `fournisseur` (
  `ID_Fournisseur` int NOT NULL AUTO_INCREMENT,
  `Nom` varchar(255) NOT NULL,
  `ID_Utilisateur` int NOT NULL,
  PRIMARY KEY (`ID_Fournisseur`),
  KEY `ID_Utilisateur` (`ID_Utilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `fournisseur`
--

INSERT INTO `fournisseur` (`ID_Fournisseur`, `Nom`, `ID_Utilisateur`) VALUES
(1, 'Électricité Plus', 3);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `ID_Role` int NOT NULL,
  `Nom_Role` varchar(255) NOT NULL,
  PRIMARY KEY (`ID_Role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`ID_Role`, `Nom_Role`) VALUES
(1, 'Client'),
(2, 'Agent'),
(3, 'Fournisseur');

-- --------------------------------------------------------

--
-- Table structure for table `réclamation`
--

DROP TABLE IF EXISTS `réclamation`;
CREATE TABLE IF NOT EXISTS `réclamation` (
  `ID_Réclamation` int NOT NULL AUTO_INCREMENT,
  `ID_Client` int NOT NULL,
  `Type_Réclamation` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Date_Réclamation` date NOT NULL,
  `Statut` enum('En cours','Traité') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'En cours',
  `Réponse_Fournisseur` text,
  PRIMARY KEY (`ID_Réclamation`),
  KEY `ID_Client` (`ID_Client`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `réclamation`
--

INSERT INTO `réclamation` (`ID_Réclamation`, `ID_Client`, `Type_Réclamation`, `Description`, `Date_Réclamation`, `Statut`, `Réponse_Fournisseur`) VALUES
(1, 1, 'Fuite interne', 'll', '2025-03-25', 'En cours', NULL),
(2, 2, 'Fuite interne', 'll', '2025-03-25', 'En cours', NULL),
(4, 2, 'Fuite interne', 'll', '2025-03-25', 'En cours', NULL),
(5, 1, 'Facture', 'hh', '2025-03-25', 'En cours', NULL),
(6, 2, 'Fuite interne', 'kkoj', '2025-03-25', 'En cours', NULL),
(7, 1, 'Facture', 'fdj ff', '2025-03-26', 'En cours', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `ID_Utilisateur` int NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Mot_de_passe` varchar(255) NOT NULL,
  `ID_Role` int NOT NULL,
  PRIMARY KEY (`ID_Utilisateur`),
  UNIQUE KEY `Email` (`Email`),
  KEY `ID_Role` (`ID_Role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`ID_Utilisateur`, `Email`, `Mot_de_passe`, `ID_Role`) VALUES
(1, 'douae@example.com', 'password123', 1),
(2, 'lina@example.com', 'password456', 2),
(3, 'nada@example.com', 'password789', 3);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agent`
--
ALTER TABLE `agent`
  ADD CONSTRAINT `agent_ibfk_1` FOREIGN KEY (`ID_Utilisateur`) REFERENCES `utilisateur` (`ID_Utilisateur`);

--
-- Constraints for table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `client_ibfk_1` FOREIGN KEY (`ID_Utilisateur`) REFERENCES `utilisateur` (`ID_Utilisateur`) ON DELETE CASCADE;

--
-- Constraints for table `consommation`
--
ALTER TABLE `consommation`
  ADD CONSTRAINT `consommation_ibfk_1` FOREIGN KEY (`ID_Compteur`) REFERENCES `compteur` (`ID_Compteur`);

--
-- Constraints for table `facture`
--
ALTER TABLE `facture`
  ADD CONSTRAINT `facture_ibfk_1` FOREIGN KEY (`ID_Compteur`) REFERENCES `compteur` (`ID_Compteur`),
  ADD CONSTRAINT `facture_ibfk_2` FOREIGN KEY (`ID_Consommation`) REFERENCES `consommation` (`ID_Consommation`) ON DELETE CASCADE;

--
-- Constraints for table `fichier_consommation`
--
ALTER TABLE `fichier_consommation`
  ADD CONSTRAINT `fichier_consommation_ibfk_2` FOREIGN KEY (`ID_Agent`) REFERENCES `agent` (`ID_Agent`);

--
-- Constraints for table `fournisseur`
--
ALTER TABLE `fournisseur`
  ADD CONSTRAINT `fournisseur_ibfk_1` FOREIGN KEY (`ID_Utilisateur`) REFERENCES `utilisateur` (`ID_Utilisateur`);

--
-- Constraints for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`ID_Role`) REFERENCES `role` (`ID_Role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
