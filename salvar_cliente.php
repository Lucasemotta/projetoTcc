<?php

// Conexão com o banco
include "conexao.php";


// Recebendo os dados do formulário

$nome = $_POST['nome'];

$telefone = $_POST['telefone'];

$email = $_POST['email'];



// Inserindo no banco

$sql = "INSERT INTO clientes
(
    nome,
    telefone,
    email
)

VALUES
(
    '$nome',
    '$telefone',
    '$email'
)";



$resultado = mysqli_query($conexao, $sql);



// Verificando se salvou

if($resultado){

    echo "

    <script>

        alert('Cliente cadastrado com sucesso!');

        window.location.href='clientes.php';

    </script>

    ";

}
else{

    echo "

    <script>

        alert('Erro ao cadastrar cliente!');

        window.history.back();

    </script>

    ";

}


?>