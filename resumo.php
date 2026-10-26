<?php
require "agenda_lib.php";
require "conexao.php";

$idAgendamento = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$idAgendamento) {
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
$stmt->bind_param("i", $idAgendamento);
$stmt->execute();
$dados = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Só o próprio cliente (ou um administrador logado) pode ver o comprovante.
// Sem isso, qualquer pessoa trocando o número na URL veria nome e telefone de outros clientes.
$ehAdmin   = !empty($_SESSION['usuario_id']);
$ehDono    = $dados && !empty($_SESSION['id_cliente']) && (int) $_SESSION['id_cliente'] === (int) $dados['id_cliente'];

if (!$dados || !($ehAdmin || $ehDono)) {
    http_response_code(404);
    exit("Agendamento não encontrado.");
}

$dataFormatada    = date('d/m/Y', strtotime($dados['data_agendamento']));
$horarioFormatado = date('H:i', strtotime($dados['horario']));
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Confirmação do Agendamento</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/cortai.css">
</head>
<body>

<div class="container pagina">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card-cortai">

                <div class="icone-sucesso"><i class="bi bi-check-circle-fill"></i></div>
                <h2 class="titulo text-center mb-2">Agendamento confirmado!</h2>
                <p class="subtitulo text-center mb-4">Apresente seus dados ao chegar na barbearia.</p>

                <div class="info-box">
                    <div class="info-item"><span class="label">Cliente</span><span class="valor"><?= htmlspecialchars($dados['nome_cliente']); ?></span></div>
                    <div class="info-item"><span class="label">Telefone</span><span class="valor"><?= htmlspecialchars($dados['telefone_cliente']); ?></span></div>
                    <div class="info-item"><span class="label">Serviço</span><span class="valor"><?= htmlspecialchars($dados['nome_servico']); ?></span></div>
                    <div class="info-item"><span class="label">Barbeiro</span><span class="valor"><?= htmlspecialchars($dados['nome_barbeiro']); ?></span></div>
                    <div class="info-item"><span class="label">Data</span><span class="valor"><i class="bi bi-calendar-event me-1"></i><?= $dataFormatada; ?></span></div>
                    <div class="info-item"><span class="label">Horário</span><span class="valor"><i class="bi bi-clock me-1"></i><?= $horarioFormatado; ?> (<?= (int) ($dados['duracao'] ?? 30); ?> min)</span></div>
                    <div class="info-item"><span class="label">Valor total</span><span class="valor destaque-valor">R$ <?= number_format($dados['valor'], 2, ',', '.'); ?></span></div>
                </div>

                <div class="d-grid gap-2 no-print">
                    <a href="agenda.php" class="btn btn-cortai d-flex align-items-center justify-content-center">
                        <i class="bi bi-calendar-plus me-2"></i> AGENDAR OUTRO HORÁRIO
                    </a>
                    <button type="button" onclick="window.print()" class="btn btn-cortai-outline">
                        <i class="bi bi-printer me-1"></i> Imprimir comprovante
                    </button>
                    <a href="index.php?trocar=1" class="btn btn-cortai-outline d-flex align-items-center justify-content-center">
                        <i class="bi bi-box-arrow-right me-1"></i> Sair
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>
