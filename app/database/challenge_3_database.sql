-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-04-2025 a las 13:04:58
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `challenge_3_database`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `category` enum('footwear','bottoms','tops','home-decor','elektrical-devices','kitchen','card-games','video-games','board-games') DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `status` enum('sold','in stock') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `product`
--

INSERT INTO `product` (`id`, `user_id`, `name`, `category`, `description`, `price`, `status`, `created_at`) VALUES
(1, 1, 'Nike Air Max', 'footwear', 'Lightweight running shoes in great condition.', 85, 'sold', '2025-04-07 10:38:12'),
(2, 2, 'Levi\'s 501 Jeans', 'bottoms', 'Classic straight-fit jeans.', 40, 'in stock', '2025-04-07 10:38:12'),
(3, 1, 'Winter Jacket', 'tops', 'Warm and waterproof jacket.', 50, 'sold', '2025-04-07 10:38:12'),
(4, 2, 'Scandinavian Wall Clock', 'home-decor', 'Minimalist wooden wall clock.', 35, 'sold', '2025-04-07 10:38:12'),
(5, 1, 'Sony WH-1000XM4', 'elektrical-devices', 'Noise-cancelling headphones.', 200, 'sold', '2025-04-07 10:38:12'),
(6, 2, 'Instant Pot Duo', 'kitchen', '7-in-1 electric pressure cooker.', 60, 'in stock', '2025-04-07 10:38:12'),
(7, 1, 'Uno', 'card-games', 'Fun family card game.', 10, 'sold', '2025-04-07 10:38:12'),
(8, 2, 'The Legend of Zelda: Breath of the Wild', 'video-games', 'Open-world adventure game for Nintendo Switch.', 45, 'in stock', '2025-04-07 10:38:12'),
(9, 1, 'Settlers of Catan', 'board-games', 'Classic strategy board game.', 25, 'in stock', '2025-04-07 10:38:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sale_information`
--

CREATE TABLE `sale_information` (
  `Information` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sale_information`
--

INSERT INTO `sale_information` (`Information`) VALUES
('don_omar has bought Scandinavian Wall Clock at 2025-04-08 10:33:24'),
('arruti3 has bought Sony WH-1000XM4 at 2025-04-11 09:28:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','member') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user`
--

INSERT INTO `user` (`id`, `username`, `firstname`, `lastname`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'testuser', 'Test', 'User', 'test@example.com', '$2a$12$MB0TrAWjbMFNcOKSthHmkO7wGU0QcSEgKFt1QDu4tTzyq5SwV0q4q', 'member', '2025-04-07 10:38:12'),
(2, 'admin', 'Admin', 'User', 'admin@example.com', '$2a$12$MB0TrAWjbMFNcOKSthHmkO7wGU0QcSEgKFt1QDu4tTzyq5SwV0q4q', 'admin', '2025-04-07 10:38:12'),
(3, 'irakaslea', 'irakaslea', 'irakaslea', 'irakalsea@uni.eus', 'irakaslea', 'admin', '2025-04-11 09:15:36');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user','','') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `firstname`, `lastname`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'danelk', 'Danel', 'Carril', 'carril.danel@uni.eus', 'danel123', 'admin', '2025-04-02 12:05:01'),
(2, 'don_omar', 'Don', 'Omar', 'omar.don@uni.eus', 'friopolar', 'admin', '2025-04-04 08:53:19'),
(4, 'arruti3', 'Aritz', 'Arruti', 'arruti.aritz@uni.eus', 'arruti123', 'admin', '2025-04-09 12:13:10'),
(5, 'irakaslea', 'irakaslea', 'irakaslea', 'irakaslea@uni.eus', 'irakaslea', 'admin', '2025-04-11 09:17:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_product`
--

CREATE TABLE `user_product` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `sales` int(11) DEFAULT 0,
  `Information` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `user_product`
--

INSERT INTO `user_product` (`id`, `user_id`, `product_id`, `sales`, `Information`) VALUES
(3, 2, 7, 0, NULL),
(7, 1, 3, 0, NULL),
(12, 2, 4, 0, NULL),
(16, 4, 5, 0, NULL);

--
-- Disparadores `user_product`
--
DELIMITER $$
CREATE TRIGGER `Sale_time` AFTER INSERT ON `user_product` FOR EACH ROW INSERT INTO sale_information
SELECT CONCAT(u.username, ' has bought ', p.name, ' at ', NOW())
FROM user_product s 
INNER JOIN product p ON s.product_id = p.id
INNER JOIN users u ON s.user_id = u.id
WHERE s.id = NEW.id
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_sale_insert` AFTER INSERT ON `user_product` FOR EACH ROW BEGIN
    UPDATE product
    SET status = 'sold'
    WHERE id = NEW.product_id;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `user_product`
--
ALTER TABLE `user_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `product`
--
ALTER TABLE `product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `user_product`
--
ALTER TABLE `user_product`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Filtros para la tabla `user_product`
--
ALTER TABLE `user_product`
  ADD CONSTRAINT `user_product_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_product_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
