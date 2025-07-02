-- Criação da tabela `clientes`
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nomeCliente` VARCHAR(300) DEFAULT 'sem nome',
  `cpf` VARCHAR(11) DEFAULT 'sem cpf',
  `endereco` VARCHAR(300) DEFAULT 'sem endereco',
  `telefone` VARCHAR(11) DEFAULT 'nao possui',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Criação da tabela `livros`
CREATE TABLE IF NOT EXISTS `livros` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(500) DEFAULT 'nao preencheu',
  `isbn` VARCHAR(50) DEFAULT 'sem isbn',
  `editora` VARCHAR(200) DEFAULT 'sem editora',
  `lancamento` DATE DEFAULT NULL,
  `disponivel` TINYINT(1) DEFAULT '1',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Criação da tabela `emprestimo`
CREATE TABLE IF NOT EXISTS `emprestimo` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `idCliente` INT DEFAULT NULL,
  `idLivro` INT DEFAULT NULL,
  `criadoEm` DATE DEFAULT NULL,
  `renovadoEm` DATE DEFAULT NULL,
  `vencimento` DATE DEFAULT NULL,
  `devolvidoEm` DATE DEFAULT NULL,
  `ativo` TINYINT(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idCliente` (`idCliente`),
  KEY `idLivro` (`idLivro`),
  CONSTRAINT `emprestimo_ibfk_1` FOREIGN KEY (`idCliente`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `emprestimo_ibfk_2` FOREIGN KEY (`idLivro`) REFERENCES `livros` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Criação da tabela `usuarios`
CREATE TABLE IF NOT EXISTS `usuarios` (
  `ID` INT NOT NULL AUTO_INCREMENT,
  `usuario` VARCHAR(50) NOT NULL,
  `nome` VARCHAR(300) NOT NULL,
  `senha` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
