-- MySQL dump 10.13  Distrib 5.5.25, for osx10.6 (i386)
--
-- Host: localhost    Database: soundcloud
-- ------------------------------------------------------
-- Server version	5.5.25

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Blog`
--

DROP TABLE IF EXISTS `Blog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Blog` (
  `TSsid` int(11) NOT NULL AUTO_INCREMENT,
  `SCid` int(11) NOT NULL,
  `Blog` varchar(5) NOT NULL,
  `AddDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TSsid`)
) ENGINE=InnoDB AUTO_INCREMENT=975 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `FLikes`
--

DROP TABLE IF EXISTS `FLikes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FLikes` (
  `TSid` int(11) NOT NULL AUTO_INCREMENT,
  `TSuid` int(11) NOT NULL,
  `SCid` int(11) NOT NULL,
  `SCuid` int(11) NOT NULL,
  `Title` varchar(50) NOT NULL,
  `Artist` varchar(30) NOT NULL,
  `Count` int(11) NOT NULL,
  `FromU` varchar(300) NOT NULL,
  `Liked` int(11) NOT NULL,
  `AddDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TSid`)
) ENGINE=InnoDB AUTO_INCREMENT=9949 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Follow`
--

DROP TABLE IF EXISTS `Follow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Follow` (
  `TSfid` int(11) NOT NULL AUTO_INCREMENT,
  `TSuid` int(11) NOT NULL,
  `SCuid` int(11) NOT NULL,
  `Artist` varchar(30) NOT NULL,
  `LastLikeId` int(11) DEFAULT NULL,
  `Follower` int(11) NOT NULL,
  `StillFollowing` int(11) NOT NULL DEFAULT '1',
  `UnfollowDate` timestamp NULL DEFAULT NULL,
  `AddDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TSfid`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Likes`
--

DROP TABLE IF EXISTS `Likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Likes` (
  `TSid` int(11) NOT NULL AUTO_INCREMENT,
  `TSuid` int(11) NOT NULL,
  `SCid` int(11) NOT NULL,
  `SCuid` int(11) NOT NULL,
  `Title` varchar(50) NOT NULL,
  `Artist` varchar(30) NOT NULL,
  `AddDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`TSid`)
) ENGINE=InnoDB AUTO_INCREMENT=488 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Rejected`
--

DROP TABLE IF EXISTS `Rejected`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Rejected` (
  `TSrid` int(11) NOT NULL AUTO_INCREMENT,
  `TSuid` int(11) NOT NULL,
  `SCid` int(11) NOT NULL,
  `Title` varchar(50) NOT NULL,
  `Artist` varchar(30) NOT NULL,
  `Count` int(11) NOT NULL,
  `Liked` int(11) NOT NULL,
  `RejectedDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TSrid`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Stats`
--

DROP TABLE IF EXISTS `Stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Stats` (
  `Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `NbeFollowers` int(11) NOT NULL,
  `NbeFollowing` int(11) NOT NULL,
  `NbeNewLikes` int(11) NOT NULL,
  `NbeLikesAttente` int(11) NOT NULL,
  `NbeLikes` int(11) NOT NULL,
  `NbeRejected` int(11) NOT NULL,
  `ArtisteFavoris` int(11) NOT NULL,
  `BestRatio` float NOT NULL,
  `SCid` int(11) NOT NULL,
  `TSuid` int(11) NOT NULL,
  `Username` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary table structure for view `TopCount_view`
--

DROP TABLE IF EXISTS `TopCount_view`;
/*!50001 DROP VIEW IF EXISTS `TopCount_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `TopCount_view` (
  `SCid` int(11),
  `Title` varchar(50),
  `Artist` varchar(30),
  `Count` int(11)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `TopRatio_view`
--

DROP TABLE IF EXISTS `TopRatio_view`;
/*!50001 DROP VIEW IF EXISTS `TopRatio_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `TopRatio_view` (
  `SCid` int(11),
  `Ratio` decimal(14,4),
  `Title` varchar(50),
  `Artist` varchar(30)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `Users`
--

DROP TABLE IF EXISTS `Users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Users` (
  `TSuid` int(11) NOT NULL AUTO_INCREMENT,
  `SCuid` int(11) NOT NULL,
  `Username` varchar(30) NOT NULL,
  `Name` varchar(30) NOT NULL,
  `AddDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TSuid`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Final view structure for view `TopCount_view`
--

/*!50001 DROP TABLE IF EXISTS `TopCount_view`*/;
/*!50001 DROP VIEW IF EXISTS `TopCount_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `TopCount_view` AS select `FLikes`.`SCid` AS `SCid`,`FLikes`.`Title` AS `Title`,`FLikes`.`Artist` AS `Artist`,`FLikes`.`Count` AS `Count` from `FLikes` where ((`FLikes`.`Count` > 1) and (`FLikes`.`Liked` > 0)) order by `FLikes`.`Count` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `TopRatio_view`
--

/*!50001 DROP TABLE IF EXISTS `TopRatio_view`*/;
/*!50001 DROP VIEW IF EXISTS `TopRatio_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = latin1 */;
/*!50001 SET character_set_results     = latin1 */;
/*!50001 SET collation_connection      = latin1_swedish_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `TopRatio_view` AS select `FLikes`.`SCid` AS `SCid`,(`FLikes`.`Liked` / `FLikes`.`Count`) AS `Ratio`,`FLikes`.`Title` AS `Title`,`FLikes`.`Artist` AS `Artist` from `FLikes` where ((`FLikes`.`Liked` > 0) and (`FLikes`.`Count` > 1)) order by (`FLikes`.`Liked` / `FLikes`.`Count`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-12-02 16:32:41
