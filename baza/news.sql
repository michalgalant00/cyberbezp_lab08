-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 31 Maj 2023, 01:16
-- Wersja serwera: 10.4.25-MariaDB
-- Wersja PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `news`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_polish_ci NOT NULL COMMENT 'name of the message',
  `type` varchar(20) COLLATE utf8_polish_ci DEFAULT NULL COMMENT 'type of the message\r\n(private/public)',
  `message` varchar(2000) COLLATE utf8_polish_ci NOT NULL COMMENT 'message text',
  `deleted` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'existing message - 0, deleted - 1',
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `message`
--

INSERT INTO `message` (`id`, `name`, `type`, `message`, `deleted`, `user_id`) VALUES
(1, 'New Intel technology', 'public', 'Intel has announced a new processor for desktops', 0, NULL),
(2, 'Intel shares raising', 'private', 'brokers announce: Intel shares will go up!', 0, NULL),
(3, 'New graphic card from NVidia', 'public', 'NVidia has announced a new graphic card for desktops', 0, NULL),
(4, 'Airplane crash', 'public', 'A passenger plane has crashed in Europe', 0, NULL),
(5, 'Coronavirus', 'private', 'A new version of virus was found!', 0, NULL),
(6, 'Bitcoin price raises', 'public', 'Price of bitcoin reaches new record.', 0, NULL),
(9, 'New Windows announced', 'public', 'A new version of windows was announced. Present buyers of Widows\r\n10 can update the system to the newest version for free.', 0, NULL);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `permissions`
--

INSERT INTO `permissions` (`id`, `name`) VALUES
(1, 'Admin'),
(4, 'Delete'),
(5, 'View'),
(6, 'Role1'),
(7, 'Role2'),
(8, 'Role3'),
(9, 'msg_edit'),
(10, 'msg_delete');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_polish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Admin'),
(2, 'User'),
(3, 'Other'),
(4, 'Useless'),
(5, 'msg_role');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `role_permission`
--

CREATE TABLE `role_permission` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `role_permission`
--

INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(2, 5),
(3, 6),
(3, 7),
(3, 8),
(5, 9),
(5, 10);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `login` varchar(30) COLLATE utf8_polish_ci NOT NULL,
  `email` varchar(60) COLLATE utf8_polish_ci NOT NULL,
  `hash` blob DEFAULT NULL,
  `salt` blob DEFAULT NULL COMMENT 'salt to use in password hashing',
  `2fa` tinyint(4) DEFAULT NULL,
  `sms_code` varchar(6) COLLATE utf8_polish_ci DEFAULT NULL COMMENT 'security code sent via sms or e-mail',
  `code_timelife` timestamp NULL DEFAULT NULL COMMENT 'timelife of security code',
  `security_question` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL COMMENT 'additional security question used while password recovering',
  `answer` varchar(255) COLLATE utf8_polish_ci DEFAULT NULL COMMENT 'security question answer',
  `lockout_time` timestamp NULL DEFAULT NULL COMMENT 'time to which user account is blocked',
  `session_id` blob DEFAULT NULL COMMENT 'user session identifier',
  `id_status` int(11) NOT NULL COMMENT 'account status',
  `password_form` int(11) NOT NULL DEFAULT 1 COMMENT '1- SHA512, 2-SHA512+salt,3- HMAC'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `user`
--

INSERT INTO `user` (`id`, `login`, `email`, `hash`, `salt`, `2fa`, `sms_code`, `code_timelife`, `security_question`, `answer`, `lockout_time`, `session_id`, `id_status`, `password_form`) VALUES
(1, 'john', 'johny@gmail.com', 0x3535326432396639323930623935323165363031366332323936666134353131, 0x734635256752, 0, '345543', '2022-01-05 13:25:36', 'Your friend\'s name?', 'Peter', NULL, NULL, 2, 1),
(2, 'susie', 'susie@gmail.com', 0x3863393066323836373836633766336239363536346531653838653064646162, 0x6a363752, 0, '674545', '2022-01-12 13:25:36', 'Where were you on your 2015\'s holiday?', 'Turkey', NULL, NULL, 5, 1),
(3, 'anie', 'anie@gmail.com', 0x6463623731306135363663326132346338626661663833363138653732386637, 0x73646667683534, 0, NULL, NULL, 'Your favorite color?', 'Navy blue', NULL, NULL, 1, 1),
(27, 'test', 'test', 0x3063666532366163356230303330613164346436613330643139336536383261643630313233656363383061636163613030303434336134383838363263383066363036366631363832633136323163353162303762613631623935396533613565666435393635343836306635326564316236636461336532373464623533, 0xe30a90779acb7eb48cc244e37d137848, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users_sessions`
--

CREATE TABLE `users_sessions` (
  `user_id` int(11) NOT NULL,
  `data_login` timestamp NULL DEFAULT NULL,
  `data_logout` timestamp NULL DEFAULT NULL,
  `hash_session_id` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Zrzut danych tabeli `users_sessions`
--

INSERT INTO `users_sessions` (`user_id`, `data_login`, `data_logout`, `hash_session_id`) VALUES
(19, '2023-05-30 19:18:36', '2023-05-30 19:20:15', '4a3a28c5946d56760dc216a6ebb1c9533d7ca0ac528b074636861baee474e3f0'),
(23, '2023-05-30 19:20:22', '2023-05-30 19:21:10', 'ed097ec801f008dc5cab216e9c2e270838d10570047823e432f49d9d84abae1d'),
(19, '2023-05-30 19:21:20', '2023-05-30 19:22:02', 'cabcbb3b0f92526cd9122fdda36d0f947395513fa3d11492569741bf0f901c5a'),
(19, '2023-05-30 19:55:31', NULL, 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855'),
(19, '2023-05-30 19:55:42', '2023-05-30 20:02:04', 'ce097b44fc35dea3a5a569bd091e11630575825fa73cc00449a7dba2680bbdee'),
(19, '2023-05-30 20:02:13', NULL, 'b9d545ea81f49995bbe9e786646ac1aef5d579ff379faec3cf7fbc37bf7a5765'),
(19, '2023-05-30 20:31:09', '2023-05-30 20:36:09', '5eef2cc2088cf280e29d4576391d3b53edea31a77155618a8c518b3a8e01f14e'),
(23, '2023-05-30 20:36:17', '2023-05-30 22:12:24', '261b8626a05884fc7133479144780bd63780787b5e954fd4e8dbaa1eec8be517'),
(19, '2023-05-30 22:12:17', '2023-05-30 22:12:24', '261b8626a05884fc7133479144780bd63780787b5e954fd4e8dbaa1eec8be517'),
(19, '2023-05-30 22:18:44', NULL, 'e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855'),
(19, '2023-05-30 22:25:29', '2023-05-30 23:03:53', 'e442ad93f948b98ee57fbc3be7128b489312df126720df3693f6dc2fd0af2e40'),
(19, '2023-05-30 22:52:30', '2023-05-30 23:03:53', 'e442ad93f948b98ee57fbc3be7128b489312df126720df3693f6dc2fd0af2e40'),
(27, '2023-05-30 23:04:08', NULL, '6c16837c44445c6408878c37d2689e38c07f3ff7149bbf382ff19d150209967a'),
(27, '2023-05-30 23:15:02', NULL, '6c16837c44445c6408878c37d2689e38c07f3ff7149bbf382ff19d150209967a');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_permission`
--

CREATE TABLE `user_permission` (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user_role`
--

CREATE TABLE `user_role` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_polish_ci;

--
-- Zrzut danych tabeli `user_role`
--

INSERT INTO `user_role` (`user_id`, `role_id`) VALUES
(27, 1);

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indeksy dla tabeli `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `FKuser674283` (`id_status`);

--
-- Indeksy dla tabeli `user_permission`
--
ALTER TABLE `user_permission`
  ADD PRIMARY KEY (`user_id`,`permission_id`),
  ADD KEY `FK_user_permission_permission` (`permission_id`);

--
-- Indeksy dla tabeli `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT dla tabeli `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT dla tabeli `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT dla tabeli `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `role_permission`
--
ALTER TABLE `role_permission`
  ADD CONSTRAINT `role_permission_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permission_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Ograniczenia dla tabeli `user_permission`
--
ALTER TABLE `user_permission`
  ADD CONSTRAINT `FK_user_permission_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_user_permission_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Ograniczenia dla tabeli `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
