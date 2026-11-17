-- Cortaí v4: PIN do cliente. Rode uma vez no cortai_db (pode repetir).
USE cortai_db;
ALTER TABLE clientes ADD COLUMN IF NOT EXISTS pin_hash VARCHAR(255) NULL;
-- Clientes antigos (sem PIN) definem o PIN no primeiro acesso.
