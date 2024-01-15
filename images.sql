-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 14, 2023 at 07:02 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `serverside`
--

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `Movie_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `name`, `Movie_ID`) VALUES
(1, 'Avengers-endgame.jpeg', 1),
(3, 'WeHaveAGhost.jpg', 2),
(4, 'uncharted.jpg', 6),
(10, 'thor-loveAndThunder.jpg', 22),
(11, 'Enola.jpg', 20),
(12, 'BlackAdam.jpg', 7),
(13, 'glassOnion.jpg', 24),
(14, 'shotgun-wedding.jpg', 26),
(15, 'shazam.jpg', 28),
(16, 'johnwick-chapter4.jpg', 30),
(28, 'wakanda-forever.jpg', 8),
(37, 'antman.jpg', 32),
(39, 'murder-mystery.jpg', 53),
(40, 'avatar.jpg', 54),
(51, 'shangchi.jpg', 60),
(52, 'soul.jpg', 61),
(53, 'puss.jpg', 62),
(56, 'doctorStrange.jpg', 63),
(58, 'luther.jpg', 64),
(62, 'darkOctober.jpg', 65),
(63, 'greatGatsby.jpg', 66),
(64, 'faraway.jpg', 67),
(67, 'conjuring.jpg', 69),
(68, 'smile.jpg', 70),
(69, 'deathonthenile.jpg', 71),
(70, 'thunderforec.jpg', 72),
(71, 'jumanji.jpg', 73),
(72, 'vacation.jpg', 74),
(73, 'isntItRomantic.jpg', 75),
(74, 'champions.jpg', 76),
(75, 'blackwidow.jpg', 77),
(76, 'manfromtoronto.jpg', 78),
(77, 'deadpool2.jpg', 79),
(78, 'jurassicworld.jpg', 80),
(79, 'eternals.jpg', 81),
(80, 'freeguy.jpg', 82),
(82, 'kingsman.jpg', 84),
(83, 'hitman.jpg', 85),
(84, 'cruella.jpg', 86),
(85, 'guardians.jpg', 87),
(86, 'xmen.jpg', 88),
(87, 'readyPlayerOne.jpg', 89),
(88, 'mazerunner.jpg', 90),
(89, 'bumblebee.jpg', 91),
(93, 'findingOhana.jpg', 92),
(94, 'insurgent.jpg', 93),
(95, 'thesorcerer.jpg', 94),
(97, 'dumbledoore.jpg', 95),
(98, 'hobbit.jpg', 96),
(99, 'rampage.jpg', 97),
(101, 'theinbetween.jpg', 99),
(106, 'fallen.jpg', 101),
(108, 'missing.jpg', 103),
(109, 'knivesout.jpg', 104),
(110, 'tomorrowland.jpg', 105),
(111, 'dayshift.jpg', 106),
(112, 'nightbooks.jpg', 107),
(113, 'armyofthedead.jpg', 108),
(114, 'theson.jpg', 109),
(115, 'mebeforeyou.jpg', 110),
(116, 'gifted.jpg', 111),
(118, 'lostcity.jpg', 68),
(119, 'royaltreatment.jpg', 100),
(122, 'marrryme.jpg', 98);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `Movie_FK` (`Movie_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `Movie_FK` FOREIGN KEY (`Movie_ID`) REFERENCES `movies` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
