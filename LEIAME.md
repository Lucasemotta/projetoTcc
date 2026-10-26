# Cortaí — projeto completo (versão 2)

Pasta autônoma: pode ser copiada para um lugar novo sem mexer no projeto antigo.

## Para rodar
1. Copie a pasta `cortai` para `htdocs` do XAMPP (ex.: `C:\xampp\htdocs\cortai_v2`).
2. Inicie Apache e MySQL no XAMPP.
3. No phpMyAdmin, importe `banco.sql` (cria o banco e as tabelas).
4. Abra http://localhost/cortai_v2/

## Para NÃO misturar com o projeto antigo (mesmo computador)
O `conexao.php` usa o banco `cortai_db`. Se o projeto antigo usa esse mesmo banco, os dois
compartilham os dados. Para separar:
- No `banco.sql`, troque `cortai_db` por `cortai_v2` (2 linhas, no começo do arquivo), e
- No `conexao.php`, troque `$banco = "cortai_db"` por `$banco = "cortai_v2"`.

## Já tem banco antigo e quer aproveitar os dados?
Use `migracao_v2.sql` no banco antigo em vez de `banco.sql` (não apaga dados).
