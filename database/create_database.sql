-- Autor: Magdaléna Ondrušková <xondru16@stud.fit.vutbr.cz>
-- Date:  15.11.2020
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


DROP TABLE IF EXISTS `ban`;
DROP TABLE IF EXISTS `group`;
DROP TABLE IF EXISTS `member`;
DROP TABLE IF EXISTS `moderator`;
DROP TABLE IF EXISTS `post`;
DROP TABLE IF EXISTS `registration`;
DROP TABLE IF EXISTS `subscribe`;
DROP TABLE IF EXISTS `thread`;
DROP TABLE IF EXISTS `user`;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `ban`
--

CREATE TABLE `ban` (
  `groupID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  PRIMARY KEY (`groupID`,`userID`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;


INSERT INTO `ban` (`groupID`, `userID`) VALUES
(2, 3);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `group`
--

CREATE TABLE `group` (
  `gID` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `visibility` tinyint(1) NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci,
  `date_created` date NOT NULL,
  `picture` blob,
  `administrate` int(11) NOT NULL,
  PRIMARY KEY (`gID`),
  KEY `administrate` (`administrate`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs AUTO_INCREMENT=6 ;


INSERT INTO `group` (`gID`, `name`, `visibility`, `description`, `date_created`, `picture`, `administrate`) VALUES
(1, 'Coronavirus', 1, 'Group to discuss actual situation in world about epidemic of coronavirus ', '2020-11-02', NULL, 1),
(2, 'Political situation in USA', 1, 'Discussion about election in USA. ', '2020-11-01', NULL, 2),
(3, 'Programming ', 0, 'Let''s talk about programming. ', '2020-11-14', NULL, 2),
(4, 'World news', 1, 'Discussion about everything that is going on in the world', '2020-10-14', NULL, 2),
(5, 'Movies ', 0, 'new movies in cinema, on netflix or just general news about movies ', '2020-11-03', NULL, 3);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `member`
--

CREATE TABLE `member` (
  `groupID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  PRIMARY KEY (`groupID`,`userID`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;


INSERT INTO `member` (`groupID`, `userID`) VALUES
(4, 1),
(1, 2),
(5, 2),
(1, 3);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `moderator`
--

CREATE TABLE `moderator` (
  `groupID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  PRIMARY KEY (`groupID`,`userID`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;


INSERT INTO `moderator` (`groupID`, `userID`) VALUES
(1, 2);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `post`
--

CREATE TABLE `post` (
  `pID` int(11) NOT NULL AUTO_INCREMENT,
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `picture` blob,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ranking` int(11) NOT NULL DEFAULT '0',
  `wrote` int(11) NOT NULL,
  `is_answer` int(11) DEFAULT NULL,
  `in_thread` int(11) NOT NULL,
  PRIMARY KEY (`pID`),
  KEY `wrote` (`wrote`),
  KEY `is_answer` (`is_answer`),
  KEY `in_thread` (`in_thread`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs AUTO_INCREMENT=8 ;


INSERT INTO `post` (`pID`, `text`, `picture`, `date_created`, `ranking`, `wrote`, `is_answer`, `in_thread`) VALUES
(1, 'Numbers are great but they are also testing less and less people. ', NULL, '2020-11-15 10:21:19', 0, 1, NULL, 1),
(2, 'Yeah, they are testing less and less people but the number of positive people tested is decreasing, so thats good thing.', NULL, '2020-11-15 14:03:11', 0, 2, 1, 1),
(3, 'And the number of people in hospital si also getting lower, so it looks like the things will be getting slowly better.', NULL, '2020-11-15 14:13:53', 0, 1, 1, 1),
(4, 'But I think it is too soon to celebrate. ', NULL, '2020-11-15 15:07:52', 0, 3, NULL, 1);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `registration`
--

CREATE TABLE `registration` (
  `groupID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  PRIMARY KEY (`groupID`,`userID`),
  KEY `userID` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `subscribe`
--

CREATE TABLE `subscribe` (
  `userID` int(11) NOT NULL,
  `subscribeID` int(11) NOT NULL,
  PRIMARY KEY (`userID`,`subscribeID`),
  KEY `subscribeID` (`subscribeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs;


INSERT INTO `subscribe` (`userID`, `subscribeID`) VALUES
(1, 2),
(1, 3);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `thread`
--

CREATE TABLE `thread` (
  `tID` int(11) NOT NULL AUTO_INCREMENT,
  `title` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `ranking` int(11) NOT NULL DEFAULT '0',
  `picture` blob,
  `in_group` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  PRIMARY KEY (`tID`),
  KEY `has` (`in_group`),
  KEY `created` (`created`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs AUTO_INCREMENT=6 ;


INSERT INTO `thread` (`tID`, `title`, `description`, `ranking`, `picture`, `in_group`, `created`) VALUES
(1, 'Situation in Czech Republic', 'The situation is getting better. What do you think was the main reason it started getting better? ', 0, NULL, 1, 1),
(2, 'Slovakia''s Borders', 'What does it mean for students in Czech republic? ', 0, NULL, 1, 2),
(3, 'Who do you think will win?', 'I''m not exactly fan of Trump, so I hope for Biden. ', 0, NULL, 2, 3),
(4, 'Poland protests', 'Im supporting protests in Poland. I don''t think that another people should decide what I would do with my body. ', 0, NULL, 4, 1),
(5, 'Tenet', 'I just saw Tenet yesterday. So if you saw it, I want to know what did you think about it. ', 0, NULL, 5, 3);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `user`
--

CREATE TABLE `user` (
  `uID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_slovak_ci NOT NULL,
  `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `first_name` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `last_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `sex` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
  `date_of_birth` date NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `visibility` tinyint(1) NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci,
  `profile_picture` blob,
  PRIMARY KEY (`uID`),
  UNIQUE KEY `username` (`username`,`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin2 COLLATE=latin2_czech_cs AUTO_INCREMENT=4 ;


INSERT INTO `user` (`uID`, `username`, `password`, `first_name`, `last_name`, `sex`, `date_of_birth`, `email`, `visibility`, `description`, `profile_picture`) VALUES
(1, 'kate', 'heslo123', 'Kate', 'White', 'female', '1998-05-15', 'kate_white@email.com', 1, 'I like marvel movies and books', NULL),
(2, 'adam', 'Heslo123', 'Adam', 'Smith', 'male', '1996-01-01', 'smith@email.com', 0, 'I like programming.', NULL),
(3, 'michael', 'Heslo456', 'Michael', 'Black', 'male', '2000-03-02', 'michael.black@email.com', 1, NULL, NULL);


--
-- Obmedzenie pre tabuľku `ban`
--
ALTER TABLE `ban`
  ADD CONSTRAINT `ban_ibfk_1` FOREIGN KEY (`groupID`) REFERENCES `group` (`gID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `ban_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `user` (`uID`) ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `group`
--
ALTER TABLE `group`
  ADD CONSTRAINT `group_ibfk_1` FOREIGN KEY (`administrate`) REFERENCES `user` (`uID`) ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `member`
--
ALTER TABLE `member`
  ADD CONSTRAINT `member_ibfk_1` FOREIGN KEY (`groupID`) REFERENCES `group` (`gID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `member_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `user` (`uID`) ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `moderator`
--
ALTER TABLE `moderator`
  ADD CONSTRAINT `moderator_ibfk_1` FOREIGN KEY (`groupID`) REFERENCES `group` (`gID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `moderator_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `user` (`uID`) ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`wrote`) REFERENCES `user` (`uID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `post_ibfk_2` FOREIGN KEY (`is_answer`) REFERENCES `post` (`pID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `post_ibfk_4` FOREIGN KEY (`in_thread`) REFERENCES `thread` (`tID`) ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `registration`
--
ALTER TABLE `registration`
  ADD CONSTRAINT `registration_ibfk_1` FOREIGN KEY (`groupID`) REFERENCES `group` (`gID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `registration_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `user` (`uID`) ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `subscribe`
--
ALTER TABLE `subscribe`
  ADD CONSTRAINT `subscribe_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `user` (`uID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `subscribe_ibfk_2` FOREIGN KEY (`subscribeID`) REFERENCES `user` (`uID`) ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `thread`
--
ALTER TABLE `thread`
  ADD CONSTRAINT `thread_ibfk_1` FOREIGN KEY (`in_group`) REFERENCES `group` (`gID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `thread_ibfk_2` FOREIGN KEY (`created`) REFERENCES `user` (`uID`) ON UPDATE CASCADE;

