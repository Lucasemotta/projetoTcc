<?php
session_start();
include "conexao.php";

// Redireciona para a página inicial caso a sessão do usuário não esteja ativa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$dataFiltro = $_GET['data'] ?? date('Y-m-d');

// Lógica para atualizar status ou excluir agendamento
if (isset($_GET['acao']) && isset($_GET['id'])) {
    $id_agendamento = intval($_GET['id']);
    $acao = $_GET['acao'];

    if ($acao === 'concluir') {
        $stmtUp = $conn->prepare("UPDATE agendamentos SET status = 'Concluído' WHERE id_agendamento = ?");
        $stmtUp->bind_param("i", $id_agendamento);
        $stmtUp->execute();
        $stmtUp->close();
    } elseif ($acao === 'cancelar') {
        $stmtUp = $conn->prepare("UPDATE agendamentos SET status = 'Cancelado' WHERE id_agendamento = ?");
        $stmtUp->bind_param("i", $id_agendamento);
        $stmtUp->execute();
        $stmtUp->close();
    } elseif ($acao === 'excluir') {
        $stmtDel = $conn->prepare("DELETE FROM agendamentos WHERE id_agendamento = ?");
        $stmtDel->bind_param("i", $id_agendamento);
        $stmtDel->execute();
        $stmtDel->close();
    }

    header("Location: dashboard.php?data=" . $dataFiltro);
    exit;
}

// Consulta todos os agendamentos da data selecionada
$sql = "SELECT a.*, c.nome AS cliente_nome, c.telefone AS cliente_telefone,
               b.nome AS barbeiro_nome, s.nome AS servico_nome, s.valor
        FROM agendamentos a
        INNER JOIN clientes c ON a.id_cliente = c.id_cliente
        INNER JOIN barbeiros b ON a.id_barbeiro = b.id_barbeiro
        INNER JOIN servicos s ON a.id_servico = s.id_servico
        WHERE a.data_agendamento = ?
        ORDER BY a.horario ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $dataFiltro);
$stmt->execute();
$agendamentos = $stmt->get_result();

// Variável para somar o faturamento do dia
$totalFaturamento = 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Dashboard Administrativo</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Fonte Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #111516;
            color: white;
            font-family: 'Montserrat', Arial, sans-serif;
        }

        .navbar-admin {
            background-color: #191e1f;
            border-bottom: 1px solid #303a3b;
            padding: 15px 30px;
        }

        .navbar-brand {
            color: #719999 !important;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .container {
            margin-top: 40px;
            margin-bottom: 40px;
        }

        .card-panel {
            background-color: #191e1f;
            border: 1px solid #303a3b;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        .titulo span {
            color: #719999;
        }

        .card-resumo-valor {
            background-color: #111516;
            border: 1px solid #3b4547;
            border-radius: 8px;
            padding: 15px 20px;
            display: inline-block;
        }

        .card-resumo-valor span {
            color: #719999;
            font-weight: bold;
            font-size: 20px;
        }

        .table {
            color: #ffffff;
            border-color: #303a3b;
        }

        .table thead {
            background-color: #719999;
            color: #111516;
        }

        .table thead th {
            border: none;
            padding: 12px;
        }

        .table tbody td {
            padding: 14px 12px;
            border-color: #293436;
        }

        .form-control {
            background-color: #111516;
            border: 1px solid #3b4547;
            color: white;
            padding: 8px 12px;
        }

        .form-control:focus {
            background-color: #111516;
            color: white;
            border-color: #719999;
            box-shadow: none;
        }

        .btn-outline-light {
            border-color: #3b4547;
            color: #c8cecc;
        }

        .btn-outline-light:hover {
            background-color: #719999;
            border-color: #719999;
            color: #111516;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-admin">
    <a class="navbar-brand" href="dashboard.php">CORTAÍ ADMIN</a>
    <div class="ms-auto d-flex gap-2">
        <a href="servicos.php" class="btn btn-outline-light btn-sm"><i class="bi bi-scissors me-1"></i>Serviços</a>
        <a href="barbeiros.php" class="btn btn-outline-light btn-sm"><i class="bi bi-person-badge me-1"></i>Barbeiros</a>
        <a href="logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Sair</a>
    </div>
</nav>

<div class="container">
    <div class="card-panel mb-4">
        
        <!-- Cabeçalho do Card com Filtro de Data -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="titulo m-0">Agendamentos do <span>Dia</span></h2>
            </div>

            <form method="GET" action="dashboard.php" class="d-flex align-items-center gap-2">
                <label class="mb-0 text-muted" style="font-size: 14px;">Filtrar Data:</label>
                <input type="date" name="data" class="form-control" value="<?= htmlspecialchars($dataFiltro); ?>" onchange="this.form.submit()">
            </form>
        </div>

        <!-- Tabela de Agendamentos -->
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Horário</th>
                        <th>Cliente</th>
                        <th>Telefone</th>
                        <th>Serviço</th>
                        <th>Barbeiro</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($agendamentos && $agendamentos->num_rows > 0): ?>
                        <?php while ($row = $agendamentos->fetch_assoc()): ?>
                            <?php 
                                if ($row['status'] !== 'Cancelado') {
                                    $totalFaturamento += $row['valor']; 
                                }
                            ?>
                            <tr>
                                <td><strong><i class="bi bi-clock me-1 text-muted"></i><?= date('H:i', strtotime($row['horario'])); ?></strong></td>
                                <td><?= htmlspecialchars($row['cliente_nome']); ?></td>
                                <td><?= htmlspecialchars($row['cliente_telefone']); ?></td>
                                <td><?= htmlspecialchars($row['servico_nome']); ?></td>
                                <td><?= htmlspecialchars($row['barbeiro_nome']); ?></td>
                                <td>R$ <?= number_format($row['valor'], 2, ',', '.'); ?></td>
                                <td>
                                    <?php 
                                        $statusClass = 'bg-info text-dark';
                                        if ($row['status'] === 'Concluído') $statusClass = 'bg-success';
                                        if ($row['status'] === 'Cancelado') $statusClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $statusClass; ?>"><?= htmlspecialchars($row['status']); ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="dashboard.php?data=<?= $dataFiltro; ?>&acao=concluir&id=<?= $row['id_agendamento']; ?>" class="btn btn-outline-success" title="Marcar como Concluído"><i class="bi bi-check-lg"></i></a>
                                        <a href="dashboard.php?data=<?= $dataFiltro; ?>&acao=cancelar&id=<?= $row['id_agendamento']; ?>" class="btn btn-outline-warning" title="Cancelar Agendamento"><i class="bi bi-x-lg"></i></a>
                                        <a href="dashboard.php?data=<?= $dataFiltro; ?>&acao=excluir&id=<?= $row['id_agendamento']; ?>" class="btn btn-outline-danger" onclick="return confirm('Excluir este agendamento?');" title="Excluir"><i class="bi bi-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Nenhum agendamento encontrado para esta data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Faturamento Total Previsto do Dia -->
        <div class="mt-4 text-end">
            <div class="card-resumo-valor">
                Total Previsto: <span>R$ <?= number_format($totalFaturamento, 2, ',', '.'); ?></span>
            </div>
        </div>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>