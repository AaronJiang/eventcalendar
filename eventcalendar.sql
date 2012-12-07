-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 07, 2012 at 04:37 PM
-- Server version: 5.1.62
-- PHP Version: 5.3.10-1ubuntu2ppa6~lucid

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `eventcalendar`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE IF NOT EXISTS `events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_title` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `event_desc` text COLLATE utf8_unicode_ci,
  `event_start` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `event_end` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`event_id`),
  KEY `event_start` (`event_start`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_title`, `event_desc`, `event_start`, `event_end`) VALUES
(1, 'New Year&#039;s Day', 'Happy New Year!', '2010-01-01 00:00:00', '2010-01-01 23:59:59'),
(2, 'Last Day of January', 'Last day of the month! Yay!', '2010-01-31 00:00:00', '2010-01-31 23:59:59');
