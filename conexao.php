<?php
// Ativa a exibição de erros do PHP para facilitar o diagnóstico de problemas
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$usuario = "root";
$senha = "";
$banco = "cortai_db";

// 1. Conecta ao servidor MySQL
$conn = new mysqli($host, $usuario, $senha);

if ($conn->connect_error) {
    die("Erro ao conectar com o MySQL: " . $conn->connect_error);
}

// 2. Seleciona o banco de dados 'cortai_db'
if (!$conn->select_db($banco)) {
    die("Erro ao selecionar o banco de dados: " . $conn->error);
}
?>