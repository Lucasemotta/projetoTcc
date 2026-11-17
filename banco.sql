-- Cortaí — esquema completo do banco (instalação nova)
-- Para quem JÁ tem o banco criado, use migracao_v2.sql em vez deste arquivo.

CREATE DATABASE IF NOT EXISTS cortai_db CHARACTER SET utf8mb4;
USE cortai_db;

CREATE TABLE clientes (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE barbeiros (
    id_barbeiro INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20),
    especialidade VARCHAR(100),
    status BOOLEAN DEFAULT TRUE
);

CREATE TABLE servicos (
    id_servico INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255),
    valor DECIMAL(10,2) NOT NULL,
    duracao INT NOT NULL
);

-- Quais serviços cada barbeiro realiza (a agenda só mostra barbeiros que prestam o serviço escolhido)
CREATE TABLE barbeiro_servico (
    id_barbeiro INT NOT NULL,
    id_servico  INT NOT NULL,
    PRIMARY KEY (id_barbeiro, id_servico),
    FOREIGN KEY (id_barbeiro) REFERENCES barbeiros(id_barbeiro) ON DELETE CASCADE,
    FOREIGN KEY (id_servico)  REFERENCES servicos(id_servico)  ON DELETE CASCADE
);

CREATE TABLE agendamentos (
    id_agendamento INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_barbeiro INT NOT NULL,
    id_servico INT NOT NULL,
    data_agendamento DATE NOT NULL,
    horario TIME NOT NULL,
    status VARCHAR(30) DEFAULT 'Agendado',
    FOREIGN KEY (id_cliente)  REFERENCES clientes(id_cliente),
    FOREIGN KEY (id_barbeiro) REFERENCES barbeiros(id_barbeiro),
    FOREIGN KEY (id_servico)  REFERENCES servicos(id_servico),
    INDEX idx_agend_barbeiro_data (id_barbeiro, data_agendamento, horario)
);

-- Contas dos barbeiros/administradores (a coluna se chama "usuario", como o login espera)
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100),
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(255)
);

-- v3: várias barbearias
CREATE TABLE IF NOT EXISTS barbearias (
    id_barbearia INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario   INT NOT NULL UNIQUE,            -- dono: cada conta de barbeiro administra uma barbearia
    nome         VARCHAR(100) NOT NULL,
    endereco     VARCHAR(255) NOT NULL,
    cidade       VARCHAR(80),
    telefone     VARCHAR(20),
    latitude     DECIMAL(10,7) NOT NULL,
    longitude    DECIMAL(10,7) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);
ALTER TABLE barbeiros ADD COLUMN IF NOT EXISTS id_barbearia INT NULL;
ALTER TABLE servicos  ADD COLUMN IF NOT EXISTS id_barbearia INT NULL;

-- v4: PIN do cliente
ALTER TABLE clientes ADD COLUMN IF NOT EXISTS pin_hash VARCHAR(255) NULL;

-- v5: perfil da barbearia e avaliações
ALTER TABLE barbearias ADD COLUMN IF NOT EXISTS descricao VARCHAR(500) NULL, ADD COLUMN IF NOT EXISTS foto VARCHAR(255) NULL;
CREATE TABLE IF NOT EXISTS avaliacoes (
    id_avaliacao INT AUTO_INCREMENT PRIMARY KEY,
    id_agendamento INT NOT NULL UNIQUE,          -- 1 avaliação por atendimento
    id_cliente INT NOT NULL,
    id_barbearia INT NOT NULL,
    nota TINYINT NOT NULL,
    comentario VARCHAR(300) NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_aval_barbearia (id_barbearia)
);
