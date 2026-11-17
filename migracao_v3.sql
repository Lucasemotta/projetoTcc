-- Cortaí v3 (várias barbearias). Rode uma vez no cortai_db. Pode repetir sem problema.
USE cortai_db;
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

-- Dados antigos: a 1ª conta existente ganha uma barbearia provisória (edite em Painel > Barbearia)
-- e passa a ser dona dos barbeiros e serviços que já existiam.
INSERT INTO barbearias (id_usuario, nome, endereco, cidade, latitude, longitude)
SELECT MIN(id_usuario), 'Minha Barbearia', 'Endereço a definir', '', -23.5505000, -46.6333000
FROM usuarios WHERE NOT EXISTS (SELECT 1 FROM barbearias) HAVING COUNT(*) > 0;
UPDATE barbeiros SET id_barbearia = (SELECT MIN(id_barbearia) FROM barbearias) WHERE id_barbearia IS NULL;
UPDATE servicos  SET id_barbearia = (SELECT MIN(id_barbearia) FROM barbearias) WHERE id_barbearia IS NULL;
