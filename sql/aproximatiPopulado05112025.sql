-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 05/11/2025 às 16:05
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
-- Banco de dados: `aproximati`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `atendimentos`
--

CREATE TABLE `atendimentos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `tecnico_id` int(11) NOT NULL,
  `servico_id` int(11) NOT NULL,
  `status` enum('pendente','em_andamento','concluido','cancelado') DEFAULT 'pendente',
  `data_atendimento` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `atendimentos`
--

INSERT INTO `atendimentos` (`id`, `cliente_id`, `tecnico_id`, `servico_id`, `status`, `data_atendimento`) VALUES
(1, 5, 2, 1, 'concluido', '2024-01-15 10:00:00'),
(2, 5, 2, 2, 'concluido', '2024-01-20 14:30:00'),
(5, 5, 2, 1, 'concluido', '2025-11-05 10:59:45'),
(6, 5, 8, 7, 'concluido', '2025-11-05 11:52:47');

-- --------------------------------------------------------

--
-- Estrutura para tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `tecnico_id` int(11) NOT NULL,
  `atendimento_id` int(11) NOT NULL,
  `nota` int(11) DEFAULT NULL,
  `comentario` text DEFAULT NULL,
  `resposta_tecnico` text DEFAULT NULL,
  `data_avaliacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `avaliacoes`
--

INSERT INTO `avaliacoes` (`id`, `cliente_id`, `tecnico_id`, `atendimento_id`, `nota`, `comentario`, `resposta_tecnico`, `data_avaliacao`) VALUES
(4, 5, 2, 1, 5, 'Otimo técnico', 'Muito obrigado', '2025-11-05 10:49:00'),
(5, 5, 8, 6, 1, 'Montou o pc, mas agora não funciona', 'ligou na tomada?\r\n', '2025-11-05 12:02:38');

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL,
  `atendimento_id` int(11) NOT NULL,
  `remetente_id` int(11) NOT NULL,
  `mensagem` text NOT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `data_envio` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `mensagens`
--

INSERT INTO `mensagens` (`id`, `atendimento_id`, `remetente_id`, `mensagem`, `lida`, `data_envio`) VALUES
(1, 5, 2, 'Oi, como posso entrar em contato', 1, '2025-11-05 11:00:21'),
(2, 5, 5, 'Oi, meu endereço é tal, queria que formatasse meu pc', 1, '2025-11-05 11:00:57'),
(3, 5, 2, 'perfeito, estarei ai amanhã', 0, '2025-11-05 11:01:24');

-- --------------------------------------------------------

--
-- Estrutura para tabela `portfolio`
--

CREATE TABLE `portfolio` (
  `id` int(11) NOT NULL,
  `tecnico_id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `imagem_url` varchar(255) DEFAULT NULL,
  `data_publicacao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `portfolio`
--

INSERT INTO `portfolio` (`id`, `tecnico_id`, `titulo`, `descricao`, `imagem_url`, `data_publicacao`) VALUES
(7, 2, 'Formatando um pc', '', '690b518a847b9.jpg', '2025-11-05 10:30:50'),
(8, 2, 'Reparos', '', '690b5199499f3.jpg', '2025-11-05 10:31:05'),
(9, 2, 'Montando um PC', '', '690b51ac36f0a.jpg', '2025-11-05 10:31:24'),
(10, 2, 'Otima ferramente', '', '690b51b610c06.jpg', '2025-11-05 10:31:34'),
(11, 8, 'Mais um Notebook limpo', '', '690b59abc8b82.jpg', '2025-11-05 11:05:31'),
(12, 3, 'Precisa limpaa!', '', '690b5a061ae28.jpg', '2025-11-05 11:07:02'),
(13, 3, 'Montei mais um', '', '690b5a2be837a.jpg', '2025-11-05 11:07:39');

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--

CREATE TABLE `servicos` (
  `id` int(11) NOT NULL,
  `tecnico_id` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `servicos`
--

INSERT INTO `servicos` (`id`, `tecnico_id`, `titulo`, `descricao`, `preco`) VALUES
(1, 2, 'Formatação e Instalação de Sistema', 'Formatação completa do computador com instalação do Windows e programas básicos', 80.00),
(2, 2, 'Limpeza e Manutenção Preventiva', 'Limpeza física do computador, troca de pasta térmica e otimização do sistema', 50.00),
(7, 8, 'Manutenção de Notebooks', '', 120.00),
(9, 3, 'Limpeza de Pc', '', 100.00),
(10, 3, 'Montagem de PC', '', 150.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('tecnico','cliente','admin') NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `especialidade` varchar(255) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`, `telefone`, `cidade`, `estado`, `foto_perfil`, `especialidade`, `data_cadastro`) VALUES
(1, 'Administrador', 'admin@aproximati.com', '$2y$10$rIGRGlbwHTMkTQYe7iFsAuUteeN4aLxlT5sg/JF/lw4yrMd31jWOS', 'admin', '(42) 99999-9999', 'Ponta Grossa', 'PR', NULL, NULL, '2025-10-21 09:45:23'),
(2, 'João Silva', 'joao@tecnico.com', '$2y$10$nZxo0EV/jtrzbaU.1x1hTeQSgyieVNPDQgAzx8gSr2d/uhw4ypqre', 'tecnico', '(42) 99999-0001', 'Ponta Grossa', 'PR', 'avatar_2_690b512fdab9a.jpg', 'Manutenção de Computadores', '2025-10-21 09:45:23'),
(3, 'Maria Santos', 'maria@tecnico.com', '$2y$10$KDWaH5MWrCZPtBJzf/wbEOYsiwbVfXXcD5kYOZ9yvgdy2uHf49LPG', 'tecnico', '(42) 99999-0002', 'Ponta Grossa', 'PR', 'avatar_3_690b59e777915.jpg', 'Desenvolvimento Web', '2025-10-21 09:45:23'),
(5, 'Ana Oliveira', 'ana@cliente.com', '$2y$10$bT8Qdm6dFvY9QaNaqxFnXuIcpfC7xtBrkTF5M4OtkM1jUMoI/S.2.', 'cliente', '(42) 99999-1001', 'Ponta Grossa', 'PR', 'avatar_5_690b55b208343.jpg', NULL, '2025-10-21 09:45:23'),
(6, 'Carlos Ferreira', 'carlos@cliente.com', '827ccb0eea8a706c4c34a16891f84e7b', 'cliente', '(42) 99999-1002', 'Ponta Grossa', 'PR', NULL, NULL, '2025-10-21 09:45:23'),
(7, 'Fernando de Gois', 'fernando@gmail.com', '$2y$10$7HlyWHdjQNsDgZ87vEkxcu5GTI8uzLjdpS.GNzGK9pi67svIaY5ZC', 'cliente', '42984427094', 'Ponta Grossa', 'PR', NULL, '', '2025-10-21 09:47:15'),
(8, 'Fernando de Gois', 'fernandotec@gmail.com', '$2y$10$DJuYF14kv5aWznIPehoLbeJy.cbnRY9tlN4Hvx652v0ghuXwpfcHC', 'tecnico', '4298456464', 'Ponta Grossa', 'PR', 'avatar_8_690b59908555d.jpg', 'Manutenção de PC', '2025-10-21 09:51:19'),
(9, 'Pedro da Silva', 'pedro@tecnico.com', '$2y$10$d1uWW.A.FMzfPE3c4WQrCOUyf.nsYvHxfuBnMR6c5THZqyuBTWYyK', 'tecnico', NULL, NULL, NULL, NULL, NULL, '2025-11-05 11:31:22');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `tecnico_id` (`tecnico_id`),
  ADD KEY `servico_id` (`servico_id`);

--
-- Índices de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cliente_id` (`cliente_id`),
  ADD KEY `tecnico_id` (`tecnico_id`),
  ADD KEY `atendimento_id` (`atendimento_id`);

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `atendimento_id` (`atendimento_id`),
  ADD KEY `remetente_id` (`remetente_id`),
  ADD KEY `data_envio` (`data_envio`);

--
-- Índices de tabela `portfolio`
--
ALTER TABLE `portfolio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tecnico_id` (`tecnico_id`);

--
-- Índices de tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tecnico_id` (`tecnico_id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `atendimentos`
--
ALTER TABLE `atendimentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `portfolio`
--
ALTER TABLE `portfolio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `atendimentos`
--
ALTER TABLE `atendimentos`
  ADD CONSTRAINT `atendimentos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `atendimentos_ibfk_2` FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `atendimentos_ibfk_3` FOREIGN KEY (`servico_id`) REFERENCES `servicos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `avaliacoes_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `avaliacoes_ibfk_2` FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `avaliacoes_ibfk_3` FOREIGN KEY (`atendimento_id`) REFERENCES `atendimentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `mensagens`
--
ALTER TABLE `mensagens`
  ADD CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`atendimento_id`) REFERENCES `atendimentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`remetente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `portfolio`
--
ALTER TABLE `portfolio`
  ADD CONSTRAINT `portfolio_ibfk_1` FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `servicos`
--
ALTER TABLE `servicos`
  ADD CONSTRAINT `servicos_ibfk_1` FOREIGN KEY (`tecnico_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
