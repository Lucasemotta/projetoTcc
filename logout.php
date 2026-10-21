<?php
session_start();

// Limpa todas as variáveis da sessão
session_unset();

// Destrói a sessão ativa
session_destroy();

// Redireciona de volta para a tela de login
header("Location: login.php");
exit;
?>