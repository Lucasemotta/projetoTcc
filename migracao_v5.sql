-- Cortaí v5: descrição e foto da barbearia + avaliações. Rode uma vez no cortai_db (pode repetir).
USE cortai_db;
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
