-- Script para adicionar a funcionalidade de chat ao banco de dados existente
-- Execute este script se você já tem o banco de dados criado

USE aproximati;

-- --------------------------------------------------------

--
-- Estrutura da tabela `mensagens`
--
CREATE TABLE IF NOT EXISTS `mensagens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `atendimento_id` int(11) NOT NULL,
  `remetente_id` int(11) NOT NULL,
  `mensagem` text NOT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `data_envio` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `atendimento_id` (`atendimento_id`),
  KEY `remetente_id` (`remetente_id`),
  KEY `data_envio` (`data_envio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Adicionando as chaves estrangeiras (Constraints)
--

-- Remove constraints existentes se houver (pode dar erro se não existir, mas não é problema)
ALTER TABLE `mensagens` DROP FOREIGN KEY IF EXISTS `mensagens_ibfk_1`;
ALTER TABLE `mensagens` DROP FOREIGN KEY IF EXISTS `mensagens_ibfk_2`;

-- Adiciona as constraints
ALTER TABLE `mensagens`
  ADD CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`atendimento_id`) REFERENCES `atendimentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`remetente_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;

