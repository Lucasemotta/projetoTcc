<?php
session_start();
include "conexao.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');

if (empty($nome) || empty($telefone)) {
    echo "<script>
        alert('Por favor, preencha todos os campos.');
        window.location.href = 'index.php';
    </script>";
    exit;
}

// Verifica se o cliente já existe pelo telefone
$sqlBusca = "SELECT id_cliente FROM clientes WHERE telefone = ? LIMIT 1";
$stmtBusca = $conn->prepare($sqlBusca);
$stmtBusca->bind_param("s", $telefone);
$stmtBusca->execute();
$resBusca = $stmtBusca->get_result();

if ($resBusca->num_rows > 0) {
    // Cliente já existe, pega o ID dele
    $cliente = $resBusca->fetch_assoc();
    $_SESSION['id_cliente'] = $cliente['id_cliente'];
} else {
    // Cadastra novo cliente
    $sqlInsert = "INSERT INTO clientes (nome, telefone) VALUES (?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("ss", $nome, $telefone);
    
    if ($stmtInsert->execute()) {
        $_SESSION['id_cliente'] = $conn->insert_id;
    } else {
        die("Erro ao cadastrar cliente: " . $stmtInsert->error);
    }
}

// Redireciona para a página de agendamento
header("Location: agenda.php");
exit;
?>