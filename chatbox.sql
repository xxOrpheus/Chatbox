-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 09, 2014 at 11:15 PM
-- Server version: 5.6.12-log
-- PHP Version: 5.5.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `chatbox`
--
CREATE DATABASE IF NOT EXISTS `chatbox` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `chatbox`;

-- --------------------------------------------------------

--
-- Table structure for table `chat_applications`
--

CREATE TABLE IF NOT EXISTS `chat_applications` (
  `app_id` varchar(128) NOT NULL,
  `owner` int(11) NOT NULL,
  `date_registered` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE IF NOT EXISTS `chat_messages` (
  `nick` varchar(16) NOT NULL,
  `uid` int(11) NOT NULL,
  `message` text NOT NULL,
  `time` int(11) NOT NULL,
  `room` int(11) NOT NULL DEFAULT '0',
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`nick`, `uid`, `message`, `time`, `room`, `id`) VALUES
('anonymous', 0, 'Hello world', 1300000000, 0, 1),
('fuku', 1, 'adfhaerh FUCK', 1234567890, 1, 2),
('guest_1a375d', 0, 'i luv sex', 1394431175, 1, 3),
('guest_1a375d', 0, 'i luv sex', 1394431182, 1, 4),
('guest_1a375d', 0, 'boats and hoes', 1394431438, 1, 5),
('guest_1a375d', 0, 'boats and hoes', 1394431440, 1, 6),
('guest_1a375d', 0, 'boats and hoes', 1394431894, 1, 7);

-- --------------------------------------------------------

--
-- Table structure for table `chat_rooms`
--

CREATE TABLE IF NOT EXISTS `chat_rooms` (
  `name` varchar(128) NOT NULL,
  `date_registered` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `chat_rooms`
--

INSERT INTO `chat_rooms` (`name`, `date_registered`, `owner`, `id`) VALUES
('sex', 11111111, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `chat_users`
--

CREATE TABLE IF NOT EXISTS `chat_users` (
  `username` varchar(16) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `date_registered` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
