<?php
session_start();
include "conexao.php";

// Só quem está logado no painel cadastra barbeiros
if (!isset($_SESSION['usuario_id']) || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php?tipo=barbeiro");
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$especialidade = trim($_POST['especialidade'] ?? '');
$status = (int) ($_POST['status'] ?? 1);
$servicos = array_filter(array_map('intval', $_POST['servicos'] ?? []));

if (empty($nome)) {
    die("O campo nome é obrigatório.");
}

$conn->begin_transaction();
try {
    $stmt = $conn->prepare("INSERT INTO barbeiros (nome, telefone, especialidade, status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $nome, $telefone, $especialidade, $status);
    $stmt->execute();
    $idBarbeiro = $conn->insert_id;
    $stmt->close();

    // Vincula o barbeiro aos serviços marcados (ids que não existem são ignorados)
    $vinc = $conn->prepare("INSERT IGNORE INTO barbeiro_servico (id_barbeiro, id_servico)
                            SELECT ?, id_servico FROM servicos WHERE id_servico = ?");
    foreach ($servicos as $idServico) {
        $vinc->bind_param("ii", $idBarbeiro, $idServico);
        $vinc->execute();
    }
    $vinc->close();

    $conn->commit();
    header("Location: barbeiros.php?sucesso=1");
    exit;
} catch (Throwable $e) {
    $conn->rollback();
    error_log("Cortaí - erro ao cadastrar barbeiro: " . $e->getMessage());
    echo "Não foi possível cadastrar o barbeiro. Tente novamente.";
}
