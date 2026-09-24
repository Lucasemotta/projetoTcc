<?php

$servidor = "localhost";
$usuario = "root";
$senha = "";

/*
|--------------------------------------------------------------------------
| Conexão inicial com o MySQL
|--------------------------------------------------------------------------
*/

$conn = new mysqli(
    $servidor,
    $usuario,
    $senha
);

if ($conn->connect_error) {
    die("Erro na conexão com o MySQL: " . $conn->connect_error);
}


/*
|--------------------------------------------------------------------------
| Criar banco caso não exista
|--------------------------------------------------------------------------
*/

$sqlBanco = "CREATE DATABASE IF NOT EXISTS cortai";

if (!$conn->query($sqlBanco)) {
    die("Erro ao criar o banco: " . $conn->error);
}


/*
|--------------------------------------------------------------------------
| Selecionar banco
|--------------------------------------------------------------------------
*/

$conn->select_db("cortai");


/*
|--------------------------------------------------------------------------
| Configuração de caracteres
|--------------------------------------------------------------------------
*/

$conn->set_charset("utf8mb4");


/*
|--------------------------------------------------------------------------
| Criar tabela clientes
|--------------------------------------------------------------------------
*/

$conn->query("

CREATE TABLE IF NOT EXISTS clientes (

    id_cliente INT AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(100) NOT NULL,

    telefone VARCHAR(20) NOT NULL,

    email VARCHAR(100),

    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP

)

");


/*
|--------------------------------------------------------------------------
| Criar tabela barbeiros
|--------------------------------------------------------------------------
*/

$conn->query("

CREATE TABLE IF NOT EXISTS barbeiros (

    id_barbeiro INT AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(100) NOT NULL,

    telefone VARCHAR(20),

    especialidade VARCHAR(100),

    status BOOLEAN DEFAULT TRUE

)

");


/*
|--------------------------------------------------------------------------
| Criar tabela servicos
|--------------------------------------------------------------------------
*/

$conn->query("

CREATE TABLE IF NOT EXISTS servicos (

    id_servico INT AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(100) NOT NULL,

    descricao VARCHAR(255),

    valor DECIMAL(10,2) NOT NULL,

    duracao INT NOT NULL

)

");


/*
|--------------------------------------------------------------------------
| Criar tabela usuarios
|--------------------------------------------------------------------------
*/

$conn->query("

CREATE TABLE IF NOT EXISTS usuarios (

    id_usuario INT AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(100),

    login VARCHAR(50),

    senha VARCHAR(255)

)

");


/*
|--------------------------------------------------------------------------
| Criar tabela agendamentos
|--------------------------------------------------------------------------
*/

$conn->query("

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

");

?>