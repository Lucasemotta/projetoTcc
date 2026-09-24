<?php

$servidor = "localhost";
$usuario = "root";
$senha = "";
$porta = 3306;

$conn = new mysqli(
    $servidor,
    $usuario,
    $senha,
    "",
    $porta
);

if ($conn->connect_error) {
    die("Erro na conexão com o MySQL: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Cria o banco caso não exista
$sqlBanco = "CREATE DATABASE IF NOT EXISTS cortai";

if (!$conn->query($sqlBanco)) {
    die("Erro ao criar o banco: " . $conn->error);
}

// Seleciona o banco
$conn->select_db("cortai");