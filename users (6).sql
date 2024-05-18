-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:8889
-- Üretim Zamanı: 18 May 2024, 01:33:29
-- Sunucu sürümü: 5.7.39
-- PHP Sürümü: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `test`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `remember` varchar(200) NOT NULL DEFAULT '',
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `district` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci NOT NULL,
  `logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`name`, `email`, `password`, `remember`, `is_admin`, `city`, `district`, `address`, `logo`) VALUES
('Macro Admin', 'admin1@gmail.com', '$2y$10$w74yYlTe49BjZPbDNB7FpusrLLlqNFMHJEVSjTGrlLKXA7UoiDmbO', '', 1, 'Ankara', 'Çankaya', 'Türkiye, Ankara, Çankaya, Birlik', NULL),
('pratik adam', 'arman@gmail.com', '$2y$10$9wB9sBQ4AOD8jOijelL1pus.QIs5Hh7QhhHVe1tdz.WwCrzoGgF6S', '', 0, 'Ankara', 'Çankaya', 'Türkiye, Ankara, Çankaya', NULL),
('ogulcan', 'ogulcan@gmail.com', '$2y$10$7gP1GsIZUgXhBTVg8y92teUV1cje1FSCZ2cjHqrV4YKjRgn/RLKPa', '', 0, '', '', '', NULL),
('onurcan', 'onurcan@gmail.com', '$2y$10$UYXVR0xi.5I6w/biyoXRo.5Sj4WG1s7TEZxZhceZz4mKxK/vB9/0C', '', 0, '', '', '', NULL),
('ece', 'ece@gmail.com', '$2y$10$VuxKGfRbHX78n7TUOlFzweZ3zvdmKa/vdmJU8PdBN2HAiXDGogBY2', '', 0, '', '', '', NULL),
('Arman Yılmazkurt ', 'armanyilmazkurt@gmail.com', '$2y$10$Kj0Ai5eiAZeuBymNEfMtFedpdtpdYdXRh1uTqIbgnwnxB6FbhETHu', '', 0, 'Ankara', 'Çankaya', 'Türkiye, Ankara, Çankaya', NULL),
('Hahahahaha', 'a@g.com', '$2y$10$ZQJoD0rlSF6xpKCZgjseI.K/KwaZppxwMgLOSxlvCqmb0IUUQ5OJC', '', 0, 'aa', 'a', 'a', NULL),
('Sibel', 'sibelyil@hotmail.com', '$2y$10$6UXeTRXsNAIYHyQSJDiuruWhbGh/iP5LISgJcSN8dRjKwo1D0wFcW', '', 0, 'Ankara', 'Çankaya', 'Türkiye, Ankara, Çankaya', NULL),
('Market1', 'market@gmail.com', '$2y$10$Ecsa2fsT6K6rgcMi/LtYqusrmrvtB8a6WD6Knxvazyir3kX6adb06', '', 0, 'Ankara', 'Çankaya', 'Türkiye, Ankara, Çankaya', NULL),
('Walmart', 'market1@gmail.com', '$2y$10$YFl5aaQ.96jwhOpRk4i3MufZ1XI3s4Jaaum4tumwkBX7z8cNoZKgO', '', 1, 'Ankara', 'Çankaya', 'Türkiye, Ankara, Çankaya', NULL),
('Migros', 'migros@gmail.com', '$2y$10$waMrRH8rHajr.Q0bUuKjze.k20f5nyxmEuuDn8zyXhfgcN3FpB.E.', '', 1, 'Ankara', 'Çankaya', 'Türkiye, Ankara, Çankaya', 'uploads/664137c2e987f0.73469394.png'),
('BİM', 'bim@gmail.com', '$2y$10$.yUK7fr1AbEW2lcNPWU90.G8kdi7cNvfzX2N3i8xRRgLjdgqaZXGi', '', 1, 'Ankara', 'Çankaya', 'Türkiye, Ankara, Çankaya, BİRLİK', 'uploads/66413b2cdba912.02621715.png'),
('A101', 'a101@gmail.com', '$2y$10$4mibGrrLV2YnEghArJWfiOWKUELZHPOIOKfvieDNW5IPDqdXU.9zy', '', 1, 'Ankara', 'Çankaya', 'Türkiye, Ankara, Çankaya, BİRLİK', 'uploads/66413b99eb6074.26160299.png'),
('Bora', 'bora@gmail.com', '$2y$10$B6eid8dKEh40hObQTay4nu1he72CmRMmxOONohzGS8fA0qd568LGe', '', 0, 'Ankara', 'Çankaya', 'Türkiye, Ankara, Çankaya', NULL),
('Naima', 'naima@gmail.com', '$2y$10$sKQdVa2PhMlVGkUgGcYqueVbyvbZb9U7Ylw2AYkie4sj/wnhWK4YG', '', 0, 'Ankara', 'Çankaya', 'Türkiye, Ankara, Çankaya, BİRLİK', NULL),
('Arman Yılmazkurt', 'armanyilmazkurt123@gmail.com', '$2y$10$iF4Fr.FVt8KZjlBbPovJoODPcf9fGQq03iBq2mUXnxHYn4MKEMZDq', '', 0, 'Ankara', 'Çankaya', 'Türkiye, Ankara, Çankaya', NULL),
('arman', 'arman.yilmazkurt@ug.bilkent.edu.tr', '$2y$10$J3vqOWxSctvi5GAY/7KyI.kqmatiRSPLsgi.xlF4pQ.jwnwnX6vl.', '', 0, 'Ankara', 'Çankaya', 'Türkiye, Ankara, Çankaya', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
