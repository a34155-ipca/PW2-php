-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 02/03/2026 às 11:38
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
-- Banco de dados: `pw2`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cursos`
--

CREATE TABLE `cursos` (
  `ID` int(11) NOT NULL,
  `NOME_CURSO` varchar(30) NOT NULL,
  `SIGLA` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cursos`
--

INSERT INTO `cursos` (`ID`, `NOME_CURSO`, `SIGLA`) VALUES
(1, 'Desenvolvimento web e multimed', 'DWM'),
(2, 'comercio eletronico', 'CE');

-- --------------------------------------------------------

--
-- Estrutura para tabela `disciplinas`
--

CREATE TABLE `disciplinas` (
  `ID` int(11) NOT NULL,
  `NOME_DISCIPLINA` varchar(30) NOT NULL,
  `SIGLA` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `disciplinas`
--

INSERT INTO `disciplinas` (`ID`, `NOME_DISCIPLINA`, `SIGLA`) VALUES
(1, 'MATEMATICA', 'MAT'),
(2, 'PROGRAMAÇÃO WEB 1', 'PW1');

-- --------------------------------------------------------

--
-- Estrutura para tabela `plano_estudo`
--

CREATE TABLE `plano_estudo` (
  `CURSO` int(11) NOT NULL,
  `DISCILINA` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `plano_estudo`
--

INSERT INTO `plano_estudo` (`CURSO`, `DISCILINA`) VALUES
(1, 1),
(1, 2);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`ID`);

--
-- Índices de tabela `disciplinas`
--
ALTER TABLE `disciplinas`
  ADD PRIMARY KEY (`ID`);

--
-- Índices de tabela `plano_estudo`
--
ALTER TABLE `plano_estudo`
  ADD PRIMARY KEY (`CURSO`,`DISCILINA`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
