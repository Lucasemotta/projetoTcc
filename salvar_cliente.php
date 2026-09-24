<?php
 
session_start();
 
include "conexao.php";
 
$nome = $_POST['nome'];

$telefone = $_POST['telefone'];

$email = $_POST['email'];
 
 
$sql = "

    INSERT INTO clientes

    (nome, telefone, email)

    VALUES (?, ?, ?)

";
 
 
$stmt = $conn->prepare($sql);
 
 
$stmt->bind_param(

    "sss",

    $nome,

    $telefone,

    $email

);
 
 
if ($stmt->execute()) {
 
    // Guarda o ID do cliente que acabou de ser cadastrado

    $_SESSION['id_cliente'] = $conn->insert_id;
 
    // Depois do cadastro, vai para a agenda

    header("Location: agenda.php");

    exit;
 
} else {
 
    echo "Erro ao cadastrar cliente: " . $conn->error;
 
}
 
?>
 
 