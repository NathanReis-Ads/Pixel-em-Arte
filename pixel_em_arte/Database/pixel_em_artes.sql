-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09/06/2025 às 12:20
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `pixel_em_artes`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

DROP TABLE IF EXISTS `categoria`;
CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categoria`
--

INSERT INTO `categoria` (`id`, `nome`, `descricao`) VALUES
(1, 'Inimigos', 'Modelos de Monstros ou Inimigos.'),
(2, 'NPCS', 'Modelos de NPCS.'),
(3, 'Protagonistas', 'Modelos de Personagens Principais.'),
(4, 'Animais', 'Modelos de Animais.'),
(5, 'Florestas', 'Objetos de Florestas, como árvores e arbustos.'),
(6, 'Cavernas', 'Objetos de Cavernas, como entradas e interiores.'),
(7, 'Vilarejos', 'Objetos de Vilas, como casas e interiores.'),
(8, 'Reinos', 'Objetos de Reinos, como castelos e interiores.'),
(9, 'Castelos', 'Modelos de Castelos e interiores.'),
(10, 'Casas', 'Modelos de Casas e interiores.'),
(11, 'Lojas', 'Modelos de Lojas e interiores.'),
(12, 'Móveis', 'Modelos de Móveis em geral.'),
(13, 'Armas Corpo a Corpo', 'Espadas, Lanças, Machados, etc.'),
(14, 'Armas a Distância', 'Arcos, armas de fogo.'),
(15, 'Magias', 'Feitiços, encantamentos, magias em geral.'),
(16, 'Equipamentos', 'Armaduras, mochilas, ferramentas de trabalho.');

-- --------------------------------------------------------

--
-- Estrutura para tabela `imagens`
--

DROP TABLE IF EXISTS `imagens`;
CREATE TABLE `imagens` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `url_imagem` varchar(255) NOT NULL,
  `data_postagem` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `imagens`
--

INSERT INTO `imagens` (`id`, `id_usuario`, `titulo`, `descricao`, `preco`, `url_imagem`, `data_postagem`) VALUES
(5, 3, 'Mario ', 'Sprite do Mario em 16 Bits', 1000.00, 'https://art.pixilart.com/d16ffd5a4de077b.png', '2025-04-28 20:12:21'),
(6, 5, 'Pinguim', 'Pixel arte de um pinguim fofinho!', 100.00, 'https://prohama.com/wp-content/uploads/2022/01/pingwin_4_2_16_16.jpg', '2025-05-24 18:09:46'),
(7, 6, 'Megaman', 'Conjuto de Sprites do Megaman ', 153.00, 'https://drzanuff.github.io/Compendium-Setmind/godot/Compendium-Sprites-Animados/images/image24.png', '2025-05-29 21:47:16'),
(9, 6, 'Local Bonito', 'Local bonito', 150.00, 'https://cdn.mos.cms.futurecdn.net/EFXSes9UCfsyRVoNeQ2ZTB.png', '2025-06-07 17:11:02'),
(10, 6, 'Personagem de cabelo ruivo', 'Sprites de um personagem', 50.00, 'https://img.craftpix.net/2021/05/Free-3-Cyberpunk-Characters-Pixel-Art5.jpg', '2025-06-07 17:14:57'),
(11, 6, 'Personagem Ninja', 'Ninja ', 100.00, 'https://img.craftpix.net/2023/02/Free-Samurai-Pixel-Art-Sprite-Sheets7.jpg', '2025-06-07 17:16:01'),
(12, 6, 'Conjuto de Npcs', 'Um Conjunto de Npcs para jogos de Rpg, se virem para ás animações ', 500.00, 'https://i.pinimg.com/originals/c1/d8/26/c1d826a78d47f27d5b5fd7ca74135e8f.png', '2025-06-07 17:17:05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `image_categoria`
--

DROP TABLE IF EXISTS `image_categoria`;
CREATE TABLE `image_categoria` (
  `id_imagem` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `image_categoria`
--

INSERT INTO `image_categoria` (`id_imagem`, `id_categoria`) VALUES
(9, 5),
(10, 3),
(11, 3),
(12, 2);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamento`
--

DROP TABLE IF EXISTS `pagamento`;
CREATE TABLE `pagamento` (
  `id` int(11) NOT NULL,
  `id_venda` int(11) NOT NULL,
  `status_pagamento` enum('pendente','aprovado','recusado') NOT NULL,
  `data_pagamento` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pagamento`
--

INSERT INTO `pagamento` (`id`, `id_venda`, `status_pagamento`, `data_pagamento`) VALUES
(1, 1, 'aprovado', '2025-06-03 19:05:40'),
(2, 2, 'aprovado', '2025-06-03 19:44:28'),
(3, 3, 'aprovado', '2025-06-03 19:49:38'),
(4, 4, 'aprovado', '2025-06-04 18:46:56'),
(5, 5, 'aprovado', '2025-06-04 18:46:56'),
(6, 6, 'aprovado', '2025-06-04 19:46:49');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `dat_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo_usuario` enum('comprador','vendedor','ambos','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `email`, `senha`, `nome`, `dat_criacao`, `tipo_usuario`) VALUES
(3, 'teste@teste.com', '1234', 'Usuário Teste', '2025-04-28 19:30:45', 'ambos'),
(5, 'Banana@Teste.com', '$2y$10$Dsb5KysoCmQbYAMlqzWx1Oryzmo803FbAaiyLE2e.dE4qYsqTOBD2', 'Banana1', '2025-05-24 16:40:51', 'ambos'),
(6, 'AAA@teste', '$2y$10$Z2NO2oMezY7QT4AK9h5e5OEd/ePRR3YH7XMNVZB1dkAa6gw3HLn7i', 'AAAAA', '2025-05-24 18:10:17', 'admin'),
(7, 'Dante@Hotmail.com', '$2y$10$YP//ZS5dTv/gv42ODLSzQ.L.LPQcGFx6.k7EWyHc3gWjY7wA7WzyC', 'Dante', '2025-06-04 18:45:35', 'comprador'),
(8, 'Omega@Hotmail', '$2y$10$Ft.GNZPE2lgmVkidc3gc7OdXn4PnSaztRp4HF02UUZApg2oUv8.62', 'ÔmegaChad', '2025-06-04 19:00:11', 'ambos'),
(9, 'Nathalia@Outlook.com', '$2y$10$6BmFAWU9ywmqSK8FyUTfn..CViM388QZN4tq5.HNyvK/j4XUgRKoO', 'Nathalia ', '2025-06-04 19:45:12', 'ambos');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

DROP TABLE IF EXISTS `vendas`;
CREATE TABLE `vendas` (
  `id` int(11) NOT NULL,
  `id_comprador` int(11) NOT NULL,
  `id_imagem` int(11) NOT NULL,
  `data_venda` timestamp NOT NULL DEFAULT current_timestamp(),
  `valor_pago` decimal(10,2) NOT NULL,
  `metodo_pagamento` enum('Cartão','Pix','Boleto','Transferência') NOT NULL DEFAULT 'Cartão',
  `status` enum('pendente','completo','cancelado') DEFAULT 'completo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vendas`
--

INSERT INTO `vendas` (`id`, `id_comprador`, `id_imagem`, `data_venda`, `valor_pago`, `metodo_pagamento`, `status`) VALUES
(1, 6, 6, '2025-06-03 19:05:40', 100.00, 'Cartão', 'completo'),
(2, 6, 6, '2025-06-03 19:44:28', 100.00, 'Cartão', 'completo'),
(3, 6, 5, '2025-06-03 19:49:38', 1000.00, 'Cartão', 'completo'),
(4, 6, 5, '2025-06-04 18:46:56', 1000.00, 'Cartão', 'completo'),
(5, 6, 6, '2025-06-04 18:46:56', 100.00, 'Cartão', 'completo'),
(6, 9, 7, '2025-06-04 19:46:49', 153.00, 'Cartão', 'completo');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`);

--
-- Índices de tabela `imagens`
--
ALTER TABLE `imagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Índices de tabela `image_categoria`
--
ALTER TABLE `image_categoria`
  ADD PRIMARY KEY (`id_imagem`,`id_categoria`),
  ADD KEY `id_categoria` (`id_categoria`);

--
-- Índices de tabela `pagamento`
--
ALTER TABLE `pagamento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_venda` (`id_venda`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_comprador` (`id_comprador`),
  ADD KEY `id_imagem` (`id_imagem`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `imagens`
--
ALTER TABLE `imagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `pagamento`
--
ALTER TABLE `pagamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `imagens`
--
ALTER TABLE `imagens`
  ADD CONSTRAINT `imagens_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `image_categoria`
--
ALTER TABLE `image_categoria`
  ADD CONSTRAINT `image_categoria_ibfk_1` FOREIGN KEY (`id_imagem`) REFERENCES `imagens` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `image_categoria_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pagamento`
--
ALTER TABLE `pagamento`
  ADD CONSTRAINT `pagamento_ibfk_1` FOREIGN KEY (`id_venda`) REFERENCES `vendas` (`id`);

--
-- Restrições para tabelas `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`id_comprador`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `vendas_ibfk_2` FOREIGN KEY (`id_imagem`) REFERENCES `imagens` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
