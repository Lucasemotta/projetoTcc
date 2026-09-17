<?php

include "conexao.php";

$nome = $_POST['nome'];
$telefone = $_POST['telefone'];
$especialidade = $_POST['especialidade'];
$status = $_POST['status'];

$sql = "INSERT INTO barbeiros 
        (nome, telefone, especialidade, status)
        VALUES 
        ('$nome', '$telefone', '$especialidade', '$status')";

if (mysqli_query($conexao, $sql)) {

    header("Location: cadastro_barbeiro.php?sucesso=1");
    exit;

} else {

    echo "Erro ao cadastrar barbeiro: " . mysqli_error($conexao);

}

?>