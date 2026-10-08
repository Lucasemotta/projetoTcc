<?php
session_start();
include "conexao.php";

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = trim($_POST['login'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (!empty($login) && !empty($senha)) {
        $sql = "SELECT * FROM usuarios WHERE login = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            
            // Verifica a senha (compatível com texto simples e md5/hash)
            if ($senha === $usuario['senha'] || password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id_usuario'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                header("Location: dashboard.php");
                exit;
            } else {
                $erro = "Senha incorreta.";
            }
        } else {
            $erro = "Utilizador não encontrado.";
        }
    } else {
        $erro = "Preencha todos os campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Login Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #111516; color: white; font-family: Arial, sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .card-login { background-color: #191e1f; border: 1px solid #303a3b; border-radius: 12px; padding: 40px; width: 100%; max-width: 400px; }
        .titulo { text-align: center; margin-bottom: 25px; font-weight: bold; }
        .titulo span { color: #719999; }
        .form-control { background-color: #111516; border: 1px solid #3b4547; color: white; }
        .form-control:focus { background-color: #111516; color: white; border-color: #719999; box-shadow: none; }
        .btn-cortai { background-color: #719999; color: #111516; font-weight: bold; border: none; width: 100%; padding: 12px; margin-top: 10px; }
        .btn-cortai:hover { background-color: #86aaaa; }
        .alert { background-color: #3d1c1d; border-color: #842029; color: #ea868f; padding: 10px; border-radius: 6px; font-size: 14px; text-align: center; }
    </style>
</head>
<body>

<div class="card-login">
    <h3 class="titulo">Painel <span>Cortaí</span></h3>

    <?php if (!empty($erro)): ?>
        <div class="alert mb-3"><?= $erro; ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="mb-3">
            <label class="form-label">Utilizador</label>
            <input type="text" name="login" class="form-control" required placeholder="admin">
        </div>
        <div class="mb-4">
            <label class="form-label">Senha</label>
            <input type="password" name="senha" class="form-control" required placeholder="******">
        </div>
        <button type="submit" class="btn btn-cortai">ENTRAR</button>
    </form>
</div>

</body>
</html>