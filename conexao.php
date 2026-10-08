<?php
$servidor = "localhost";
$usuario  = "root";
$senha    = "";
$banco    = "cortai";
$porta    = 3306;

// Conexão direta com a base de dados
$conn = new mysqli($servidor, $usuario, $senha, $banco, $porta);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Define o charset para evitar problemas de acentuação
$conn->set_charset("utf8mb4");
?>