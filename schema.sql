# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.1.59)
# Datenbank: pipinstrasse
# Erstellungsdauer: 2013-06-08 19:17:57 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Export von Tabelle board
# ------------------------------------------------------------

CREATE TABLE `board` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `von` int(11) NOT NULL,
  `nachricht` text CHARACTER SET latin1 NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle chat
# ------------------------------------------------------------

CREATE TABLE `chat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `von` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `created` (`created`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle comments
# ------------------------------------------------------------

CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `object_type` varchar(20) NOT NULL,
  `object_id` int(11) DEFAULT NULL,
  `von` int(11) NOT NULL,
  `comment` text,
  `liked` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `object` (`object_type`,`object_id`),
  KEY `von` (`von`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle invitations
# ------------------------------------------------------------

CREATE TABLE `invitations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `von` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `object_type` varchar(20) NOT NULL,
  `object_id` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle messages
# ------------------------------------------------------------

CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `von` int(11) NOT NULL,
  `an` int(11) NOT NULL,
  `gift` varchar(20) CHARACTER SET latin1 NOT NULL,
  `nachricht` text CHARACTER SET latin1 NOT NULL,
  `created` datetime NOT NULL,
  `viewed` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `last_id` int(11) NOT NULL,
  `updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;

INSERT INTO `migrations` (`last_id`, `updated`)
VALUES
	(11,'2013-06-08 19:55:37');

/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;


# Export von Tabelle pages
# ------------------------------------------------------------

CREATE TABLE `pages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(100) NOT NULL DEFAULT '',
  `public` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(100) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle photos
# ------------------------------------------------------------

CREATE TABLE `photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `topic_id` int(11) DEFAULT NULL,
  `von` int(11) NOT NULL,
  `seq` int(11) NOT NULL DEFAULT '0',
  `title` varchar(100) CHARACTER SET latin1 NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `topic_id` (`topic_id`,`seq`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle stream
# ------------------------------------------------------------

CREATE TABLE `stream` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `von` int(11) NOT NULL,
  `object_type` varchar(20) CHARACTER SET latin1 NOT NULL,
  `object_id` int(11) NOT NULL,
  `title` varchar(100) CHARACTER SET latin1 DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `created` (`created`),
  KEY `von` (`von`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle topics
# ------------------------------------------------------------

CREATE TABLE `topics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `von` int(11) NOT NULL,
  `shared` tinyint(1) NOT NULL COMMENT 'Alben, in die von jedem Bilder eingestellt werden dÃ¼rfen',
  `title` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `shared` (`shared`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle users
# ------------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) CHARACTER SET latin1 NOT NULL,
  `mail` varchar(255) CHARACTER SET latin1 NOT NULL,
  `roles` varchar(100) CHARACTER SET latin1 NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 NOT NULL,
  `recover` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `vorname` varchar(255) CHARACTER SET latin1 NOT NULL,
  `nachname` varchar(255) CHARACTER SET latin1 NOT NULL,
  `hausnummer` varchar(10) CHARACTER SET latin1 NOT NULL,
  `hausnr_sort` int(11) NOT NULL DEFAULT '0',
  `bio` text CHARACTER SET latin1 NOT NULL,
  `invited_by` int(11) DEFAULT NULL,
  `invited` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `chat` datetime DEFAULT NULL,
  `online` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mail` (`mail`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `recover` (`recover`),
  KEY `online` (`online`),
  KEY `chat` (`chat`),
  KEY `hausnr_sort` (`hausnr_sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
