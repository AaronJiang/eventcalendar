-- phpMyAdmin SQL Dump
-- version 3.3.2deb1ubuntu1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 22, 2013 at 09:09 AM
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_title`, `event_desc`, `event_start`, `event_end`) VALUES
(2, 'Last Day of January', 'Last day of the month! Yay!', '2010-01-31 00:00:00', '2010-01-31 23:59:59'),
(4, 'Happy new year', 'new year', '2010-01-01 00:00:00', '2010-01-01 00:00:00'),
(5, 'Middle day', 'Middle of January', '2010-01-15 00:00:00', '2010-01-15 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_pass` varchar(47) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_email` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_pass`, `user_email`) VALUES
(1, 'testuser', '12393e88c2f145795988cf3808a2315d9be05d2c289d2c9', 'admin@example.com');
