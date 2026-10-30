-- -- Cortaí — esquema completo do banco (instalação nova)
-- -- Para quem JÁ tem o banco criado, use migracao_v2.sql em vez deste arquivo.

-- CREATE DATABASE IF NOT EXISTS cortai_db CHARACTER SET utf8mb4;
-- USE cortai_db;

-- CREATE TABLE clientes (
--     id_cliente INT AUTO_INCREMENT PRIMARY KEY,
--     nome VARCHAR(100) NOT NULL,
--     telefone VARCHAR(20) NOT NULL,
--     email VARCHAR(100),
--     data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
-- );

-- CREATE TABLE barbeiros (
--     id_barbeiro INT AUTO_INCREMENT PRIMARY KEY,
--     nome VARCHAR(100) NOT NULL,
--     telefone VARCHAR(20),
--     especialidade VARCHAR(100),
--     status BOOLEAN DEFAULT TRUE
-- );

-- CREATE TABLE servicos (
--     id_servico INT AUTO_INCREMENT PRIMARY KEY,
--     nome VARCHAR(100) NOT NULL,
--     descricao VARCHAR(255),
--     valor DECIMAL(10,2) NOT NULL,
--     duracao INT NOT NULL
-- );

-- -- Quais serviços cada barbeiro realiza (a agenda só mostra barbeiros que prestam o serviço escolhido)
-- CREATE TABLE barbeiro_servico (
--     id_barbeiro INT NOT NULL,
--     id_servico  INT NOT NULL,
--     PRIMARY KEY (id_barbeiro, id_servico),
--     FOREIGN KEY (id_barbeiro) REFERENCES barbeiros(id_barbeiro) ON DELETE CASCADE,
--     FOREIGN KEY (id_servico)  REFERENCES servicos(id_servico)  ON DELETE CASCADE
-- );

-- CREATE TABLE agendamentos (
--     id_agendamento INT AUTO_INCREMENT PRIMARY KEY,
--     id_cliente INT NOT NULL,
--     id_barbeiro INT NOT NULL,
--     id_servico INT NOT NULL,
--     data_agendamento DATE NOT NULL,
--     horario TIME NOT NULL,
--     status VARCHAR(30) DEFAULT 'Agendado',
--     FOREIGN KEY (id_cliente)  REFERENCES clientes(id_cliente),
--     FOREIGN KEY (id_barbeiro) REFERENCES barbeiros(id_barbeiro),
--     FOREIGN KEY (id_servico)  REFERENCES servicos(id_servico),
--     INDEX idx_agend_barbeiro_data (id_barbeiro, data_agendamento, horario)
-- );

-- -- Contas dos barbeiros/administradores (a coluna se chama "usuario", como o login espera)
-- CREATE TABLE usuarios (
--     id_usuario INT AUTO_INCREMENT PRIMARY KEY,
--     nome VARCHAR(100),
--     usuario VARCHAR(50) NOT NULL UNIQUE,
--     senha VARCHAR(255)
-- );





-- 1. Tabela de Horários e Almoço do Barbeiro
CREATE TABLE IF NOT EXISTS disponibilidade_barbeiro (
    id_disponibilidade INT AUTO_INCREMENT PRIMARY KEY,
    id_barbeiro INT NOT NULL,
    dia_semana INT NOT NULL,
    horario_inicio TIME NOT NULL,
    horario_fim TIME NOT NULL,
    almoco_inicio TIME NULL,
    almoco_fim TIME NULL,
    FOREIGN KEY (id_barbeiro) REFERENCES barbeiros(id_barbeiro) ON DELETE CASCADE
);

-- 2. Tabela para Avisos de Fecho / Imprevistos da Barbearia
CREATE TABLE IF NOT EXISTS avisos_barbearia (
    id_aviso INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    mensagem TEXT NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE NOT NULL,
    status BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE IF NOT EXISTS disponibilidade_barbeiro (
    id_disponibilidade INT AUTO_INCREMENT PRIMARY KEY,
    id_barbeiro INT NOT NULL,
    dia_semana INT NOT NULL, -- 0 = Domingo, 1 = Segunda, ..., 6 = Sábado
    horario_inicio TIME NOT NULL,
    horario_fim TIME NOT NULL,
    FOREIGN KEY (id_barbeiro) REFERENCES barbeiros(id_barbeiro) ON DELETE CASCADE
);