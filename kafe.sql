-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Bulan Mei 2025 pada 16.20
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kafe`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `short_desc` varchar(255) NOT NULL,
  `modal_desc` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `rating` decimal(2,1) NOT NULL DEFAULT 0.0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `origin` varchar(100) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `intensity` varchar(10) NOT NULL,
  `roast_level` varchar(20) NOT NULL,
  `category` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `short_desc`, `modal_desc`, `price`, `stock`, `rating`, `created_at`, `origin`, `image_url`, `intensity`, `roast_level`, `category`) VALUES
(1, 'Cappucino Regular', 'Espresso + susu steam dan foam lembut', 'Cappuccino kami dibuat dengan campuran espresso premium dan susu steam lembut, disajikan dengan foam creamy dan latte art indah.', 35000.00, 10, 4.5, '2025-05-30 15:38:23', 'Arabika Gayo, Aceh', 'https://www.livingnorth.com/images/media/articles/food-and-drink/eat-and-drink/coffee.png?fm=webp&w=1000', '7/10', 'Medium-Dark', 'Espresso Based'),
(2, 'Americano', 'Espresso + air panas, rasa kuat', 'Americano kami menyajikan kombinasi sempurna antara espresso dan air panas, memberikan rasa kopi yang pekat dan kaya aroma.', 25000.00, 15, 4.2, '2025-05-30 15:38:23', 'Arabika Flores, Nusa Tenggara', 'https://www.foodandwine.com/thmb/k8YTwOlm3J86ejoVCsrvrgoA47g=/750x0/filters:no_upscale():max_bytes(150000):strip_icc():format(webp)/Partners-Americano-FT-BLOG0523-b8e18cc340574cc9bed536cceeec7082.jpg', '6/10', 'Dark', 'Espresso Based'),
(3, 'Latte', 'Espresso + susu hangat creamy', 'Latte kami diracik dengan espresso lembut dan susu panas, menciptakan minuman creamy yang lezat lengkap dengan latte art.', 30000.00, 20, 4.3, '2025-05-30 15:38:23', 'Arabika Java, Jawa', 'https://nucleuscoffee.com/cdn/shop/articles/Latte-recipe.jpg?v=1714499640', '5/10', 'Light Medium', 'Espresso Based'),
(6, 'Espresso Arabica', 'Espresso klasik dengan cita rasa tajam', 'Espresso murni dari 100% arabika dengan body kuat dan aroma tajam.', 25000.00, 20, 4.5, '2025-05-30 18:48:36', 'Sumatra', 'https://images.unsplash.com/photo-1511920170033-f8396924c348?auto=format&fit=crop&w=800&q=80', 'Tinggi', 'Dark', 'Espresso Based'),
(7, 'Latte Vanilla', 'Latte lembut dengan aroma vanila', 'Campuran espresso dan susu dengan sentuhan vanila.', 30000.00, 15, 4.7, '2025-05-30 18:48:36', 'Java', 'https://asset-2.tstatic.net/travel/foto/bank/images/ilustrasi-minuman-vanilla-latte.jpg', 'Sedang', 'Medium', 'Espresso Based'),
(8, 'Cold Brew Citrus', 'Cold brew segar dengan lemon', 'Kopi seduh dingin yang dicampur citrus segar untuk rasa unik.', 28000.00, 10, 4.2, '2025-05-30 18:48:36', 'Toraja', 'https://www.redpathsugar.com/sites/redpathsugar_com/files/styles/m/public/Orange_Cold_Brew_Coffee_Spritzer_500x400.jpg.webp?itok=sj2WmZSX', 'Rendah', 'Light', 'Manual Brew'),
(9, 'Vietnam Drip', 'Kopi manis gaya Vietnam', 'Kopi robusta kental dengan susu kental manis khas Vietnam.', 26000.00, 10, 4.3, '2025-05-30 18:48:36', 'Vietnam', 'https://deltacoffee.co.id/wp-content/uploads/2023/03/Kopitiam-Vietnam-Drip.jpg', 'Tinggi', 'Dark', 'Manual Brew'),
(10, 'Matcha Latte', 'Teh hijau lembut dengan susu', 'Matcha Jepang premium yang diseduh dengan susu creamy.', 32000.00, 12, 4.6, '2025-05-30 18:48:36', 'Jepang', 'https://www-justonecookbook-com.translate.goog/wp-content/uploads/2022/12/Matcha-Latte-4589-II.jpg?_x_tr_sl=en&_x_tr_tl=id&_x_tr_hl=id&_x_tr_pto=imgs', 'Sedang', '', 'Non Coffee'),
(11, 'Chocolate Hazelnut', 'Cokelat panas dengan hazelnut', 'Minuman cokelat kaya rasa dipadukan dengan aroma hazelnut.', 28000.00, 8, 4.5, '2025-05-30 18:48:36', 'Sulawesi', 'https://cocosutra.com/cdn/shop/files/Cocosutra_Hazelnut_Hot_Chocolate_Drink.png?v=1694304694&width=500', 'Rendah', '', 'Non Coffee'),
(12, 'Croissant Butter', 'Croissant lembut isi mentega', 'Croissant Prancis autentik dengan isian mentega yang lumer.', 20000.00, 25, 4.4, '2025-05-30 18:48:36', 'Prancis', 'https://www.lalevain.com/wp-content/uploads/2021/11/Xsant-768x768.jpg', '', '', 'Makanan'),
(13, 'Spaghetti Bolognese', 'Pasta klasik dengan saus daging', 'Spaghetti Italia dengan saus tomat dan daging cincang.', 35000.00, 15, 4.7, '2025-05-30 18:48:36', 'Italia', 'https://img.taste.com.au/VFkGwzXU/w720-h480-cfill-q80/taste/2016/11/spaghetti-bolognese-106560-1.jpeg', '', '', 'Makanan'),
(14, 'Affogato', 'Espresso tuang ke atas es krim', 'Espresso panas dituangkan ke atas vanilla ice cream.', 33000.00, 10, 4.8, '2025-05-30 18:48:36', 'Bali', 'https://static01.nyt.com/images/2021/08/15/magazine/affogato/affogato-jumbo-v2.jpg?quality=75&auto=webp', 'Tinggi', 'Medium', 'Espresso Based'),
(15, 'Cappuccino Caramel', 'Cappuccino dengan karamel', 'Minuman cappuccino ditambah sirup karamel untuk rasa manis.', 31000.00, 10, 4.6, '2025-05-30 18:48:36', 'Flores', 'https://healthyhut.shop/cdn/shop/files/WhatsAppImage2024-08-30at01.26.21.jpg?v=1725274111&width=990', 'Sedang', 'Medium', 'Espresso Based');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
