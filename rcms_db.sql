-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 05, 2021 at 04:57 AM
-- Server version: 5.7.31
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rcms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
CREATE TABLE IF NOT EXISTS `branches` (
  `BranchID` int(11) NOT NULL AUTO_INCREMENT,
  `BranchAddress` varchar(1000) COLLATE utf8_bin NOT NULL,
  `BranchContactNo` varchar(1000) COLLATE utf8_bin NOT NULL,
  `ManagerID` int(11) NOT NULL COMMENT 'Branch Manager''s ID',
  `BranchIsOpen` tinyint(1) NOT NULL,
  PRIMARY KEY (`BranchID`),
  UNIQUE KEY `BranchAddress` (`BranchAddress`),
  KEY `ManagerID` (`ManagerID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`BranchID`, `BranchAddress`, `BranchContactNo`, `ManagerID`, `BranchIsOpen`) VALUES
(1, 'Casimiro, Las Pinas', '828-7450', 9, 1),
(2, 'Zapote, Las Pinas', '828-7452', 10, 1),
(4, 'Alabang, Muntinlupa', '828-7453', 16, 1),
(5, 'Makati, Metro Manila', '828-71452', 18, 1),
(6, 'Pasig, Metro Manila', '1234-5678-321', 21, 1);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE IF NOT EXISTS `expenses` (
  `ExpenseID` int(11) NOT NULL AUTO_INCREMENT,
  `ExpenseName` varchar(1000) COLLATE utf8_bin NOT NULL,
  `ExpenseDateIncurred` date NOT NULL,
  `ExpenseIsDeleted` tinyint(1) NOT NULL,
  `BranchID` int(11) NOT NULL,
  `ExpenseAmount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`ExpenseID`),
  KEY `BranchID` (`BranchID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`ExpenseID`, `ExpenseName`, `ExpenseDateIncurred`, `ExpenseIsDeleted`, `BranchID`, `ExpenseAmount`) VALUES
(2, 'March - Water Bill', '2021-03-21', 0, 1, '143.44'),
(3, 'March - Meralco Bill', '2021-03-22', 0, 1, '143.10'),
(4, 'February - Telephone Bill', '2021-02-14', 0, 1, '600.00'),
(5, 'February - Meralco Bill', '2021-02-24', 0, 4, '1500.00'),
(6, 'April - Utilities Expense', '2021-04-01', 1, 1, '5.00'),
(7, 'April - Insurance', '2021-04-01', 1, 1, '1.00'),
(8, 'April - Equipment Expense', '2021-04-02', 0, 1, '800.23');

-- --------------------------------------------------------

--
-- Table structure for table `itemavailability`
--

DROP TABLE IF EXISTS `itemavailability`;
CREATE TABLE IF NOT EXISTS `itemavailability` (
  `ItemAvailabilityID` int(11) NOT NULL AUTO_INCREMENT,
  `ItemID` int(11) NOT NULL,
  `BranchID` int(11) NOT NULL,
  `ItemIsAvailable` tinyint(1) NOT NULL,
  PRIMARY KEY (`ItemAvailabilityID`),
  KEY `ItemID` (`ItemID`),
  KEY `BranchID` (`BranchID`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `itemavailability`
--

INSERT INTO `itemavailability` (`ItemAvailabilityID`, `ItemID`, `BranchID`, `ItemIsAvailable`) VALUES
(1, 1, 1, 1),
(2, 3, 1, 1),
(3, 4, 1, 1),
(4, 5, 1, 1),
(5, 5, 2, 1),
(6, 1, 4, 1),
(7, 3, 4, 1),
(8, 4, 4, 1),
(9, 5, 4, 0),
(10, 6, 1, 1),
(11, 6, 2, 1),
(12, 6, 4, 1),
(13, 7, 1, 0),
(14, 7, 2, 1),
(15, 7, 4, 1),
(16, 1, 5, 1),
(17, 3, 5, 1),
(18, 4, 5, 1),
(19, 5, 5, 1),
(20, 6, 5, 1),
(21, 7, 5, 1),
(22, 1, 6, 1),
(23, 3, 6, 1),
(24, 4, 6, 1),
(25, 5, 6, 1),
(26, 6, 6, 1),
(27, 7, 6, 1),
(28, 8, 1, 1),
(29, 8, 2, 1),
(30, 8, 4, 1),
(31, 8, 5, 1),
(32, 8, 6, 1),
(33, 9, 1, 1),
(34, 9, 2, 1),
(35, 9, 4, 1),
(36, 9, 5, 1),
(37, 9, 6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
CREATE TABLE IF NOT EXISTS `items` (
  `ItemID` int(11) NOT NULL AUTO_INCREMENT,
  `ItemName` varchar(1000) COLLATE utf8_bin NOT NULL,
  `ItemPrice` decimal(10,2) NOT NULL,
  `ItemIsDeleted` tinyint(1) NOT NULL,
  `ItemImageLocation` varchar(2048) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`ItemID`),
  UNIQUE KEY `ItemName` (`ItemName`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`ItemID`, `ItemName`, `ItemPrice`, `ItemIsDeleted`, `ItemImageLocation`) VALUES
(1, 'Sticky Baked Chicken Wings', '150.60', 0, '../img/Items/1.jpg'),
(2, 'Crabcakes with Horseradish Cream', '200.50', 1, NULL),
(3, 'Caramelized Onion Dip', '250.00', 0, '../img/Items/3.jpg'),
(4, 'Beef Stroganoff', '143.44', 0, '../img/Items/4.jpg'),
(5, 'Burmese Chicken Curry', '150.00', 0, NULL),
(6, 'Light Shrimp and Pesto Pasta', '360.50', 0, '../img/Items/6.jpg'),
(7, 'Caesar Salad', '200.50', 1, NULL),
(8, 'Lemonade', '555.55', 0, '../img/Items/8.jpg'),
(9, 'Iced Tea', '230.00', 0, '../img/Items/9.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

DROP TABLE IF EXISTS `orderitems`;
CREATE TABLE IF NOT EXISTS `orderitems` (
  `OrderItemID` int(11) NOT NULL AUTO_INCREMENT,
  `ItemID` int(11) NOT NULL,
  `OrderItemQty` int(11) NOT NULL,
  `OrderItemSubtotal` decimal(10,2) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `OrderItemIsDeleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`OrderItemID`),
  KEY `ItemID` (`ItemID`),
  KEY `OrderID` (`OrderID`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `orderitems`
--

INSERT INTO `orderitems` (`OrderItemID`, `ItemID`, `OrderItemQty`, `OrderItemSubtotal`, `OrderID`, `OrderItemIsDeleted`) VALUES
(1, 5, 1, '150.00', 1, 1),
(2, 1, 1, '100.30', 1, 0),
(3, 1, 1, '100.30', 2, 1),
(4, 3, 1, '250.00', 2, 0),
(5, 4, 1, '143.44', 2, 1),
(6, 1, 1, '100.30', 2, 0),
(7, 1, 1, '100.30', 3, 0),
(8, 3, 1, '250.00', 3, 0),
(9, 5, 2, '300.00', 4, 0),
(10, 4, 2, '286.88', 5, 0),
(11, 1, 1, '100.30', 6, 0),
(12, 6, 3, '1081.50', 7, 0),
(13, 3, 1, '250.00', 7, 0),
(14, 1, 1, '100.30', 8, 1),
(15, 3, 1, '250.00', 8, 1),
(19, 3, 3, '750.00', 13, 0),
(20, 1, 1, '100.30', 13, 0),
(21, 1, 1, '150.60', 14, 0),
(22, 8, 1, '555.55', 14, 0),
(23, 3, 1, '250.00', 15, 0),
(24, 9, 1, '230.00', 15, 0),
(25, 4, 1, '143.44', 16, 0),
(26, 5, 1, '150.00', 16, 0),
(27, 6, 3, '1081.50', 17, 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `OrderID` int(11) NOT NULL AUTO_INCREMENT,
  `OrderAddressee` varchar(1000) COLLATE utf8_bin NOT NULL,
  `WaiterID` int(11) NOT NULL,
  `OrderTotal` decimal(10,2) NOT NULL,
  `OrderDateCreated` datetime NOT NULL,
  `BranchID` int(11) NOT NULL,
  `OrderIsDeleted` tinyint(1) NOT NULL,
  `OrderIsDineIn` tinyint(1) NOT NULL,
  PRIMARY KEY (`OrderID`),
  KEY `WaiterID` (`WaiterID`),
  KEY `BranchID` (`BranchID`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`OrderID`, `OrderAddressee`, `WaiterID`, `OrderTotal`, `OrderDateCreated`, `BranchID`, `OrderIsDeleted`, `OrderIsDineIn`) VALUES
(1, 'Minatozaki Sana', 13, '100.30', '2021-03-23 02:07:43', 1, 0, 1),
(2, 'Chou Tzuyu', 13, '350.30', '2021-03-23 02:37:58', 1, 0, 0),
(3, 'Hirai Momo', 13, '350.30', '2021-03-23 11:13:58', 1, 1, 1),
(4, 'Im Nayeon', 13, '300.00', '2021-03-23 12:47:21', 1, 0, 0),
(5, 'God Jihyo', 13, '286.88', '2021-03-23 12:59:22', 1, 1, 1),
(6, 'Yoo Jeongyeon', 13, '100.30', '2021-03-23 13:12:42', 1, 1, 0),
(7, 'Kim Dahyun', 17, '1331.50', '2021-03-24 00:36:33', 4, 0, 1),
(8, 'Son Chaeyoung', 13, '0.00', '2021-03-24 08:52:42', 1, 1, 1),
(13, 'Roseanne Park', 13, '850.30', '2021-03-24 23:38:21', 1, 0, 1),
(14, 'Shin Ryu Jin', 13, '706.15', '2021-04-05 12:47:25', 1, 0, 1),
(15, 'Hwang Yeji', 13, '480.00', '2021-04-05 12:47:43', 1, 0, 1),
(16, 'Choi Jisu', 13, '293.44', '2021-04-05 12:48:01', 1, 0, 1),
(17, 'Lee Chaeryeoung', 13, '1081.50', '2021-04-05 12:48:30', 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tableorderitems`
--

DROP TABLE IF EXISTS `tableorderitems`;
CREATE TABLE IF NOT EXISTS `tableorderitems` (
  `TableOrderItemID` int(11) NOT NULL AUTO_INCREMENT,
  `TableID` int(11) DEFAULT NULL,
  `ItemID` int(11) NOT NULL,
  `RemainingQty` int(11) NOT NULL,
  `OrderID` int(11) DEFAULT NULL,
  PRIMARY KEY (`TableOrderItemID`),
  KEY `TableID` (`TableID`),
  KEY `OrderID` (`OrderID`),
  KEY `ItemID` (`ItemID`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `tableorderitems`
--

INSERT INTO `tableorderitems` (`TableOrderItemID`, `TableID`, `ItemID`, `RemainingQty`, `OrderID`) VALUES
(1, 1, 1, 0, NULL),
(2, 1, 3, 0, NULL),
(3, NULL, 5, 0, 4),
(4, 2, 4, 2, NULL),
(5, NULL, 1, 0, 6),
(6, 4, 6, 2, NULL),
(7, 4, 3, 0, NULL),
(8, 5, 1, 0, NULL),
(9, 5, 3, 0, NULL),
(12, 3, 3, 1, NULL),
(13, 3, 1, 0, NULL),
(14, 5, 1, 1, NULL),
(15, 5, 8, 1, NULL),
(16, 6, 3, 1, NULL),
(17, 6, 9, 1, NULL),
(18, 7, 4, 1, NULL),
(19, 7, 5, 1, NULL),
(20, NULL, 6, 3, 17);

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

DROP TABLE IF EXISTS `tables`;
CREATE TABLE IF NOT EXISTS `tables` (
  `TableID` int(11) NOT NULL AUTO_INCREMENT,
  `TableSeatingCapacity` int(11) NOT NULL,
  `TableIsOccupied` tinyint(1) NOT NULL COMMENT '0 - Vacant\r\n1 - Occupied',
  `TableIsDeleted` tinyint(1) NOT NULL COMMENT '0 - Not Deleted\r\n1 - Deleted',
  `BranchID` int(11) NOT NULL,
  `TableAddressee` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`TableID`),
  KEY `BranchID` (`BranchID`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`TableID`, `TableSeatingCapacity`, `TableIsOccupied`, `TableIsDeleted`, `BranchID`, `TableAddressee`) VALUES
(1, 8, 0, 1, 1, NULL),
(2, 2, 1, 1, 1, 'Lalisa Manoban'),
(3, 10, 1, 1, 1, 'Jennie Kim'),
(4, 4, 1, 0, 4, 'Kim Jisoo'),
(5, 12, 1, 0, 1, 'Shin Ryu Jin'),
(6, 4, 1, 0, 1, 'Hwang Yeji'),
(7, 5, 1, 0, 1, 'Choi Jisu'),
(8, 2, 0, 0, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `UserName` varchar(1000) COLLATE utf8_bin NOT NULL,
  `UserEmail` varchar(1000) COLLATE utf8_bin NOT NULL,
  `UserPassword` varchar(1000) COLLATE utf8_bin NOT NULL,
  `UserCellphone` varchar(1000) COLLATE utf8_bin DEFAULT NULL,
  `UserImageLocation` varchar(2048) COLLATE utf8_bin DEFAULT NULL,
  `UserIsActive` tinyint(1) NOT NULL,
  `BranchID` int(11) DEFAULT NULL,
  `UserType` int(1) NOT NULL COMMENT '1 - Owner\r\n2 - Branch Manager\r\n3 - Waiter',
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `UserEmail` (`UserEmail`),
  KEY `BranchID` (`BranchID`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `UserName`, `UserEmail`, `UserPassword`, `UserCellphone`, `UserImageLocation`, `UserIsActive`, `BranchID`, `UserType`) VALUES
(1, 'Mark Nagrampa', 'marknagrampa489@gmail.com', 'password', '0966-699-3403', '../img/Users/1.jpg', 1, NULL, 1),
(9, 'Ivana Marriam B. Saberon', 'casimiro_manager@gmail.com', 'password', '143-4444-143', '../img/Users/9.png', 1, 1, 2),
(10, 'Myoui Mina', 'zapote_manager@gmail.com', 'password', '0917-143-4445', NULL, 1, 2, 2),
(13, 'Ivana B. Saberon', 'casimiro_waiter@gmail.com', 'password', '520-520-520', '../img/Users/13.jpg', 1, 1, 3),
(14, 'Marriam B. Saberon', 'casimiro_waiter2@gmail.com', 'password', '0917-143-4447', '../img/Users/14.webp', 1, 1, 3),
(16, 'Ivana Saberon', 'alabang_manager@gmail.com', 'password', '0917-143-4448', '../img/Users/16.png', 1, 4, 2),
(17, 'Marriam Saberon', 'alabang_waiter@gmail.com', 'password', '0912-143-4444', '../img/Users/17.png', 1, 4, 3),
(18, 'Ivana', 'makati_manager@gmail.com', 'password', '0917-123-4567', '../img/Users/18.png', 1, 5, 2),
(19, 'Ivana Marriam', 'casimiro_waiter3@gmail.com', 'password', '123-456-789', '../img/Users/19.png', 1, 1, 3),
(20, 'Marriam', 'casimiro_waiter4@gmail.com', 'password', '123-456-7890', NULL, 1, 1, 3),
(21, 'Saberon', 'pasig_manager@gmail.com', 'password', '123-42123-5456', '../img/Users/21.png', 1, 6, 2);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `branches_ibfk_1` FOREIGN KEY (`ManagerID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`BranchID`) REFERENCES `branches` (`BranchID`);

--
-- Constraints for table `itemavailability`
--
ALTER TABLE `itemavailability`
  ADD CONSTRAINT `itemavailability_ibfk_1` FOREIGN KEY (`BranchID`) REFERENCES `branches` (`BranchID`),
  ADD CONSTRAINT `itemavailability_ibfk_2` FOREIGN KEY (`ItemID`) REFERENCES `items` (`ItemID`);

--
-- Constraints for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`ItemID`) REFERENCES `items` (`ItemID`),
  ADD CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`WaiterID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`BranchID`) REFERENCES `branches` (`BranchID`);

--
-- Constraints for table `tableorderitems`
--
ALTER TABLE `tableorderitems`
  ADD CONSTRAINT `tableorderitems_ibfk_1` FOREIGN KEY (`TableID`) REFERENCES `tables` (`TableID`),
  ADD CONSTRAINT `tableorderitems_ibfk_2` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`),
  ADD CONSTRAINT `tableorderitems_ibfk_3` FOREIGN KEY (`ItemID`) REFERENCES `items` (`ItemID`);

--
-- Constraints for table `tables`
--
ALTER TABLE `tables`
  ADD CONSTRAINT `tables_ibfk_1` FOREIGN KEY (`BranchID`) REFERENCES `branches` (`BranchID`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`BranchID`) REFERENCES `branches` (`BranchID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
