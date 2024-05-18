-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:8889
-- Üretim Zamanı: 18 May 2024, 01:33:22
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
-- Tablo için tablo yapısı `product`
--

CREATE TABLE `product` (
  `id` int(100) NOT NULL,
  `title` varchar(200) NOT NULL,
  `expire` date NOT NULL,
  `img` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discounted_price` decimal(10,2) NOT NULL,
  `market_email` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Tablo döküm verisi `product`
--

INSERT INTO `product` (`id`, `title`, `expire`, `img`, `description`, `price`, `discounted_price`, `market_email`, `type`) VALUES
(1, 'Banana', '2024-05-30', 'Banana-PNG-Picture.png', 'Banana is so good', '3.00', '2.00', 'bim@gmail.com', 'Fruit'),
(2, 'Almods', '2024-05-24', 'Almond-Free-Download-PNG.png', 'Almond is good', '2.99', '1.99', 'bim@gmail.com', 'Nut '),
(3, 'Avacado', '2024-05-22', 'pngtree-avocado-png-avocado-fruit-ai-generated-png-image_10153887.png', 'Avacado is good', '4.99', '3.49', 'bim@gmail.com', 'Fruit'),
(5, 'Carrot', '2024-07-25', 'red-carrot-red-carrot-transparent-background-ai-generated-free-png.webp', 'Carrot is good', '3.99', '3.99', 'migros@gmail.com', 'Vegetable'),
(6, 'Strawberries', '2024-05-30', 'ebd4deb64c74e2f1246626d5a290274d.png', 'Strawberries are good\r\n', '2.99', '2.69', 'migros@gmail.com', 'Fruit'),
(7, 'Blueberries', '2024-05-03', '83e7cb22c15636563f5e0a3d53eeb3db.png', 'Blueberries is good', '7.99', '4.00', 'migros@gmail.com', 'Fruit'),
(8, 'Eggs', '2024-05-30', 'pngimg.com - egg_PNG40811.png', 'Eggs is good', '5.00', '4.50', 'a101@gmail.com', 'Dairy'),
(9, 'Orange', '2024-05-18', 'orange-poster.png', 'Orange is good', '4.99', '4.49', 'a101@gmail.com', 'Fruit'),
(10, 'Pineapple', '2024-05-30', 'pineapple-pineapple-pineapple-transparent-background-ai-generated-free-png.webp', 'Pineapple is good', '9.99', '8.99', 'a101@gmail.com', 'Fruit'),
(11, 'Bread', '2024-05-12', 'bread.png', 'Bread is good', '2.00', '2.00', 'a101@gmail.com', ' Bakery'),
(12, 'Corn', '2024-05-19', 'yellow-corn-isolated-png.webp', 'Corn is good', '7.00', '5.60', 'a101@gmail.com', 'Vegetable'),
(13, 'Broccoli', '2024-05-31', 'broccoli-broccoli-broccoli-transparent-background-ai-generated-free-png.webp', 'Broccoli is good', '4.00', '3.60', 'bim@gmail.com', 'Vegetable'),
(15, 'Egg', '2024-05-30', 'pngimg.com - egg_PNG40811.png', 'Egg is good', '3.00', '2.70', 'bim@gmail.com', 'Dairy'),
(16, 'Bananaaa', '2024-05-22', 'Banana-PNG-Picture.png', 'Banan is good', '4.00', '3.20', 'bim@gmail.com', 'Fruit'),
(20, 'Tomato', '2024-05-30', 'tomatopng.parspng.com-3.png', 'Tomato is good', '4.00', '3.60', 'bim@gmail.com', 'Vegetable'),
(26, 'Cheese', '2024-05-23', 'Cheese-Transparent.png', 'Cheese is good', '2.49', '1.99', 'migros@gmail.com', 'Dairy'),
(27, 'Pie', '2024-05-29', 'apple-pie-transparent-png.webp', 'Pie is good', '7.99', '7.19', 'migros@gmail.com', 'Bakery'),
(28, 'Yogurt', '2024-05-24', 'bowl-of-strawberry-yogurt-on-transparent-background-free-png.webp', 'Yogurt is good', '4.00', '3.20', 'migros@gmail.com', 'Dairy'),
(29, 'Ayran', '2024-06-07', 'Ayran_200ml.png', 'Ayran is good', '0.99', '0.89', 'migros@gmail.com', 'Dairy'),
(31, 'Grapes', '2024-06-04', 'Grape_PNG_Free_Clip_Art_Image.png', 'Grapes is good', '3.00', '2.70', 'a101@gmail.com', 'Fruit'),
(32, 'Kiwi', '2024-07-04', 'Kiwi-PNG.png', 'Kiwi is good', '4.00', '4.00', 'a101@gmail.com', 'Fruit'),
(33, 'Cherry', '2024-05-30', 'pngimg.com - cherry_PNG609.png', 'Cherry is good', '2.79', '2.51', 'a101@gmail.com', 'Fruit'),
(34, 'Lime', '2024-05-29', 'pngimg.com - lime_PNG21.png', 'Lime is good', '3.00', '2.70', 'bim@gmail.com', 'Fruit'),
(35, 'Lemon', '2024-06-09', 'pngtree-lemon-png-images-with-transparent-background-png-image_6095484.png', 'Lemon is good', '1.49', '1.34', 'bim@gmail.com', 'Fruit'),
(36, 'Mango', '2024-05-30', 'pngtree-mango-realistic-fruit-photo-png-image_6658362.png', 'Mango is good', '5.69', '5.12', 'bim@gmail.com', 'Fruit'),
(37, 'Peach', '2024-07-25', 'purepng.com-peachfruitspeach-981524762023fxagv.png', 'Peach is good', '5.00', '5.00', 'migros@gmail.com', 'Fruit'),
(38, 'Watermelon', '2024-06-06', 'watermelon-transparent-background-free-png.webp', 'Watermelon is good', '9.89', '8.90', 'bim@gmail.com', 'Fruit'),
(41, 'Apple', '2024-06-30', 'apple.png', 'Fresh and juicy apples', '1.99', '1.49', 'migros@gmail.com', 'Fruit'),
(42, 'Beef', '2024-05-25', 'beef.png', 'High quality beef', '9.99', '8.49', 'bim@gmail.com', 'Meat'),
(43, 'Chicken Breast', '2024-06-01', 'chicken_breast.png', 'Lean chicken breast', '6.99', '5.99', 'a101@gmail.com', 'Meat'),
(44, 'Spinach', '2024-05-28', 'spinach.png', 'Fresh spinach', '2.49', '1.99', 'migros@gmail.com', 'Vegetable'),
(45, 'Cauliflower', '2024-05-22', 'cauliflower.png', 'Organic cauliflower', '3.49', '2.99', 'bim@gmail.com', 'Vegetable'),
(46, 'Milk', '2024-05-18', 'milk.png', 'Whole milk', '1.49', '1.29', 'a101@gmail.com', 'Dairy'),
(47, 'Butter', '2024-06-10', 'butter.png', 'Creamy butter', '2.99', '2.49', 'migros@gmail.com', 'Dairy'),
(48, 'Yogurt', '2024-06-15', 'yogurt.png', 'Plain yogurt', '1.99', '1.69', 'bim@gmail.com', 'Dairy'),
(49, 'Almond Milk', '2024-07-05', 'almond_milk.png', 'Almond milk', '3.99', '3.49', 'a101@gmail.com', 'Dairy'),
(50, 'Whole Wheat Bread', '2024-05-12', 'whole_wheat_bread.png', 'Whole wheat bread', '2.49', '2.19', 'migros@gmail.com', 'Bakery'),
(51, 'Croissant', '2024-05-15', 'croissant.png', 'Fresh croissant', '1.99', '1.79', 'bim@gmail.com', 'Bakery'),
(52, 'Chocolate Chip Cookies', '2024-05-20', 'chocolate_chip_cookies.png', 'Delicious chocolate chip cookies', '3.49', '2.99', 'a101@gmail.com', 'Bakery'),
(53, 'Salmon', '2024-05-25', 'salmon.png', 'Fresh salmon', '12.99', '11.99', 'migros@gmail.com', 'Meat'),
(54, 'Tuna', '2024-06-01', 'tuna.png', 'Fresh tuna', '10.99', '9.99', 'bim@gmail.com', 'Meat'),
(55, 'Pork Chops', '2024-05-18', 'pork_chops.png', 'Juicy pork chops', '8.99', '7.99', 'a101@gmail.com', 'Meat'),
(56, 'Ground Beef', '2024-05-22', 'ground_beef.png', 'Lean ground beef', '6.49', '5.99', 'migros@gmail.com', 'Meat'),
(57, 'Peach', '2024-07-01', 'peach.png', 'Fresh peach', '1.49', '1.29', 'bim@gmail.com', 'Fruit'),
(58, 'Plum', '2024-06-20', 'plum.png', 'Juicy plum', '1.99', '1.69', 'a101@gmail.com', 'Fruit'),
(59, 'Pear', '2024-06-15', 'pear.png', 'Fresh pear', '1.79', '1.49', 'migros@gmail.com', 'Fruit'),
(60, 'Watermelon', '2024-07-10', 'watermelon.png', 'Sweet watermelon', '4.99', '4.49', 'bim@gmail.com', 'Fruit'),
(61, 'Cantaloupe', '2024-06-25', 'cantaloupe.png', 'Juicy cantaloupe', '3.99', '3.49', 'a101@gmail.com', 'Fruit'),
(62, 'Lettuce', '2024-05-15', 'lettuce.png', 'Crisp lettuce', '1.49', '1.29', 'migros@gmail.com', 'Vegetable'),
(63, 'Cabbage', '2024-05-20', 'cabbage.png', 'Fresh cabbage', '1.99', '1.69', 'bim@gmail.com', 'Vegetable'),
(64, 'Green Beans', '2024-06-05', 'green_beans.png', 'Fresh green beans', '2.99', '2.49', 'a101@gmail.com', 'Vegetable'),
(65, 'Sweet Potato', '2024-06-10', 'sweet_potato.png', 'Organic sweet potato', '3.49', '2.99', 'migros@gmail.com', 'Vegetable'),
(66, 'Zucchini', '2024-06-15', 'zucchini.png', 'Fresh zucchini', '2.49', '2.19', 'bim@gmail.com', 'Vegetable'),
(67, 'Caramelized Onions', '2024-05-25', 'caramelized_onions.png', 'Sweet caramelized onions', '3.99', '3.49', 'a101@gmail.com', 'Vegetable'),
(68, 'Cheddar Cheese', '2024-06-30', 'cheddar_cheese.png', 'Aged cheddar cheese', '4.99', '4.49', 'migros@gmail.com', 'Dairy'),
(69, 'Mozzarella Cheese', '2024-06-20', 'mozzarella_cheese.png', 'Fresh mozzarella cheese', '3.99', '3.49', 'bim@gmail.com', 'Dairy'),
(70, 'Bagels', '2024-05-22', 'bagels.png', 'Fresh bagels', '2.99', '2.49', 'a101@gmail.com', 'Bakery'),
(71, 'Coke', '2024-05-28', 'Coca-Cola-9.png', 'Coke is good', '2.49', '1.99', 'migros@gmail.com', 'Drink'),
(72, 'Elma', '2024-05-29', 'apple-43-300x297.png', 'Elma güzel', '2.00', '1.60', 'bim@gmail.com', 'Fruit');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `product`
--
ALTER TABLE `product`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
