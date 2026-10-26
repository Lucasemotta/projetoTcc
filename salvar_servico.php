<?php
session_start();
include "conexao.php";

// Só quem está logado no painel cadastra serviços
if (!isset($_SESSION['usuario_id']) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php?tipo=barbeiro");
    exit;
}

$nome      = trim($_POST['nome'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$valor     = (float) ($_POST['valor'] ?? 0);
$duracao   = (int) ($_POST['duracao'] ?? 0);

if ($nome === '' || $valor <= 0 || $duracao <= 0) {
    die("Informe nome, valor e duração do serviço.");
}

$conn->begin_transaction();
try {
    $stmt = $conn->prepare("INSERT INTO servicos (nome, descricao, valor, duracao) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdi", $nome, $descricao, $valor, $duracao);
    $stmt->execute();
    $idServico = $conn->insert_id;
    $stmt->close();

    // Serviço novo já aparece na agenda: começa vinculado a todos os barbeiros ativos
    $vinc = $conn->prepare("INSERT IGNORE INTO barbeiro_servico (id_barbeiro, id_servico)
                            SELECT id_barbeiro, ? FROM barbeiros WHERE status = 1");
    $vinc->bind_param("i", $idServico);
    $vinc->execute();
    $vinc->close();

    $conn->commit();
    header("Location: servicos.php");
    exit;
} catch (Throwable $e) {
    $conn->rollback();
    error_log("Cortaí - erro ao cadastrar serviço: " . $e->getMessage());
    echo "Não foi possível cadastrar o serviço. Tente novamente.";
}
