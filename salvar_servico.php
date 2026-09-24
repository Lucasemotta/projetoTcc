<?php

include "conexao.php";


$nome = $_POST['nome'];
$descricao = $_POST['descricao'];
$valor = $_POST['valor'];
$duracao = $_POST['duracao'];


$sql = "INSERT INTO servicos
        (nome, descricao, valor, duracao)
        VALUES (?, ?, ?, ?)";


$stmt = $conn->prepare($sql);


$stmt->bind_param(
    "ssdi",
    $nome,
    $descricao,
    $valor,
    $duracao
);


if ($stmt->execute()) {

    header("Location: servicos.php");
    exit;

} else {

    echo "Erro ao cadastrar serviço: " . $conn->error;

}

?>