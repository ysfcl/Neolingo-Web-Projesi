-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 15 Haz 2025, 20:09:45
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `dil_ogrenme`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `bilgiler`
--

CREATE TABLE `bilgiler` (
  `bilgi_id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `baslik` varchar(100) NOT NULL,
  `icerik` text DEFAULT NULL,
  `tarih` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `bilgiler`
--

INSERT INTO `bilgiler` (`bilgi_id`, `kullanici_id`, `baslik`, `icerik`, `tarih`) VALUES
(10, 4, 'kelime 2', 'gato -> kedi', '2025-06-12'),
(11, 4, 'kelime 3', 'holla-> merhaba', '2025-06-13');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `calisma_oturumlari`
--

CREATE TABLE `calisma_oturumlari` (
  `oturum_id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `dil_id` int(11) NOT NULL,
  `baslangic_zamani` datetime NOT NULL,
  `bitis_zamani` datetime DEFAULT NULL,
  `calisma_suresi` int(11) GENERATED ALWAYS AS (timestampdiff(MINUTE,`baslangic_zamani`,`bitis_zamani`)) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `diller`
--

CREATE TABLE `diller` (
  `dil_id` int(11) NOT NULL,
  `dil_adi` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kelimeler`
--

CREATE TABLE `kelimeler` (
  `kelime_id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `dil_id` int(11) NOT NULL,
  `kelime` varchar(100) NOT NULL,
  `ceviri` varchar(100) DEFAULT NULL,
  `ornek_cumle` text DEFAULT NULL,
  `eklenme_suresi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `kullanici_id` int(11) NOT NULL,
  `kullanici_adi` varchar(50) NOT NULL,
  `ad` varchar(50) DEFAULT NULL,
  `soyad` varchar(50) DEFAULT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `dogum_tarihi` date DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `sifre_hash` varchar(255) NOT NULL,
  `hesap_olusturma` timestamp NOT NULL DEFAULT current_timestamp(),
  `ogrenilen_dil` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `kullanicilar`
--

INSERT INTO `kullanicilar` (`kullanici_id`, `kullanici_adi`, `ad`, `soyad`, `telefon`, `dogum_tarihi`, `email`, `sifre_hash`, `hesap_olusturma`, `ogrenilen_dil`) VALUES
(3, 'root', 'aaa', 'bbb', '555', '2025-06-14', 'ab@hotmail.com', '$2y$10$yaYqAG/GC./TYydOvXEmauoCya8n5aTSTviCVSPlh3sxyjAXYRIHC', '2025-06-14 20:20:59', NULL),
(4, 'kullanici', 'Yusuf', 'Çil', '544 444 44 44', '2025-06-02', 'bb@hotmail.com', '$2y$10$WlvfWkvCGtTxC9N02WkEseEpn3T5L6lrwd4o.NY1W84SpD9Xc7ldO', '2025-06-15 17:00:21', 'İspanyolca');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kullanici_dil`
--

CREATE TABLE `kullanici_dil` (
  `kullanici_dil_id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `dil_id` int(11) NOT NULL,
  `seviye` enum('Baslangic','Orta','Ileri') DEFAULT 'Baslangic'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `ogrenme_kaydi`
--

CREATE TABLE `ogrenme_kaydi` (
  `ogrenme_id` int(11) NOT NULL,
  `kullanici_id` int(11) NOT NULL,
  `dil_id` int(11) NOT NULL,
  `tarih` date NOT NULL,
  `etkinlik_tipi` enum('Kelime','Dinleme','Konusma','Yazma','Okuma') NOT NULL,
  `sure_dakika` int(11) NOT NULL,
  `notlar` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `bilgiler`
--
ALTER TABLE `bilgiler`
  ADD PRIMARY KEY (`bilgi_id`),
  ADD KEY `kullanici_id` (`kullanici_id`);

--
-- Tablo için indeksler `calisma_oturumlari`
--
ALTER TABLE `calisma_oturumlari`
  ADD PRIMARY KEY (`oturum_id`),
  ADD KEY `kullanici_id` (`kullanici_id`),
  ADD KEY `dil_id` (`dil_id`);

--
-- Tablo için indeksler `diller`
--
ALTER TABLE `diller`
  ADD PRIMARY KEY (`dil_id`),
  ADD UNIQUE KEY `dil_adi` (`dil_adi`);

--
-- Tablo için indeksler `kelimeler`
--
ALTER TABLE `kelimeler`
  ADD PRIMARY KEY (`kelime_id`),
  ADD KEY `kullanici_id` (`kullanici_id`),
  ADD KEY `dil_id` (`dil_id`);

--
-- Tablo için indeksler `kullanicilar`
--
ALTER TABLE `kullanicilar`
  ADD PRIMARY KEY (`kullanici_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Tablo için indeksler `kullanici_dil`
--
ALTER TABLE `kullanici_dil`
  ADD PRIMARY KEY (`kullanici_dil_id`),
  ADD KEY `kullanici_id` (`kullanici_id`),
  ADD KEY `dil_id` (`dil_id`);

--
-- Tablo için indeksler `ogrenme_kaydi`
--
ALTER TABLE `ogrenme_kaydi`
  ADD PRIMARY KEY (`ogrenme_id`),
  ADD KEY `kullanici_id` (`kullanici_id`),
  ADD KEY `dil_id` (`dil_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `bilgiler`
--
ALTER TABLE `bilgiler`
  MODIFY `bilgi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Tablo için AUTO_INCREMENT değeri `calisma_oturumlari`
--
ALTER TABLE `calisma_oturumlari`
  MODIFY `oturum_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `diller`
--
ALTER TABLE `diller`
  MODIFY `dil_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `kelimeler`
--
ALTER TABLE `kelimeler`
  MODIFY `kelime_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `kullanicilar`
--
ALTER TABLE `kullanicilar`
  MODIFY `kullanici_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Tablo için AUTO_INCREMENT değeri `kullanici_dil`
--
ALTER TABLE `kullanici_dil`
  MODIFY `kullanici_dil_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `ogrenme_kaydi`
--
ALTER TABLE `ogrenme_kaydi`
  MODIFY `ogrenme_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `bilgiler`
--
ALTER TABLE `bilgiler`
  ADD CONSTRAINT `bilgiler_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`kullanici_id`);

--
-- Tablo kısıtlamaları `calisma_oturumlari`
--
ALTER TABLE `calisma_oturumlari`
  ADD CONSTRAINT `calisma_oturumlari_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`kullanici_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `calisma_oturumlari_ibfk_2` FOREIGN KEY (`dil_id`) REFERENCES `diller` (`dil_id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `kelimeler`
--
ALTER TABLE `kelimeler`
  ADD CONSTRAINT `kelimeler_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`kullanici_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kelimeler_ibfk_2` FOREIGN KEY (`dil_id`) REFERENCES `diller` (`dil_id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `kullanici_dil`
--
ALTER TABLE `kullanici_dil`
  ADD CONSTRAINT `kullanici_dil_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`kullanici_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kullanici_dil_ibfk_2` FOREIGN KEY (`dil_id`) REFERENCES `diller` (`dil_id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `ogrenme_kaydi`
--
ALTER TABLE `ogrenme_kaydi`
  ADD CONSTRAINT `ogrenme_kaydi_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`kullanici_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ogrenme_kaydi_ibfk_2` FOREIGN KEY (`dil_id`) REFERENCES `diller` (`dil_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
