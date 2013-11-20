-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mer 20 Novembre 2013 à 10:07
-- Version du serveur: 5.5.25
-- Version de PHP: 5.4.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données: `soundcloud`
--

-- --------------------------------------------------------

--
-- Structure de la table `FLikes`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `Follow`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `Likes`
--

CREATE TABLE `Likes` (
  `TSid` int(11) NOT NULL AUTO_INCREMENT,
  `TSuid` int(11) NOT NULL,
  `SCid` int(11) NOT NULL,
  `SCuid` int(11) NOT NULL,
  `Title` varchar(50) NOT NULL,
  `Artist` varchar(30) NOT NULL,
  `AddDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`TSid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `Rejected`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `Stats`
--

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

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `TopCount_view`
--
CREATE TABLE `TopCount_view` (
`SCid` int(11)
,`Title` varchar(50)
,`Artist` varchar(30)
,`Count` int(11)
);
-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `TopRatio_view`
--
CREATE TABLE `TopRatio_view` (
`SCid` int(11)
,`Ratio` decimal(14,4)
,`Title` varchar(50)
,`Artist` varchar(30)
);
-- --------------------------------------------------------

--
-- Structure de la table `Users`
--

CREATE TABLE `Users` (
  `TSuid` int(11) NOT NULL AUTO_INCREMENT,
  `SCuid` int(11) NOT NULL,
  `Username` varchar(30) NOT NULL,
  `Name` varchar(30) NOT NULL,
  `AddDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TSuid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
