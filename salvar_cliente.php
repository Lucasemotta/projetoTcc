<?php

session_start();

include "conexao.php";

$nome = $_POST['nome'];
$telefone = $_POST['telefone'];

$sql = "INSERT INTO clientes (nome, telefone)
        VALUES (?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param("ss", $nome, $telefone);

if ($stmt->execute()) {

    // Guarda o ID do cliente para usar no agendamento
    $_SESSION['id_cliente'] = $conn->insert_id;

    // Redireciona para a agenda
    header("Location: agenda.php");
    exit;

} else {

    echo "Erro ao cadastrar cliente: " . $conn->error;

}

?>