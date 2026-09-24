<?php

session_start();

include "conexao.php";


if (!isset($_SESSION['id_agendamento'])) {

    header("Location: index.php");
    exit;

}


$id_agendamento = $_SESSION['id_agendamento'];


$sql = "

SELECT

    a.id_agendamento,

    a.data_agendamento,

    a.horario,

    a.status,

    c.nome AS cliente,

    c.telefone,

    b.nome AS barbeiro,

    s.nome AS servico,

    s.valor

FROM agendamentos a

INNER JOIN clientes c
    ON a.id_cliente = c.id_cliente

INNER JOIN barbeiros b
    ON a.id_barbeiro = b.id_barbeiro

INNER JOIN servicos s
    ON a.id_servico = s.id_servico

WHERE a.id_agendamento = ?

";


$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id_agendamento);

$stmt->execute();

$resultado = $stmt->get_result();


if ($resultado->num_rows == 0) {

    echo "Agendamento não encontrado.";
    exit;

}


$agendamento = $resultado->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Cortaí - Agendamento Confirmado</title>

<link 
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


<style>

body {
    background-color: #111516;
    color: white;
    font-family: Arial, sans-serif;
}

.container {
    margin-top: 60px;
}

.card-resumo {
    background-color: #191e1f;
    border: 1px solid #303a3b;
    border-radius: 15px;
    padding: 40px;
}

.titulo {
    color: #719999;
}

.item {
    border-bottom: 1px solid #303a3b;
    padding: 12px 0;
}

.valor {
    color: #86aaaa;
    font-size: 22px;
    font-weight: bold;
}

.btn-cortai {
    background-color: #719999;
    color: #111516;
    font-weight: bold;
    border: none;
}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-md-7">

<div class="card-resumo">

<h2 class="titulo text-center mb-4">

Agendamento confirmado!

</h2>


<div class="item">

<strong>Cliente:</strong>

<?= htmlspecialchars($agendamento['cliente']); ?>

</div>


<div class="item">

<strong>Telefone:</strong>

<?= htmlspecialchars($agendamento['telefone']); ?>

</div>


<div class="item">

<strong>Serviço:</strong>

<?= htmlspecialchars($agendamento['servico']); ?>

</div>


<div class="item">

<strong>Barbeiro:</strong>

<?= htmlspecialchars($agendamento['barbeiro']); ?>

</div>


<div class="item">

<strong>Data:</strong>

<?= date(
    "d/m/Y",
    strtotime($agendamento['data_agendamento'])
); ?>

</div>


<div class="item">

<strong>Horário:</strong>

<?= date(
    "H:i",
    strtotime($agendamento['horario'])
); ?>

</div>


<div class="item">

<strong>Valor:</strong>

<span class="valor">

R$ <?= number_format(
    $agendamento['valor'],
    2,
    ',',
    '.'
); ?>

</span>

</div>


<div class="text-center mt-4">

<a href="index.php" class="btn btn-cortai">

VOLTAR AO INÍCIO

</a>

</div>


</div>

</div>

</div>

</div>

</body>

</html>