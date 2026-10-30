<?php
require_once 'conexao.php';
session_start();

// Activa relatórios de erros para evitar tela branca em desenvolvimento
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pega o id do barbeiro da sessão ou usa 1 como padrão para testes
    $id_barbeiro = $_SESSION['id_barbeiro'] ?? 1; 

    $dia_semana     = $_POST['dia_semana'] ?? 1;
    $horario_inicio = $_POST['horario_inicio'] ?? null;
    $horario_fim    = $_POST['horario_fim'] ?? null;
    $almoco_inicio  = !empty($_POST['almoco_inicio']) ? $_POST['almoco_inicio'] : null;
    $almoco_fim     = !empty($_POST['almoco_fim']) ? $_POST['almoco_fim'] : null;

    try {
        $stmt = $conexao->prepare("
            INSERT INTO disponibilidade_barbeiro 
            (id_barbeiro, dia_semana, horario_inicio, horario_fim, almoco_inicio, almoco_fim) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iissss", $id_barbeiro, $dia_semana, $horario_inicio, $horario_fim, $almoco_inicio, $almoco_fim);
        $stmt->execute();

        // Redireciona de volta para a dashboard para EVITAR a tela branca
        header("Location: dashboard.php?sucesso=1");
        exit();
    } catch (Exception $e) {
        die("Erro ao guardar horário: " . $e->getMessage());
    }
}

header("Location: dashboard.php");
exit();
?>