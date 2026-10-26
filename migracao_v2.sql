-- Cortaí — migração para quem JÁ tem o banco cortai_db criado.
-- Rode uma vez (phpMyAdmin > cortai_db > SQL). Pode rodar de novo sem problema.
-- Feito para MariaDB (o do XAMPP). 
USE cortai_db;

-- 1) Coluna do login: o código usa "usuario"; o banco antigo tinha "login".
--    (se você já renomeou na mão, esta linha não faz nada)
ALTER TABLE usuarios CHANGE COLUMN IF EXISTS login usuario VARCHAR(50) NOT NULL;
ALTER TABLE usuarios ADD UNIQUE INDEX IF NOT EXISTS uq_usuarios_usuario (usuario);

-- 2) Vínculo barbeiro <-> serviço
CREATE TABLE IF NOT EXISTS barbeiro_servico (
    id_barbeiro INT NOT NULL,
    id_servico  INT NOT NULL,
    PRIMARY KEY (id_barbeiro, id_servico),
    FOREIGN KEY (id_barbeiro) REFERENCES barbeiros(id_barbeiro) ON DELETE CASCADE,
    FOREIGN KEY (id_servico)  REFERENCES servicos(id_servico)  ON DELETE CASCADE
);

-- Ponto de partida: todo barbeiro já cadastrado presta todos os serviços (nada some da agenda).
-- Depois ajuste em Painel > Barbeiros, ou: DELETE FROM barbeiro_servico WHERE id_barbeiro=2 AND id_servico=3;
INSERT IGNORE INTO barbeiro_servico (id_barbeiro, id_servico)
SELECT b.id_barbeiro, s.id_servico FROM barbeiros b CROSS JOIN servicos s;

-- 3) Índice para a consulta de horários ocupados
ALTER TABLE agendamentos ADD INDEX IF NOT EXISTS idx_agend_barbeiro_data (id_barbeiro, data_agendamento, horario);
