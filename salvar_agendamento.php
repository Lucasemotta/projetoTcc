<?php

session_start();

include "conexao.php";


if (!isset($_SESSION['id_cliente'])) {

    header("Location: cadastro_cliente.php");
    exit;

}


$id_cliente = $_SESSION['id_cliente'];

$id_servico = $_POST['id_servico'];

$id_barbeiro = $_POST['id_barbeiro'];

$data_agendamento = $_POST['data_agendamento'];

$horario = $_POST['horario'];


/*
|--------------------------------------------------------------------------
| Verificar se o horário já está ocupado
|--------------------------------------------------------------------------
*/

$sqlVerifica = "

SELECT id_agendamento

FROM agendamentos

WHERE id_barbeiro = ?

AND data_agendamento = ?

AND horario = ?

AND status = 'Agendado'

";


$stmtVerifica = $conn->prepare($sqlVerifica);

$stmtVerifica->bind_param(
    "iss",
    $id_barbeiro,
    $data_agendamento,
    $horario
);

$stmtVerifica->execute();

$resultado = $stmtVerifica->get_result();


if ($resultado->num_rows > 0) {

    echo "

    <script>

    alert('Esse horário já está ocupado para este barbeiro.');

    window.location.href = 'agenda.php';

    </script>

    ";

    exit;

}


/*
|--------------------------------------------------------------------------
| Salvar agendamento
|--------------------------------------------------------------------------
*/

$sql = "

INSERT INTO agendamentos

(id_cliente, id_barbeiro, id_servico, data_agendamento, horario)

VALUES (?, ?, ?, ?, ?)

";


$stmt = $conn->prepare($sql);


$stmt->bind_param(
    "iiiss",
    $id_cliente,
    $id_barbeiro,
    $id_servico,
    $data_agendamento,
    $horario
);


if ($stmt->execute()) {

    $_SESSION['id_agendamento'] = $conn->insert_id;

    header("Location: resumo.php");

    exit;

} else {

    echo "Erro ao realizar agendamento: " . $conn->error;

}

?>