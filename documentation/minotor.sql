-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 17 juin 2025 à 10:52
-- Version du serveur : 8.0.42
-- Version de PHP : 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `minotor`
--

-- --------------------------------------------------------

--
-- Structure de la table `clean`
--

DROP TABLE IF EXISTS `clean`;
CREATE TABLE IF NOT EXISTS `clean` (
  `truck_cleaning_id` int NOT NULL,
  `truck_id` int NOT NULL,
  PRIMARY KEY (`truck_cleaning_id`,`truck_id`),
  KEY `IDX_F1B0AD491FEE3B9` (`truck_cleaning_id`),
  KEY `IDX_F1B0AD49C6957CCE` (`truck_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `company`
--

DROP TABLE IF EXISTS `company`;
CREATE TABLE IF NOT EXISTS `company` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_siret` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_contact` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `company`
--

INSERT INTO `company` (`id`, `company_name`, `company_siret`, `company_contact`) VALUES
(1, 'Boulangerie Dupont', '82374628100017', 'dupont@boulangerie.fr'),
(2, 'Minoterie du Moulin Bleu', '52438196700032', 'contact@moulinbleu.com'),
(3, 'Le Pain d’Or', '37862145900028', 'info@paindor.fr');

-- --------------------------------------------------------

--
-- Structure de la table `contains`
--

DROP TABLE IF EXISTS `contains`;
CREATE TABLE IF NOT EXISTS `contains` (
  `sales_list_id` int NOT NULL,
  `product_id` int NOT NULL,
  `product_quantity` int NOT NULL,
  `product_discount` int NOT NULL,
  PRIMARY KEY (`sales_list_id`,`product_id`),
  KEY `IDX_8EFA6A7E33576AEB` (`sales_list_id`),
  KEY `IDX_8EFA6A7E4584665A` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `contains`
--

INSERT INTO `contains` (`sales_list_id`, `product_id`, `product_quantity`, `product_discount`) VALUES
(1, 2, 10, 3);

-- --------------------------------------------------------

--
-- Structure de la table `delivery`
--

DROP TABLE IF EXISTS `delivery`;
CREATE TABLE IF NOT EXISTS `delivery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sales_list_id` int NOT NULL,
  `delivery_date` date NOT NULL,
  `delivery_address` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `delivery_status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `driver_remark` longtext COLLATE utf8mb4_unicode_ci,
  `qr_code` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_3781EC1033576AEB` (`sales_list_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `delivery`
--

INSERT INTO `delivery` (`id`, `sales_list_id`, `delivery_date`, `delivery_address`, `delivery_number`, `delivery_status`, `driver_remark`, `qr_code`) VALUES
(1, 1, '2024-06-10', '15 rue de la farine', 'DLV2024001', 'in_progress', 'Colis laissé à l\'accueil.', 'QR-6826ee9a5d3a6');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250509102603', '2025-05-15 12:07:32', 1369),
('DoctrineMigrations\\Version20250512201503', '2025-05-15 12:07:34', 35),
('DoctrineMigrations\\Version20250512201509', '2025-05-15 12:07:34', 13),
('DoctrineMigrations\\Version20250516074921', '2025-05-16 07:49:27', 162),
('DoctrineMigrations\\Version20250516082256', '2025-05-16 08:23:03', 136);

-- --------------------------------------------------------

--
-- Structure de la table `evaluate`
--

DROP TABLE IF EXISTS `evaluate`;
CREATE TABLE IF NOT EXISTS `evaluate` (
  `sales_list_id` int NOT NULL,
  `reviewer_id` int NOT NULL,
  `quote_accepted` tinyint(1) NOT NULL,
  PRIMARY KEY (`sales_list_id`,`reviewer_id`),
  KEY `IDX_8E840A8833576AEB` (`sales_list_id`),
  KEY `IDX_8E840A8870574616` (`reviewer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `invoice`
--

DROP TABLE IF EXISTS `invoice`;
CREATE TABLE IF NOT EXISTS `invoice` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sales_list_id` int DEFAULT NULL,
  `total_amount` double NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `payment_status` tinyint(1) NOT NULL,
  `acceptance_date` date NOT NULL,
  `pricing_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_9065174433576AEB` (`sales_list_id`),
  KEY `IDX_906517448864AF73` (`pricing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pricing`
--

DROP TABLE IF EXISTS `pricing`;
CREATE TABLE IF NOT EXISTS `pricing` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fixed_fee` double NOT NULL,
  `modification_date` datetime NOT NULL,
  `cost_per_km` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `warehouse_id` int NOT NULL,
  `product_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` double NOT NULL,
  `net_price` double NOT NULL,
  `gross_price` double NOT NULL,
  `unit_weight` double NOT NULL,
  `category` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stock_quantity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D34A04AD5080ECDE` (`warehouse_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `product`
--

INSERT INTO `product` (`id`, `warehouse_id`, `product_name`, `quantity`, `net_price`, `gross_price`, `unit_weight`, `category`, `stock_quantity`) VALUES
(1, 1, 'Farine complète T150', 100, 25, 30, 1, 'flour', 200),
(2, 1, 'Farine Noire', 100, 25, 30, 1, 'flour', 0),
(3, 1, 'Farine Complète', 100, 25, 30, 1, 'flour', 90);

-- --------------------------------------------------------

--
-- Structure de la table `product_supplier`
--

DROP TABLE IF EXISTS `product_supplier`;
CREATE TABLE IF NOT EXISTS `product_supplier` (
  `product_id` int NOT NULL,
  `supplier_id` int NOT NULL,
  PRIMARY KEY (`product_id`,`supplier_id`),
  KEY `IDX_509A06E94584665A` (`product_id`),
  KEY `IDX_509A06E92ADD6D8C` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `restock`
--

DROP TABLE IF EXISTS `restock`;
CREATE TABLE IF NOT EXISTS `restock` (
  `supplier_id` int NOT NULL,
  `truck_id` int NOT NULL,
  `product_id` int NOT NULL,
  `supplier_product_quantity` int NOT NULL,
  `order_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_date` date NOT NULL,
  `order_status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`supplier_id`,`truck_id`,`product_id`),
  KEY `IDX_33B621E82ADD6D8C` (`supplier_id`),
  KEY `IDX_33B621E8C6957CCE` (`truck_id`),
  KEY `IDX_33B621E84584665A` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `restock`
--

INSERT INTO `restock` (`supplier_id`, `truck_id`, `product_id`, `supplier_product_quantity`, `order_number`, `order_date`, `order_status`) VALUES
(1, 1, 1, 200, 'CMD20240607-01', '2024-06-07', 'pending');

-- --------------------------------------------------------

--
-- Structure de la table `sales_list`
--

DROP TABLE IF EXISTS `sales_list`;
CREATE TABLE IF NOT EXISTS `sales_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `products_price` double NOT NULL,
  `global_discount` int NOT NULL,
  `issue_date` datetime NOT NULL,
  `expiration_date` datetime NOT NULL,
  `order_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `sales_list`
--

INSERT INTO `sales_list` (`id`, `status`, `products_price`, `global_discount`, `issue_date`, `expiration_date`, `order_date`) VALUES
(1, 'preparing_products', 247, 10, '2024-05-15 00:00:00', '2024-06-15 00:00:00', '2024-05-15 00:00:00'),
(2, 'pending', 250.5, 10, '2024-05-15 00:00:00', '2024-06-15 00:00:00', '2024-05-15 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `supplier`
--

DROP TABLE IF EXISTS `supplier`;
CREATE TABLE IF NOT EXISTS `supplier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_address` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `supplier`
--

INSERT INTO `supplier` (`id`, `supplier_name`, `supplier_address`) VALUES
(1, 'Minoterie du Sud', '15 boulevard du Blé, 34000 Montpellier');

-- --------------------------------------------------------

--
-- Structure de la table `truck`
--

DROP TABLE IF EXISTS `truck`;
CREATE TABLE IF NOT EXISTS `truck` (
  `id` int NOT NULL AUTO_INCREMENT,
  `delivery_id` int DEFAULT NULL,
  `warehouse_id` int NOT NULL,
  `driver_id` int DEFAULT NULL,
  `registration_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `truck_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_available` tinyint(1) NOT NULL,
  `delivery_count` int NOT NULL,
  `transport_distance` double NOT NULL,
  `transport_fee` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CDCCF30A12136921` (`delivery_id`),
  KEY `IDX_CDCCF30A5080ECDE` (`warehouse_id`),
  KEY `IDX_CDCCF30AC3423909` (`driver_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `truck`
--

INSERT INTO `truck` (`id`, `delivery_id`, `warehouse_id`, `driver_id`, `registration_number`, `truck_type`, `is_available`, `delivery_count`, `transport_distance`, `transport_fee`) VALUES
(1, NULL, 1, NULL, 'AB-123-CD', 'monocuve', 1, 0, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `truck_cleaning`
--

DROP TABLE IF EXISTS `truck_cleaning`;
CREATE TABLE IF NOT EXISTS `truck_cleaning` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cleaning_start_date` date NOT NULL,
  `cleaning_end_date` date NOT NULL,
  `observations` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`),
  KEY `IDX_8D93D649979B1AD6` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `company_id`, `email`, `roles`, `password`, `last_name`, `first_name`, `role`) VALUES
(1, 1, 'jean.vendeur@example.com', '[]', '$2y$13$MtoR3.bbIW5nU/1lbUvGN.PQRujNi.9h9lm.hERDe6FRw280PQyF6', 'Vendeur', 'Jean', 'Sales');

-- --------------------------------------------------------

--
-- Structure de la table `warehouse`
--

DROP TABLE IF EXISTS `warehouse`;
CREATE TABLE IF NOT EXISTS `warehouse` (
  `id` int NOT NULL AUTO_INCREMENT,
  `warehouse_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `storage_capacity` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `warehouse`
--

INSERT INTO `warehouse` (`id`, `warehouse_address`, `storage_capacity`) VALUES
(1, '42 rue du Pain, 75000 Paris', 800);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `clean`
--
ALTER TABLE `clean`
  ADD CONSTRAINT `FK_F1B0AD491FEE3B9` FOREIGN KEY (`truck_cleaning_id`) REFERENCES `truck_cleaning` (`id`),
  ADD CONSTRAINT `FK_F1B0AD49C6957CCE` FOREIGN KEY (`truck_id`) REFERENCES `truck` (`id`);

--
-- Contraintes pour la table `contains`
--
ALTER TABLE `contains`
  ADD CONSTRAINT `FK_8EFA6A7E33576AEB` FOREIGN KEY (`sales_list_id`) REFERENCES `sales_list` (`id`),
  ADD CONSTRAINT `FK_8EFA6A7E4584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

--
-- Contraintes pour la table `delivery`
--
ALTER TABLE `delivery`
  ADD CONSTRAINT `FK_3781EC1033576AEB` FOREIGN KEY (`sales_list_id`) REFERENCES `sales_list` (`id`);

--
-- Contraintes pour la table `evaluate`
--
ALTER TABLE `evaluate`
  ADD CONSTRAINT `FK_8E840A8833576AEB` FOREIGN KEY (`sales_list_id`) REFERENCES `sales_list` (`id`),
  ADD CONSTRAINT `FK_8E840A8870574616` FOREIGN KEY (`reviewer_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `invoice`
--
ALTER TABLE `invoice`
  ADD CONSTRAINT `FK_9065174433576AEB` FOREIGN KEY (`sales_list_id`) REFERENCES `sales_list` (`id`),
  ADD CONSTRAINT `FK_906517448864AF73` FOREIGN KEY (`pricing_id`) REFERENCES `pricing` (`id`);

--
-- Contraintes pour la table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `FK_D34A04AD5080ECDE` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse` (`id`);

--
-- Contraintes pour la table `product_supplier`
--
ALTER TABLE `product_supplier`
  ADD CONSTRAINT `FK_509A06E92ADD6D8C` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`),
  ADD CONSTRAINT `FK_509A06E94584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

--
-- Contraintes pour la table `restock`
--
ALTER TABLE `restock`
  ADD CONSTRAINT `FK_33B621E82ADD6D8C` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`),
  ADD CONSTRAINT `FK_33B621E84584665A` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  ADD CONSTRAINT `FK_33B621E8C6957CCE` FOREIGN KEY (`truck_id`) REFERENCES `truck` (`id`);

--
-- Contraintes pour la table `truck`
--
ALTER TABLE `truck`
  ADD CONSTRAINT `FK_CDCCF30A12136921` FOREIGN KEY (`delivery_id`) REFERENCES `delivery` (`id`),
  ADD CONSTRAINT `FK_CDCCF30A5080ECDE` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouse` (`id`),
  ADD CONSTRAINT `FK_CDCCF30AC3423909` FOREIGN KEY (`driver_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D649979B1AD6` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
