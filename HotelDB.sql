-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 27, 2016 at 11:02 PM
-- Server version: 5.5.52-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `HotelDB`
--

-- --------------------------------------------------------

--
-- Table structure for table `Payment`
--

CREATE TABLE IF NOT EXISTS `Payment` (
  `paymentID` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `amount` double DEFAULT '0',
  PRIMARY KEY (`paymentID`),
  KEY `fk_Payment_1_idx` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Room`
--

CREATE TABLE IF NOT EXISTS `Room` (
  `roomID` int(11) NOT NULL AUTO_INCREMENT,
  `sectionID` int(11) DEFAULT NULL,
  PRIMARY KEY (`roomID`),
  KEY `fk_Room_1_idx` (`sectionID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `RoomBooking`
--

CREATE TABLE IF NOT EXISTS `RoomBooking` (
  `roomID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `fromDate` date DEFAULT NULL,
  `toDate` date DEFAULT NULL,
  PRIMARY KEY (`roomID`,`userID`),
  KEY `fk_RoomBooking_2_idx` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `RoomServices`
--

CREATE TABLE IF NOT EXISTS `RoomServices` (
  `roomID` int(11) DEFAULT NULL,
  `serviceID` int(11) DEFAULT NULL,
  KEY `fk_RoomServices_1_idx` (`roomID`),
  KEY `fk_RoomServices_2_idx` (`serviceID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Section`
--

CREATE TABLE IF NOT EXISTS `Section` (
  `sectionID` int(11) NOT NULL AUTO_INCREMENT,
  `sectionName` varchar(100) DEFAULT NULL,
  `sectionDescription` text,
  `sectionRate` int(11) DEFAULT NULL,
  PRIMARY KEY (`sectionID`),
  UNIQUE KEY `sectionName_UNIQUE` (`sectionName`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sectionImages`
--

CREATE TABLE IF NOT EXISTS `sectionImages` (
  `sectionID` int(11) DEFAULT NULL,
  `sectionImage` varchar(500) DEFAULT NULL,
  KEY `fk_sectionImages_1_idx` (`sectionID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Service`
--

CREATE TABLE IF NOT EXISTS `Service` (
  `serviceID` int(11) NOT NULL AUTO_INCREMENT,
  `serviceName` varchar(100) DEFAULT NULL,
  `serviceDescription` text,
  `servicePrice` double DEFAULT '0',
  PRIMARY KEY (`serviceID`),
  UNIQUE KEY `serviceName` (`serviceName`),
  KEY `serviceID` (`serviceID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `Service`
--

INSERT INTO `Service` (`serviceID`, `serviceName`, `serviceDescription`, `servicePrice`) VALUES
(5, 'aAA', 'SSSSSSSSS', 100),
(7, 'xxx', 'new desci', 200);

-- --------------------------------------------------------

--
-- Table structure for table `User`
--

CREATE TABLE IF NOT EXISTS `User` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `userFName` varchar(45) DEFAULT NULL,
  `userLName` varchar(45) DEFAULT NULL,
  `userEmail` varchar(45) DEFAULT NULL,
  `userPassword` varchar(45) DEFAULT NULL,
  `userType` varchar(45) DEFAULT NULL,
  `Usercol` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Payment`
--
ALTER TABLE `Payment`
  ADD CONSTRAINT `fk_Payment_1` FOREIGN KEY (`userID`) REFERENCES `User` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Room`
--
ALTER TABLE `Room`
  ADD CONSTRAINT `fk_Room_1` FOREIGN KEY (`sectionID`) REFERENCES `Section` (`sectionID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `RoomBooking`
--
ALTER TABLE `RoomBooking`
  ADD CONSTRAINT `fk_RoomBooking_1` FOREIGN KEY (`roomID`) REFERENCES `Room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_RoomBooking_2` FOREIGN KEY (`userID`) REFERENCES `User` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `RoomServices`
--
ALTER TABLE `RoomServices`
  ADD CONSTRAINT `fk_RoomServices_1` FOREIGN KEY (`roomID`) REFERENCES `Room` (`roomID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_RoomServices_2` FOREIGN KEY (`serviceID`) REFERENCES `Service` (`serviceID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sectionImages`
--
ALTER TABLE `sectionImages`
  ADD CONSTRAINT `fk_sectionImages_1` FOREIGN KEY (`sectionID`) REFERENCES `Section` (`sectionID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
