-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 02, 2025 at 08:46 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `powerbill`
--

-- --------------------------------------------------------

--
-- Table structure for table `agent`
--

CREATE TABLE `agent` (
  `ID_Agent` int(11) NOT NULL,
  `ID_Utilisateur` int(11) NOT NULL,
  `Téléphone` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `ID_Client` int(11) NOT NULL,
  `ID_Utilisateur` int(11) NOT NULL,
  `CIN` varchar(50) NOT NULL,
  `Adresse` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`ID_Client`, `ID_Utilisateur`, `CIN`, `Adresse`) VALUES
(1, 3, 'L123456', '123 Rue des Fleurs, Tétouan'),
(2, 2, 'l9999', 'llllll');

-- --------------------------------------------------------

--
-- Table structure for table `compteur`
--

CREATE TABLE `compteur` (
  `ID_Compteur` int(11) NOT NULL,
  `ID_Client` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `compteur`
--

INSERT INTO `compteur` (`ID_Compteur`, `ID_Client`) VALUES
(1, 1),
(2, 1),
(3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `consommation`
--

CREATE TABLE `consommation` (
  `ID_Consommation` int(11) NOT NULL,
  `ID_Compteur` int(11) NOT NULL,
  `Mois` int(11) NOT NULL,
  `Annee` int(11) NOT NULL,
  `Qté_consommé` decimal(10,2) NOT NULL,
  `Image_Compteur` varchar(255) DEFAULT NULL,
  `status` enum('anomalie','pas d''anomalie') DEFAULT 'pas d''anomalie'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consommation`
--

INSERT INTO `consommation` (`ID_Consommation`, `ID_Compteur`, `Mois`, `Annee`, `Qté_consommé`, `Image_Compteur`, `status`) VALUES
(422, 1, 4, 2025, 123.00, 'uploads/compteurs/compteur_1_4_2025_67ed598f4bc38.png', NULL),
(423, 1, 4, 2025, 1234.00, 'uploads/compteurs/compteur_1_4_2025_67ed853ca740f.png', 'anomalie');

-- --------------------------------------------------------

--
-- Table structure for table `facture`
--

CREATE TABLE `facture` (
  `ID_Facture` int(11) NOT NULL,
  `ID_Compteur` int(11) NOT NULL,
  `ID_Consommation` int(11) NOT NULL,
  `Date_émission` date NOT NULL,
  `Mois` int(11) NOT NULL,
  `Annee` int(11) NOT NULL,
  `Prix_HT` decimal(10,2) NOT NULL,
  `Prix_TTC` decimal(10,2) NOT NULL,
  `Statut_paiement` enum('paye','non paye') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facture`
--

INSERT INTO `facture` (`ID_Facture`, `ID_Compteur`, `ID_Consommation`, `Date_émission`, `Mois`, `Annee`, `Prix_HT`, `Prix_TTC`, `Statut_paiement`) VALUES
(1, 1, 1, '2025-02-01', 2, 2025, 128.80, 151.90, 'paye'),
(2, 1, 2, '2025-03-01', 3, 2025, 110.40, 130.27, 'non paye');

-- --------------------------------------------------------

--
-- Table structure for table `fichier_consommation`
--

CREATE TABLE `fichier_consommation` (
  `ID_Fichier` int(11) NOT NULL,
  `ID_Client` int(11) NOT NULL,
  `ID_Agent` int(11) NOT NULL,
  `Consommation` decimal(10,2) NOT NULL,
  `Annee` int(11) NOT NULL,
  `Date_creation` date NOT NULL,
  `Chemin_Fichier` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fournisseur`
--

CREATE TABLE `fournisseur` (
  `ID_Fournisseur` int(11) NOT NULL,
  `ID_Utilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `ID_Role` int(11) NOT NULL,
  `Nom_Role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`ID_Role`, `Nom_Role`) VALUES
(1, 'Fournisseur'),
(2, 'Agent'),
(3, 'Client');

-- --------------------------------------------------------

--
-- Table structure for table `réclamation`
--

CREATE TABLE `réclamation` (
  `ID_Réclamation` int(11) NOT NULL,
  `ID_Client` int(11) NOT NULL,
  `Type_Réclamation` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Date_Réclamation` date NOT NULL,
  `Statut` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `ID_Utilisateur` int(11) NOT NULL,
  `ID_Role` int(11) NOT NULL,
  `Nom` varchar(255) NOT NULL,
  `Prénom` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Mot_de_passe` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`ID_Utilisateur`, `ID_Role`, `Nom`, `Prénom`, `Email`, `Mot_de_passe`) VALUES
(1, 1, 'Aazibou', 'Douae', 'douae@example.com', 'password123'),
(2, 2, 'Ait brahim', 'Lina', 'lina@example.com', 'password456'),
(3, 1, 'Elbjioui', 'Nada', 'nada@example.com', 'password789');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agent`
--
ALTER TABLE `agent`
  ADD PRIMARY KEY (`ID_Agent`),
  ADD KEY `ID_Utilisateur` (`ID_Utilisateur`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`ID_Client`),
  ADD UNIQUE KEY `CIN` (`CIN`),
  ADD KEY `ID_Utilisateur` (`ID_Utilisateur`);

--
-- Indexes for table `compteur`
--
ALTER TABLE `compteur`
  ADD PRIMARY KEY (`ID_Compteur`),
  ADD KEY `ID_Client` (`ID_Client`);

--
-- Indexes for table `consommation`
--
ALTER TABLE `consommation`
  ADD PRIMARY KEY (`ID_Consommation`),
  ADD KEY `ID_Compteur` (`ID_Compteur`);

--
-- Indexes for table `facture`
--
ALTER TABLE `facture`
  ADD PRIMARY KEY (`ID_Facture`),
  ADD KEY `ID_Compteur` (`ID_Compteur`),
  ADD KEY `ID_Consommation` (`ID_Consommation`);

--
-- Indexes for table `fichier_consommation`
--
ALTER TABLE `fichier_consommation`
  ADD PRIMARY KEY (`ID_Fichier`),
  ADD KEY `ID_Client` (`ID_Client`),
  ADD KEY `ID_Agent` (`ID_Agent`);

--
-- Indexes for table `fournisseur`
--
ALTER TABLE `fournisseur`
  ADD PRIMARY KEY (`ID_Fournisseur`),
  ADD KEY `ID_Utilisateur` (`ID_Utilisateur`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`ID_Role`);

--
-- Indexes for table `réclamation`
--
ALTER TABLE `réclamation`
  ADD PRIMARY KEY (`ID_Réclamation`),
  ADD KEY `ID_Client` (`ID_Client`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`ID_Utilisateur`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `ID_Role` (`ID_Role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `consommation`
--
ALTER TABLE `consommation`
  MODIFY `ID_Consommation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=424;

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
  ADD CONSTRAINT `client_ibfk_1` FOREIGN KEY (`ID_Utilisateur`) REFERENCES `utilisateur` (`ID_Utilisateur`);

--
-- Constraints for table `compteur`
--
ALTER TABLE `compteur`
  ADD CONSTRAINT `compteur_ibfk_1` FOREIGN KEY (`ID_Client`) REFERENCES `client` (`ID_Client`);

--
-- Constraints for table `consommation`
--
ALTER TABLE `consommation`
  ADD CONSTRAINT `consommation_ibfk_1` FOREIGN KEY (`ID_Compteur`) REFERENCES `compteur` (`ID_Compteur`);

--
-- Constraints for table `facture`
--
ALTER TABLE `facture`
  ADD CONSTRAINT `facture_ibfk_1` FOREIGN KEY (`ID_Compteur`) REFERENCES `compteur` (`ID_Compteur`);

--
-- Constraints for table `fichier_consommation`
--
ALTER TABLE `fichier_consommation`
  ADD CONSTRAINT `fichier_consommation_ibfk_1` FOREIGN KEY (`ID_Client`) REFERENCES `client` (`ID_Client`),
  ADD CONSTRAINT `fichier_consommation_ibfk_2` FOREIGN KEY (`ID_Agent`) REFERENCES `agent` (`ID_Agent`);

--
-- Constraints for table `fournisseur`
--
ALTER TABLE `fournisseur`
  ADD CONSTRAINT `fournisseur_ibfk_1` FOREIGN KEY (`ID_Utilisateur`) REFERENCES `utilisateur` (`ID_Utilisateur`);

--
-- Constraints for table `réclamation`
--
ALTER TABLE `réclamation`
  ADD CONSTRAINT `réclamation_ibfk_1` FOREIGN KEY (`ID_Client`) REFERENCES `client` (`ID_Client`);

--
-- Constraints for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `utilisateur_ibfk_1` FOREIGN KEY (`ID_Role`) REFERENCES `role` (`ID_Role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
