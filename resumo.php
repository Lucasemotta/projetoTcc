<?php
session_start();
include "conexao.php";

$id_agendamento = $_GET['id'] ?? null;

if (!$id_agendamento) {
    header("Location: index.php");
    exit;
}

// Busca todos os dados detalhados do agendamento
$sql = "SELECT a.*, c.nome AS nome_cliente, c.telefone AS telefone_cliente,
               b.nome AS nome_barbeiro, s.nome AS nome_servico, s.valor, s.duracao
        FROM agendamentos a
        INNER JOIN clientes c ON a.id_cliente = c.id_cliente
        INNER JOIN barbeiros b ON a.id_barbeiro = b.id_barbeiro
        INNER JOIN servicos s ON a.id_servico = s.id_servico
        WHERE a.id_agendamento = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_agendamento);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Agendamento não encontrado.");
}

$dados = $resultado->fetch_assoc();
$dataFormatada = date('d/m/Y', strtotime($dados['data_agendamento']));
$horarioFormatado = date('H:i', strtotime($dados['horario']));
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Confirmação do Agendamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #111516; color: white; font-family: Arial, sans-serif; }
        .container { margin-top: 60px; margin-bottom: 60px; }
        .card-resumo { background-color: #191e1f; border: 1px solid #303a3b; border-radius: 15px; padding: 40px; box-shadow: 0 15px 35px rgba(0,0,0,0.4); }
        .icone-sucesso { font-size: 60px; color: #28a745; text-align: center; margin-bottom: 15px; }
        .titulo { text-align: center; margin-bottom: 10px; font-weight: 700; }
        .subtitulo { text-align: center; color: #8f9997; margin-bottom: 30px; }
        .info-box { background-color: #111516; border: 1px solid #293436; border-radius: 10px; padding: 20px; margin-bottom: 25px; }
        .info-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #222a2b; }
        .info-item:last-child { border-bottom: none; }
        .label { color: #8f9997; }
        .valor { font-weight: 600; color: #ffffff; }
        .destaque-valor { color: #719999; font-size: 18px; }
        .btn-inicio { background-color: #719999; color: #111516; font-weight: bold; width: 100%; padding: 12px; border: none; border-radius: 6px; }
        .btn-inicio:hover { background-color: #86aaaa; color: #111516; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card-resumo">
                <div class="icone-sucesso">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <h2 class="titulo">Agendamento Confirmado!</h2>
                <p class="subtitulo">Apresente seus dados ao chegar na barbearia.</p>

                <div class="info-box">
                    <div class="info-item">
                        <span class="label">Cliente:</span>
                        <span class="valor"><?= htmlspecialchars($dados['nome_cliente']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Telefone:</span>
                        <span class="valor"><?= htmlspecialchars($dados['telefone_cliente']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Serviço:</span>
                        <span class="valor"><?= htmlspecialchars($dados['nome_servico']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Barbeiro:</span>
                        <span class="valor"><?= htmlspecialchars($dados['nome_barbeiro']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Data:</span>
                        <span class="valor"><?= $dataFormatada; ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Horário:</span>
                        <span class="valor"><?= $horarioFormatado; ?> (<?= $dados['duracao']; ?> min)</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Valor Total:</span>
                        <span class="valor destaque-valor">R$ <?= number_format($dados['valor'], 2, ',', '.'); ?></span>
                    </div>
                </div>

                <a href="index.php" class="btn btn-inicio">VOLTAR À PÁGINA INICIAL</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>