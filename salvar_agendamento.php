<?php
require "agenda_lib.php";
require "conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: agenda.php");
    exit;
}

$idCliente  = exigir_cliente();
$idServico  = filter_input(INPUT_POST, 'id_servico', FILTER_VALIDATE_INT);
$idBarbeiro = filter_input(INPUT_POST, 'id_barbeiro', FILTER_VALIDATE_INT);
$data       = (string) ($_POST['data_agendamento'] ?? '');
$horario    = (string) ($_POST['horario'] ?? '');        // "HH:MM"

function voltar_com_erro(string $mensagem): void
{
    agenda_flash($mensagem);
    header("Location: agenda.php");
    exit;
}

if (!$idServico || !$idBarbeiro || $data === '' || $horario === '') {
    voltar_com_erro('Preencha serviço, barbeiro, data e horário para confirmar.');
}
if (!agenda_data_valida($data)) {
    voltar_com_erro('Escolha uma data a partir de hoje.');
}
$servico = agenda_servico($conn, $idServico);
if (!$servico || !barbeiro_presta_servico($conn, $idBarbeiro, $idServico)) {
    voltar_com_erro('Este barbeiro não realiza o serviço escolhido.');
}
$duracao = (int) $servico['duracao'];

// Bloqueia a linha do barbeiro enquanto confere e grava: dois clientes que clicam
// no mesmo instante não conseguem reservar o mesmo horário.
$conn->begin_transaction();
try {
    $lock = $conn->prepare("SELECT id_barbeiro FROM barbeiros WHERE id_barbeiro = ? FOR UPDATE");
    $lock->bind_param("i", $idBarbeiro);
    $lock->execute();
    $lock->close();

    if (!in_array($horario, horarios_livres($conn, $idBarbeiro, $data, $duracao), true)) {
        $conn->rollback();
        voltar_com_erro('Esse horário acabou de ser ocupado ou não está disponível. Escolha outro.');
    }

    $horarioSql = $horario . ':00';
    $ins = $conn->prepare(
        "INSERT INTO agendamentos (id_cliente, id_barbeiro, id_servico, data_agendamento, horario, status)
         VALUES (?, ?, ?, ?, ?, 'Agendado')"
    );
    $ins->bind_param("iiiss", $idCliente, $idBarbeiro, $idServico, $data, $horarioSql);
    $ins->execute();
    $idAgendamento = $conn->insert_id;
    $ins->close();

    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    error_log('Cortaí - erro ao salvar agendamento: ' . $e->getMessage());
    voltar_com_erro('Não foi possível salvar seu agendamento agora. Tente novamente.');
}

header("Location: resumo.php?id=" . $idAgendamento);
exit;
