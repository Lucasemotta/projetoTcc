<?php
include "conexao.php";

$nome = trim($_POST['nome'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$especialidade = trim($_POST['especialidade'] ?? '');
$status = $_POST['status'] ?? 1;

if (empty($nome)) {
    die("O campo nome é obrigatório.");
}

// Utiliza a variável de conexão correta ($conn) e Prepared Statement para maior segurança
$sql = "INSERT INTO barbeiros (nome, telefone, especialidade, status) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssi", $nome, $telefone, $especialidade, $status);

if ($stmt->execute()) {
    header("Location: barbeiros.php?sucesso=1");
    exit;
} else {
    echo "Erro ao cadastrar barbeiro: " . $stmt->error;
}
?>