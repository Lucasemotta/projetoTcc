<?php

// Dados do MySQL
$servidor = "localhost";
$usuario = "root";
$senha = "";


// Criando conexão inicial sem banco
$conexao = mysqli_connect(
    $servidor,
    $usuario,
    $senha
);


if (!$conexao) {

    die("Erro ao conectar no MySQL: " . mysqli_connect_error());

}


// Criar banco caso não exista

$sqlBanco = "CREATE DATABASE IF NOT EXISTS cortai";

mysqli_query($conexao, $sqlBanco);


// Selecionar banco

mysqli_select_db($conexao, "cortai");



// =============================
// CRIAÇÃO DAS TABELAS
// =============================



$sqlClientes = "

CREATE TABLE IF NOT EXISTS clientes (

    id_cliente INT AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(100) NOT NULL,

    telefone VARCHAR(20) NOT NULL,

    email VARCHAR(100),

    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP

)

";

mysqli_query($conexao, $sqlClientes);





$sqlBarbeiros = "

CREATE TABLE IF NOT EXISTS barbeiros (

    id_barbeiro INT AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(100) NOT NULL,

    telefone VARCHAR(20),

    especialidade VARCHAR(100),

    status BOOLEAN DEFAULT TRUE

)

";

mysqli_query($conexao, $sqlBarbeiros);





$sqlServicos = "

CREATE TABLE IF NOT EXISTS servicos (

    id_servico INT AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(100) NOT NULL,

    descricao VARCHAR(255),

    valor DECIMAL(10,2) NOT NULL,

    duracao INT NOT NULL

)

";

mysqli_query($conexao, $sqlServicos);





$sqlUsuarios = "

CREATE TABLE IF NOT EXISTS usuarios (

    id_usuario INT AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(100),

    login VARCHAR(50),

    senha VARCHAR(255)

)

";

mysqli_query($conexao, $sqlUsuarios);





$sqlAgendamentos = "

CREATE TABLE IF NOT EXISTS agendamentos (

    id_agendamento INT AUTO_INCREMENT PRIMARY KEY,

    id_cliente INT NOT NULL,

    id_barbeiro INT NOT NULL,

    id_servico INT NOT NULL,

    data_agendamento DATE NOT NULL,

    horario TIME NOT NULL,

    status VARCHAR(30) DEFAULT 'Agendado',


    FOREIGN KEY (id_cliente)

    REFERENCES clientes(id_cliente),


    FOREIGN KEY (id_barbeiro)

    REFERENCES barbeiros(id_barbeiro),


    FOREIGN KEY (id_servico)

    REFERENCES servicos(id_servico)

)

";

mysqli_query($conexao, $sqlAgendamentos);



// Configuração de caracteres

mysqli_set_charset($conexao, "utf8");



?>