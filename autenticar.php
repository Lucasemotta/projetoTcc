<?php
session_start();
include "conexao.php";

// Verifica se a requisição foi enviada via POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit;
}

$login = trim($_POST['login'] ?? '');
$senha = trim($_POST['senha'] ?? '');

// Validação dos campos preenchidos
if (empty($login) || empty($senha)) {
    header("Location: login.php?erro=1");
    exit;
}

/*
|--------------------------------------------------------------------------
| Autenticação Simples de Administrador / Profissional
|--------------------------------------------------------------------------
| Pode alterar o utilizador ("admin") e a palavra-passe ("123456") 
| conforme a preferência para o seu TCC.
*/
$usuarioAdmin = "admin";
$senhaAdmin   = "123456";

if ($login === $usuarioAdmin && $senha === $senhaAdmin) {
    // Regista os dados do utilizador na sessão
    $_SESSION['usuario_id']     = 1;
    $_SESSION['usuario_nome']   = "Administrador";
    $_SESSION['usuario_login']  = $login;

    // Redireciona para o Painel Principal
    header("Location: dashboard.php");
    exit;
} else {
    // Credenciais incorretas
    header("Location: login.php?erro=1");
    exit;
}
?>