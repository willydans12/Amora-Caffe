-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Jun 2025 pada 06.55
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
-- Struktur dari tabel `checkout`
--

CREATE TABLE `checkout` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `total_price` decimal(12,2) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `payments`
--

CREATE TABLE `payments` (
  `id` int(11) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `shipping_cost` decimal(12,2) NOT NULL COMMENT 'Biaya pengiriman, misal 15000.00',
  `subtotal` decimal(12,2) NOT NULL COMMENT 'Total harga barang sebelum pajak dan ongkos kirim',
  `tax` decimal(12,2) NOT NULL COMMENT 'Pajak 10% dari subtotal',
  `total` decimal(12,2) NOT NULL COMMENT 'subtotal + tax + shipping_cost',
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'Metode pembayaran (diisi di tahap selanjutnya)',
  `proof_filename` varchar(255) DEFAULT NULL,
  `status` enum('pending','paid','failed') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `payments`
--

INSERT INTO `payments` (`id`, `first_name`, `last_name`, `address`, `phone`, `email`, `shipping_cost`, `subtotal`, `tax`, `total`, `payment_method`, `proof_filename`, `status`, `created_at`) VALUES
(1, 'Wildan', 'Bathsya', 'Jl mayor jendral sutoyo no 20', '083193649165', 'amandacorp27@gmail.com', 15000.00, 66000.00, 6600.00, 87600.00, 'Transfer Bank', NULL, 'pending', '2025-06-03 15:35:25'),
(11, 'Wildan', 'Bathsya', 'Jl mayor jendral sutoyo no 20', '083193649165', 'littleenterprise07@gmail.com', 15000.00, 28000.00, 2800.00, 45800.00, 'ewallet', 'proof_11_683eebde1cc66.jpg', 'paid', '2025-06-03 19:34:22'),
(12, 'Wildan', 'Bathsya', 'Jl mayor jendral sutoyo no 20', '083193649165', 'amandacorp27@gmail.com', 15000.00, 31000.00, 3100.00, 49100.00, 'bank', 'proof_12_6840576602f1b.jpeg', 'paid', '2025-06-04 21:25:10'),
(14, 'jessica', 'miranda', 'Jl mayor jendral sutoyo no 22', '0891213114', 'jeje@gmail.com', 15000.00, 64000.00, 6400.00, 85400.00, 'ewallet', 'proof_14_6852d0b1c8daf.jpg', 'paid', '2025-06-18 21:43:48');

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
(1, 'Cappucino Regular', 'Espresso + susu steam dan foam lembut', 'Cappuccino kami dibuat dengan campuran espresso premium dan susu steam lembut, disajikan dengan foam creamy dan latte art indah.', 35000.00, 5, 4.5, '2025-05-30 15:38:23', 'Arabika Gayo, Aceh', 'https://www.livingnorth.com/images/media/articles/food-and-drink/eat-and-drink/coffee.png?fm=webp&w=1000', '7/10', 'Medium-Dark', 'Espresso Based'),
(2, 'Americano', 'Espresso + air panas, rasa kuat', 'Americano kami menyajikan kombinasi sempurna antara espresso dan air panas, memberikan rasa kopi yang pekat dan kaya aroma.', 25000.00, 15, 4.2, '2025-05-30 15:38:23', 'Arabika Flores, Nusa Tenggara', 'https://www.foodandwine.com/thmb/k8YTwOlm3J86ejoVCsrvrgoA47g=/750x0/filters:no_upscale():max_bytes(150000):strip_icc():format(webp)/Partners-Americano-FT-BLOG0523-b8e18cc340574cc9bed536cceeec7082.jpg', '6/10', 'Dark', 'Espresso Based'),
(3, 'Latte', 'Espresso + susu hangat creamy', 'Latte kami diracik dengan espresso lembut dan susu panas, menciptakan minuman creamy yang lezat lengkap dengan latte art.', 30000.00, 20, 4.3, '2025-05-30 15:38:23', 'Arabika Java, Jawa', 'https://nucleuscoffee.com/cdn/shop/articles/Latte-recipe.jpg?v=1714499640', '5/10', 'Light Medium', 'Espresso Based'),
(6, 'Espresso Arabica', 'Espresso klasik dengan cita rasa tajam', 'Espresso murni dari 100% arabika dengan body kuat dan aroma tajam.', 25000.00, 20, 4.5, '2025-05-30 18:48:36', 'Sumatra', 'https://images.unsplash.com/photo-1511920170033-f8396924c348?auto=format&fit=crop&w=800&q=80', 'Tinggi', 'Darks', 'Espresso Based'),
(7, 'Latte Vanilla', 'Latte lembut dengan aroma vanila', 'Campuran espresso dan susu dengan sentuhan vanila.', 30000.00, 15, 4.7, '2025-05-30 18:48:36', 'Java', 'https://asset-2.tstatic.net/travel/foto/bank/images/ilustrasi-minuman-vanilla-latte.jpg', 'Sedang', 'Medium', 'Espresso Based'),
(8, 'Cold Brew Citrus', 'Cold brew segar dengan lemon', 'Kopi seduh dingin yang dicampur citrus segar untuk rasa unik.', 28000.00, 10, 4.2, '2025-05-30 18:48:36', 'Toraja', 'https://www.redpathsugar.com/sites/redpathsugar_com/files/styles/m/public/Orange_Cold_Brew_Coffee_Spritzer_500x400.jpg.webp?itok=sj2WmZSX', 'Rendah', 'Light', 'Manual Brew'),
(9, 'Vietnam Drip', 'Kopi manis gaya Vietnam', 'Kopi robusta kental dengan susu kental manis khas Vietnam.', 26000.00, 10, 4.3, '2025-05-30 18:48:36', 'Vietnam', 'https://deltacoffee.co.id/wp-content/uploads/2023/03/Kopitiam-Vietnam-Drip.jpg', 'Tinggi', 'Dark', 'Manual Brew'),
(10, 'Matcha Latte', 'Teh hijau lembut dengan susu', 'Matcha Jepang premium yang diseduh dengan susu creamy.', 32000.00, 12, 4.6, '2025-05-30 18:48:36', 'Jepang', 'https://www-justonecookbook-com.translate.goog/wp-content/uploads/2022/12/Matcha-Latte-4589-II.jpg?_x_tr_sl=en&_x_tr_tl=id&_x_tr_hl=id&_x_tr_pto=imgs', 'Sedang', '', 'Non Coffee'),
(11, 'Chocolate Hazelnut', 'Cokelat panas dengan hazelnut', 'Minuman cokelat kaya rasa dipadukan dengan aroma hazelnut.', 28000.00, 5, 4.5, '2025-05-30 00:00:00', 'Sulawesi', 'https://cocosutra.com/cdn/shop/files/Cocosutra_Hazelnut_Hot_Chocolate_Drink.png?v=1694304694&width=500', 'Rendah', '', 'Non Coffee'),
(12, 'Croissant Butter', 'Croissant lembut isi mentega', 'Croissant Prancis autentik dengan isian mentega yang lumer.', 20000.00, 25, 4.4, '2025-05-30 18:48:36', 'Prancis', 'https://www.lalevain.com/wp-content/uploads/2021/11/Xsant-768x768.jpg', '', '', 'Makanan'),
(13, 'Spaghetti Bolognese', 'Pasta klasik dengan saus daging', 'Spaghetti Italia dengan saus tomat dan daging cincang.', 35000.00, 15, 4.7, '2025-05-30 18:48:36', 'Italia', 'https://img.taste.com.au/VFkGwzXU/w720-h480-cfill-q80/taste/2016/11/spaghetti-bolognese-106560-1.jpeg', '', '', 'Makanan'),
(14, 'Affogato', 'Espresso tuang ke atas es krim', 'Espresso panas dituangkan ke atas vanilla ice cream.', 33000.00, 9, 4.8, '2025-05-30 18:48:36', 'Bali', 'https://static01.nyt.com/images/2021/08/15/magazine/affogato/affogato-jumbo-v2.jpg?quality=75&auto=webp', 'Tinggi', 'Medium', 'Espresso Based'),
(15, 'Cappuccino Caramel', 'Cappuccino dengan karamel', 'Minuman cappuccino ditambah sirup karamel untuk rasa manis.', 31000.00, 5, 4.6, '2025-05-30 18:48:36', 'Flores', 'https://healthyhut.shop/cdn/shop/files/WhatsAppImage2024-08-30at01.26.21.jpg?v=1725274111&width=990', 'Sedang', 'Medium', 'Espresso Based');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('Admin','Kasir','Staff','User') DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `profil` varchar(255) DEFAULT NULL COMMENT 'Path atau URL ke gambar profil',
  `no_telepon` varchar(20) DEFAULT NULL COMMENT 'Nomor telepon pengguna',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `role`, `alamat`, `tanggal_lahir`, `password`, `status`, `profil`, `no_telepon`, `created_at`) VALUES
(10, 'haris sumatra', 'haris@gmail.com', 'Admin', 'Jl mayor jendral sutoyo no 20', '2000-07-15', 'haris12345', 1, 'https://i.pinimg.com/736x/2c/0a/e1/2c0ae1072cfbc34edceef11f03005300.jpg', NULL, '2025-06-18 09:48:35'),
(11, 'wildan bathsya', 'wildan@gmail.com', 'Admin', 'jl wr supratman no 10', '2005-11-09', 'wildan25', 1, 'https://i.pinimg.com/736x/e1/ff/54/e1ff5427ae685c0943fb289f76e0ed17.jpg', NULL, '2025-06-18 09:48:35'),
(12, 'jesica miranda', 'jeje@gmail.com', 'User', 'jl kinar poebian no 112', '2004-12-16', 'jeje12345', 1, 'https://i.pinimg.com/736x/60/17/58/60175839250ac4c8468438cc3265e734.jpg', '08121776612', '2025-06-18 09:48:35'),
(14, 'zaki', 'zaki@gmail.com', 'Kasir', 'jl wr supratman no 21', '1998-12-17', 'zaki123', 1, 'https://i.pinimg.com/736x/ca/80/d5/ca80d550d12c49cc2fe8aeb3e7113beb.jpg', NULL, '2025-06-18 09:48:35');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `checkout`
--
ALTER TABLE `checkout`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `checkout`
--
ALTER TABLE `checkout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
