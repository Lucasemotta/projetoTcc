<?php
session_start();

include "conexao.php";

// Verifica se o formulário foi enviado via POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: cadastro_cliente.php");
    exit;
}

// Garante que a coluna 'email' exista na tabela 'clientes'
$conn->query("ALTER TABLE clientes ADD COLUMN IF NOT EXISTS email VARCHAR(100) DEFAULT NULL AFTER telefone");

// Recebe e limpa os dados enviados pelo formulário
$nome     = trim($_POST['nome'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$email    = trim($_POST['email'] ?? '');

// Validação dos campos obrigatórios
if ($nome === '' || $telefone === '') {
    echo "
    <script>
        alert('Por favor, preencha o nome e o telefone.');
        window.location.href = 'cadastro_cliente.php';
    </script>
    ";
    exit;
}

// Prepara a query SQL com Prepared Statement
$sql = "INSERT INTO clientes (nome, telefone, email) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Erro ao preparar o cadastro do cliente: " . $conn->error);
}

// Vincula os parâmetros (s = string)
$stmt->bind_param("sss", $nome, $telefone, $email);

// Executa o cadastro
if ($stmt->execute()) {
    // Guarda o ID e o Nome do cliente recém-cadastrado na sessão
    $_SESSION['id_cliente']     = $conn->insert_id;
    $_SESSION['nome_cliente']   = $nome;
    $_SESSION['telefone_cliente'] = $telefone;

    // Redireciona diretamente para a tela de agendamento
    header("Location: agenda.php");
    exit;
} else {
    echo "Erro ao cadastrar cliente: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>