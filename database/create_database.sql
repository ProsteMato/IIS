-- phpMyAdmin SQL Dump
-- version 5.0.3
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: 127.0.0.1
-- Čas generovania: Út 01.Dec 2020, 21:58
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
-- Databáza: `sn`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `group`
--

DROP TABLE IF EXISTS `group`;
CREATE TABLE `group` (
  `id` int(11) NOT NULL,
  `admin_user_id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `visibility` tinyint(1) NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_created` datetime NOT NULL,
  `picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `open` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Sťahujem dáta pre tabuľku `group`
--

INSERT INTO `group` (`id`, `admin_user_id`, `name`, `visibility`, `description`, `date_created`, `picture`, `open`) VALUES
(1, 2, 'Group-owner', 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi turpis metus, euismod nec pharetra in, sodales eu lorem. Nulla venenatis leo nulla, vitae eleifend eros pretium non. Sed auctor lorem nec finibus varius.', '2020-12-01 18:56:48', 'blank_group.png', 0),
(2, 3, 'Group-mod', 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi turpis metus, euismod nec pharetra in, sodales eu lorem. Nulla venenatis leo nulla, vitae eleifend eros pretium non. Sed auctor lorem nec finibus varius.', '2020-12-01 19:02:05', '7de7a389ab6119b1d39e082564015eb5.jpeg', 0),
(3, 3, 'Group-member', 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi turpis metus, euismod nec pharetra in, sodales eu lorem. Nulla venenatis leo nulla, vitae eleifend eros pretium non. Sed auctor lorem nec finibus varius.', '2020-12-01 19:03:43', '2163e5a6e97fdf101cd47e6d87715580.jpeg', 1),
(4, 2, 'Group-join', 0, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi turpis metus, euismod nec pharetra in, sodales eu lorem. Nulla venenatis leo nulla, vitae eleifend eros pretium non. Sed auctor lorem nec finibus varius.', '2020-12-01 19:06:46', 'blank_group.png', 1),
(5, 2, 'Group-apply', 1, NULL, '2020-12-01 19:07:01', 'blank_group.png', 0);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `group_user`
--

DROP TABLE IF EXISTS `group_user`;
CREATE TABLE `group_user` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Sťahujem dáta pre tabuľku `group_user`
--

INSERT INTO `group_user` (`id`, `group_id`, `user_id`, `role`) VALUES
(1, 1, 2, 'a:2:{i:0;s:8:\"ROLE_MEM\";i:1;s:8:\"ROLE_MOD\";}'),
(2, 2, 3, 'a:2:{i:0;s:8:\"ROLE_MEM\";i:1;s:8:\"ROLE_MOD\";}'),
(3, 3, 3, 'a:2:{i:0;s:8:\"ROLE_MEM\";i:1;s:8:\"ROLE_MOD\";}'),
(4, 2, 2, 'a:2:{i:1;s:8:\"ROLE_MEM\";i:2;s:8:\"ROLE_MOD\";}'),
(5, 3, 2, 'a:1:{i:1;s:8:\"ROLE_MEM\";}'),
(6, 4, 2, 'a:2:{i:0;s:8:\"ROLE_MEM\";i:1;s:8:\"ROLE_MOD\";}'),
(7, 5, 2, 'a:2:{i:0;s:8:\"ROLE_MEM\";i:1;s:8:\"ROLE_MOD\";}');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `post`
--

DROP TABLE IF EXISTS `post`;
CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `thread_id` int(11) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `rating` int(11) NOT NULL,
  `creation_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Sťahujem dáta pre tabuľku `post`
--

INSERT INTO `post` (`id`, `thread_id`, `created_by_id`, `text`, `rating`, `creation_date`) VALUES
(1, 1, 2, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi turpis metus, euismod nec pharetra in, sodales eu lorem. Nulla venenatis leo nulla, vitae eleifend eros pretium non. Sed auctor lorem nec finibus varius. Pellentesque et sollicitudin leo. Curabitur interdum mollis risus, vitae ultrices risus aliquet sed. Nunc ligula nisi, viverra non metus accumsan, mollis dictum sem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin sed tincidunt mi, et tempor dui. Curabitur convallis lacus ac fermentum maximus. Sed varius nec tellus nec pretium. Vestibulum at posuere metus. Phasellus bibendum ut nisl eget gravida. Praesent interdum enim nisi, a tempus lacus viverra ac.', 0, '2020-12-01 19:10:47'),
(2, 2, 3, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi turpis metus, euismod nec pharetra in, sodales eu lorem. Nulla venenatis leo nulla, vitae eleifend eros pretium non. Sed auctor lorem nec finibus varius. Pellentesque et sollicitudin leo. Curabitur interdum mollis risus, vitae ultrices risus aliquet sed. Nunc ligula nisi, viverra non metus accumsan, mollis dictum sem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin sed tincidunt mi, et tempor dui. Curabitur convallis lacus ac fermentum maximus. Sed varius nec tellus nec pretium. Vestibulum at posuere metus. Phasellus bibendum ut nisl eget gravida. Praesent interdum enim nisi, a tempus lacus viverra ac.', 1, '2020-12-01 19:11:22'),
(3, 2, 2, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi turpis metus, euismod nec pharetra in, sodales eu lorem. Nulla venenatis leo nulla, vitae eleifend eros pretium non. Sed auctor lorem nec finibus varius. Pellentesque et sollicitudin leo. Curabitur interdum mollis risus, vitae ultrices risus aliquet sed. Nunc ligula nisi, viverra non metus accumsan, mollis dictum sem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin sed tincidunt mi, et tempor dui. Curabitur convallis lacus ac fermentum maximus. Sed varius nec tellus nec pretium. Vestibulum at posuere metus. Phasellus bibendum ut nisl eget gravida. Praesent interdum enim nisi, a tempus lacus viverra ac.', 0, '2020-12-01 19:14:43');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `post_user`
--

DROP TABLE IF EXISTS `post_user`;
CREATE TABLE `post_user` (
  `id` int(11) NOT NULL,
  `threads_id` int(11) NOT NULL,
  `group_list_id` int(11) NOT NULL,
  `posts_id` int(11) NOT NULL,
  `users_id` int(11) DEFAULT NULL,
  `liked` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Sťahujem dáta pre tabuľku `post_user`
--

INSERT INTO `post_user` (`id`, `threads_id`, `group_list_id`, `posts_id`, `users_id`, `liked`) VALUES
(1, 2, 2, 2, 2, 'like');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `thread`
--

DROP TABLE IF EXISTS `thread`;
CREATE TABLE `thread` (
  `id` int(11) NOT NULL,
  `group_id_id` int(11) NOT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rating` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  `last_update` datetime NOT NULL,
  `views` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Sťahujem dáta pre tabuľku `thread`
--

INSERT INTO `thread` (`id`, `group_id_id`, `created_by_id`, `title`, `description`, `rating`, `creation_date`, `last_update`, `views`) VALUES
(1, 1, 2, 'Thread1', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi turpis metus, euismod nec pharetra in, sodales eu lorem. Nulla venenatis leo nulla, vitae eleifend eros pretium non. Sed auctor lorem nec finibus varius. Pellentesque et sollicitudin leo. Curabitur interdum mollis risus, vitae ultrices risus aliquet sed. Nunc ligula nisi, viverra non metus accumsan, mollis dictum sem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin sed tincidunt mi, et tempor dui. Curabitur convallis lacus ac fermentum maximus. Sed varius nec tellus nec pretium. Vestibulum at posuere metus. Phasellus bibendum ut nisl eget gravida. Praesent interdum enim nisi, a tempus lacus viverra ac.', 1, '2020-12-01 19:07:46', '2020-12-01 19:10:47', 3),
(2, 2, 3, 'Thread1', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi turpis metus, euismod nec pharetra in, sodales eu lorem. Nulla venenatis leo nulla, vitae eleifend eros pretium non. Sed auctor lorem nec finibus varius. Pellentesque et sollicitudin leo. Curabitur interdum mollis risus, vitae ultrices risus aliquet sed. Nunc ligula nisi, viverra non metus accumsan, mollis dictum sem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin sed tincidunt mi, et tempor dui. Curabitur convallis lacus ac fermentum maximus. Sed varius nec tellus nec pretium. Vestibulum at posuere metus. Phasellus bibendum ut nisl eget gravida. Praesent interdum enim nisi, a tempus lacus viverra ac.', 1, '2020-12-01 19:11:12', '2020-12-01 19:14:43', 6),
(3, 2, 2, 'Thread2', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi turpis metus, euismod nec pharetra in, sodales eu lorem. Nulla venenatis leo nulla, vitae eleifend eros pretium non. Sed auctor lorem nec finibus varius. Pellentesque et sollicitudin leo. Curabitur interdum mollis risus, vitae ultrices risus aliquet sed. Nunc ligula nisi, viverra non metus accumsan, mollis dictum sem. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin sed tincidunt mi, et tempor dui. Curabitur convallis lacus ac fermentum maximus. Sed varius nec tellus nec pretium. Vestibulum at posuere metus. Phasellus bibendum ut nisl eget gravida. Praesent interdum enim nisi, a tempus lacus viverra ac.', -1, '2020-12-01 19:12:02', '2020-12-01 20:19:40', 7);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `thread_user`
--

DROP TABLE IF EXISTS `thread_user`;
CREATE TABLE `thread_user` (
  `id` int(11) NOT NULL,
  `threads_id` int(11) NOT NULL,
  `users_id` int(11) DEFAULT NULL,
  `group_list_id` int(11) NOT NULL,
  `liked` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Sťahujem dáta pre tabuľku `thread_user`
--

INSERT INTO `thread_user` (`id`, `threads_id`, `users_id`, `group_list_id`, `liked`) VALUES
(1, 2, 2, 2, 'like'),
(2, 3, 2, 2, 'dislike'),
(3, 1, 2, 1, 'like');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` date DEFAULT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sex` varchar(6) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `visibility` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Sťahujem dáta pre tabuľku `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `first_name`, `last_name`, `birth_date`, `profile_picture`, `description`, `sex`, `visibility`) VALUES
(1, 'admin@email.com', '[\"ROLE_ADMIN\"]', '$argon2i$v=19$m=65536,t=4,p=1$aGpWOWtLNnYuS2w3eHJKbw$Ts60SiwwyI5l9ekGxmLSYpS4GlNdU9pSnyUCogZXgi8', 'Admin', 'A', NULL, 'blank.png', NULL, NULL, 'noone'),
(2, 'user@email.com', '[]', '$argon2i$v=19$m=65536,t=4,p=1$UVp6ZEt4NHI4NXpYOFJJdw$Z4SElxZ0e3RELJFlPMbPE55fvflguyku9gVqsZXxGc8', 'Joe', 'Black', '2001-05-18', 'ae8bffcaeeaf525e43b6b9901d053cc6.jpeg', 'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA', 'male', 'registered'),
(3, 'user2@email.com', '[]', '$argon2i$v=19$m=65536,t=4,p=1$SkdxbEh6TVp0Tm1YVmRpcg$JkMx5vRJzkc9hXbTxm9ioxg0r+5EgJd1HjKZYCdi9ks', 'Anna', 'Smith', '1999-06-05', 'blank.png', NULL, 'female', 'members'),
(4, 'user3@email.com', '[]', '$argon2i$v=19$m=65536,t=4,p=1$RGwwTTRlV1JuNTM3Y3ZRTQ$KTj4TzjM4KpGuEEBBRvCIVIe6xatgOe2RAjwou8Hehg', 'Bea', 'Happy', '1996-04-05', 'blank.png', NULL, 'female', 'everyone');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `user_user`
--

DROP TABLE IF EXISTS `user_user`;
CREATE TABLE `user_user` (
  `user_source` int(11) NOT NULL,
  `user_target` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `group`
--
ALTER TABLE `group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6DC044C56352511C` (`admin_user_id`);

--
-- Indexy pre tabuľku `group_user`
--
ALTER TABLE `group_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A4C98D39FE54D947` (`group_id`),
  ADD KEY `IDX_A4C98D39A76ED395` (`user_id`);

--
-- Indexy pre tabuľku `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5A8A6C8DE2904019` (`thread_id`),
  ADD KEY `IDX_5A8A6C8DB03A8386` (`created_by_id`);

--
-- Indexy pre tabuľku `post_user`
--
ALTER TABLE `post_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_44C6B14283F885A5` (`threads_id`),
  ADD KEY `IDX_44C6B142BBAE4287` (`group_list_id`),
  ADD KEY `IDX_44C6B142D5E258C5` (`posts_id`),
  ADD KEY `IDX_44C6B14267B3B43D` (`users_id`);

--
-- Indexy pre tabuľku `thread`
--
ALTER TABLE `thread`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_31204C832F68B530` (`group_id_id`),
  ADD KEY `IDX_31204C83B03A8386` (`created_by_id`);

--
-- Indexy pre tabuľku `thread_user`
--
ALTER TABLE `thread_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_922CAC783F885A5` (`threads_id`),
  ADD KEY `IDX_922CAC767B3B43D` (`users_id`),
  ADD KEY `IDX_922CAC7BBAE4287` (`group_list_id`);

--
-- Indexy pre tabuľku `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- Indexy pre tabuľku `user_user`
--
ALTER TABLE `user_user`
  ADD PRIMARY KEY (`user_source`,`user_target`),
  ADD KEY `IDX_F7129A803AD8644E` (`user_source`),
  ADD KEY `IDX_F7129A80233D34C1` (`user_target`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `group`
--
ALTER TABLE `group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pre tabuľku `group_user`
--
ALTER TABLE `group_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pre tabuľku `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pre tabuľku `post_user`
--
ALTER TABLE `post_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pre tabuľku `thread`
--
ALTER TABLE `thread`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pre tabuľku `thread_user`
--
ALTER TABLE `thread_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pre tabuľku `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `group`
--
ALTER TABLE `group`
  ADD CONSTRAINT `FK_6DC044C56352511C` FOREIGN KEY (`admin_user_id`) REFERENCES `user` (`id`);

--
-- Obmedzenie pre tabuľku `group_user`
--
ALTER TABLE `group_user`
  ADD CONSTRAINT `FK_A4C98D39A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_A4C98D39FE54D947` FOREIGN KEY (`group_id`) REFERENCES `group` (`id`);

--
-- Obmedzenie pre tabuľku `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `FK_5A8A6C8DB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_5A8A6C8DE2904019` FOREIGN KEY (`thread_id`) REFERENCES `thread` (`id`);

--
-- Obmedzenie pre tabuľku `post_user`
--
ALTER TABLE `post_user`
  ADD CONSTRAINT `FK_44C6B14267B3B43D` FOREIGN KEY (`users_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_44C6B14283F885A5` FOREIGN KEY (`threads_id`) REFERENCES `thread` (`id`),
  ADD CONSTRAINT `FK_44C6B142BBAE4287` FOREIGN KEY (`group_list_id`) REFERENCES `group` (`id`),
  ADD CONSTRAINT `FK_44C6B142D5E258C5` FOREIGN KEY (`posts_id`) REFERENCES `post` (`id`);

--
-- Obmedzenie pre tabuľku `thread`
--
ALTER TABLE `thread`
  ADD CONSTRAINT `FK_31204C832F68B530` FOREIGN KEY (`group_id_id`) REFERENCES `group` (`id`),
  ADD CONSTRAINT `FK_31204C83B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`);

--
-- Obmedzenie pre tabuľku `thread_user`
--
ALTER TABLE `thread_user`
  ADD CONSTRAINT `FK_922CAC767B3B43D` FOREIGN KEY (`users_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_922CAC783F885A5` FOREIGN KEY (`threads_id`) REFERENCES `thread` (`id`),
  ADD CONSTRAINT `FK_922CAC7BBAE4287` FOREIGN KEY (`group_list_id`) REFERENCES `group` (`id`);

--
-- Obmedzenie pre tabuľku `user_user`
--
ALTER TABLE `user_user`
  ADD CONSTRAINT `FK_F7129A80233D34C1` FOREIGN KEY (`user_target`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_F7129A803AD8644E` FOREIGN KEY (`user_source`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
