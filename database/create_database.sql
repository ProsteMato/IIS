-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: 127.0.0.1
-- Čas generovania: Út 01.Dec 2020, 19:24
-- Verzia serveru: 10.4.14-MariaDB
-- Verzia PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `post`
--

DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `thread_id` int(11) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int(11) NOT NULL,
  `creation_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Sťahujem dáta pre tabuľku `post`
--

INSERT INTO `post` (`id`, `post_id`, `thread_id`, `created_by_id`, `text`, `rating`, `creation_date`) VALUES(1, NULL, 1, 2, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi turpis metus, euismod nec pharetra in, sodales eu lorem. Nulla venenatis leo nulla, vitae eleifend eros pretium non. Sed auctor lorem nec finibus varius. Pellentesque et sollicitudin leo. Curabitur interdum mollis risus, vitae ultrices risus aliquet sed. Nunc ligula nisi, viverra non metus accumsan, mollis dictum sem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin sed tincidunt mi, et tempor dui. Curabitur convallis lacus ac fermentum maximus. Sed varius nec tellus nec pretium. Vestibulum at posuere metus. Phasellus bibendum ut nisl eget gravida. Praesent interdum enim nisi, a tempus lacus viverra ac.', 0, '2020-12-01 19:10:47');
INSERT INTO `post` (`id`, `post_id`, `thread_id`, `created_by_id`, `text`, `rating`, `creation_date`) VALUES(2, NULL, 2, 3, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi turpis metus, euismod nec pharetra in, sodales eu lorem. Nulla venenatis leo nulla, vitae eleifend eros pretium non. Sed auctor lorem nec finibus varius. Pellentesque et sollicitudin leo. Curabitur interdum mollis risus, vitae ultrices risus aliquet sed. Nunc ligula nisi, viverra non metus accumsan, mollis dictum sem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin sed tincidunt mi, et tempor dui. Curabitur convallis lacus ac fermentum maximus. Sed varius nec tellus nec pretium. Vestibulum at posuere metus. Phasellus bibendum ut nisl eget gravida. Praesent interdum enim nisi, a tempus lacus viverra ac.', 1, '2020-12-01 19:11:22');
INSERT INTO `post` (`id`, `post_id`, `thread_id`, `created_by_id`, `text`, `rating`, `creation_date`) VALUES(3, NULL, 2, 2, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi turpis metus, euismod nec pharetra in, sodales eu lorem. Nulla venenatis leo nulla, vitae eleifend eros pretium non. Sed auctor lorem nec finibus varius. Pellentesque et sollicitudin leo. Curabitur interdum mollis risus, vitae ultrices risus aliquet sed. Nunc ligula nisi, viverra non metus accumsan, mollis dictum sem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin sed tincidunt mi, et tempor dui. Curabitur convallis lacus ac fermentum maximus. Sed varius nec tellus nec pretium. Vestibulum at posuere metus. Phasellus bibendum ut nisl eget gravida. Praesent interdum enim nisi, a tempus lacus viverra ac.', 0, '2020-12-01 19:14:43');
INSERT INTO `post` (`id`, `post_id`, `thread_id`, `created_by_id`, `text`, `rating`, `creation_date`) VALUES(4, NULL, 3, 3, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi turpis metus, euismod nec pharetra in, sodales eu lorem. Nulla venenatis leo nulla, vitae eleifend eros pretium non. Sed auctor lorem nec finibus varius. Pellentesque et sollicitudin leo. Curabitur interdum mollis risus, vitae ultrices risus aliquet sed. Nunc ligula nisi, viverra non metus accumsan, mollis dictum sem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin sed tincidunt mi, et tempor dui. Curabitur convallis lacus ac fermentum maximus. Sed varius nec tellus nec pretium. Vestibulum at posuere metus. Phasellus bibendum ut nisl eget gravida. Praesent interdum enim nisi, a tempus lacus viverra ac.', 0, '2020-12-01 19:12:20');

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5A8A6C8D4B89032C` (`post_id`),
  ADD KEY `IDX_5A8A6C8DE2904019` (`thread_id`),
  ADD KEY `IDX_5A8A6C8DB03A8386` (`created_by_id`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `FK_5A8A6C8D4B89032C` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`),
  ADD CONSTRAINT `FK_5A8A6C8DB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_5A8A6C8DE2904019` FOREIGN KEY (`thread_id`) REFERENCES `thread` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
