<?php
session_start();
include "conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: agenda.php");
    exit;
}

$id_cliente = $_SESSION['id_cliente'] ?? null;
$id_servico = $_POST['id_servico'] ?? null;
$id_barbeiro = $_POST['id_barbeiro'] ?? null;
$data_agendamento = $_POST['data_agendamento'] ?? null;
$horario = $_POST['horario'] ?? null;

if (!$id_cliente || !$id_servico || !$id_barbeiro || !$data_agendamento || !$horario) {
    echo "<script>
        alert('Por favor, preencha todos os campos do agendamento.');
        window.location.href = 'agenda.php';
    </script>";
    exit;
}

// Verifica se o barbeiro já possui agendamento no mesmo dia e horário
$sqlVerifica = "SELECT id_agendamento FROM agendamentos 
                WHERE id_barbeiro = ? AND data_agendamento = ? AND horario = ? AND status != 'Cancelado' 
                LIMIT 1";
$stmtVerifica = $conn->prepare($sqlVerifica);
$stmtVerifica->bind_param("iss", $id_barbeiro, $data_agendamento, $horario);
$stmtVerifica->execute();
$resVerifica = $stmtVerifica->get_result();

if ($resVerifica->num_rows > 0) {
    echo "<script>
        alert('Este horário já está ocupado para o barbeiro selecionado. Por favor, escolha outro horário ou barbeiro.');
        window.location.href = 'agenda.php';
    </script>";
    exit;
}

// Salva o novo agendamento
$sqlSalvar = "INSERT INTO agendamentos (id_cliente, id_barbeiro, id_servico, data_agendamento, horario, status) 
              VALUES (?, ?, ?, ?, ?, 'Agendado')";
$stmtSalvar = $conn->prepare($sqlSalvar);
$stmtSalvar->bind_param("iiiss", $id_cliente, $id_barbeiro, $id_servico, $data_agendamento, $horario);

if ($stmtSalvar->execute()) {
    $id_agendamento = $conn->insert_id;
    header("Location: resumo.php?id=" . $id_agendamento);
    exit;
} else {
    die("Erro ao salvar agendamento: " . $stmtSalvar->error);
}
?>