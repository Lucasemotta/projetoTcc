<?php

session_start();

include "conexao.php";

// Verifica se os dados foram enviados
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

// Recebe os dados
$nome = trim($_POST['nome'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');

// Validação
if ($nome === '' || $telefone === '') {
    echo "<script>
        alert('Preencha seu nome e telefone.');
        window.location.href = 'index.php';
    </script>";
    exit;
}

// Verifica se o cliente já existe pelo telefone
$sqlBusca = "SELECT id_cliente FROM clientes WHERE telefone = ? LIMIT 1";

$stmtBusca = $conn->prepare($sqlBusca);
$stmtBusca->bind_param("s", $telefone);
$stmtBusca->execute();

$resultado = $stmtBusca->get_result();

if ($resultado->num_rows > 0) {

    // Cliente já existe
    $cliente = $resultado->fetch_assoc();

    $id_cliente = $cliente['id_cliente'];

    // Atualiza o nome caso tenha sido alterado
    $sqlAtualiza = "UPDATE clientes SET nome = ? WHERE id_cliente = ?";

    $stmtAtualiza = $conn->prepare($sqlAtualiza);
    $stmtAtualiza->bind_param("si", $nome, $id_cliente);
    $stmtAtualiza->execute();

} else {

    // Cadastra novo cliente
    $sqlCadastro = "
        INSERT INTO clientes (nome, telefone)
        VALUES (?, ?)
    ";

    $stmtCadastro = $conn->prepare($sqlCadastro);
    $stmtCadastro->bind_param("ss", $nome, $telefone);

    if (!$stmtCadastro->execute()) {
        die("Erro ao cadastrar cliente: " . $stmtCadastro->error);
    }

    $id_cliente = $conn->insert_id;
}

// Guarda o cliente na sessão
$_SESSION['id_cliente'] = $id_cliente;

// Vai para a tela de agendamento
header("Location: agenda.php");
exit;