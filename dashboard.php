<?php
session_start();
include "conexao.php";

// Redireciona para a página inicial caso a sessão do usuário não esteja ativa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php?tipo=barbeiro");
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
<html lang="pt-br" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Dashboard Administrativo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/cortai.css">
</head>
<body>

<nav class="navbar navbar-admin">
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-2">
        <a class="navbar-brand m-0" href="dashboard.php">CORTAÍ ADMIN</a>
        <div class="d-flex flex-wrap gap-2">
            <a href="clientes.php" class="btn btn-admin btn-sm"><i class="bi bi-people me-1"></i>Clientes</a>
            <a href="servicos.php" class="btn btn-admin btn-sm"><i class="bi bi-scissors me-1"></i>Serviços</a>
            <a href="barbeiros.php" class="btn btn-admin btn-sm"><i class="bi bi-person-badge me-1"></i>Barbeiros</a>
            <a href="logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Sair</a>
        </div>
    </div>
</nav>

<div class="container pagina">
    <div class="card-cortai">
        
        <!-- Cabeçalho do Card com Filtro de Data -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h2 class="titulo m-0">Agendamentos do <span>dia</span></h2>
            </div>

            <form method="GET" action="dashboard.php" class="d-flex align-items-center gap-2">
                <label for="filtro-data" class="mb-0 subtitulo">Filtrar data:</label>
                <input type="date" id="filtro-data" name="data" class="form-control" value="<?= htmlspecialchars($dataFiltro); ?>" onchange="this.form.submit()">
            </form>
        </div>

        <!-- Tabela de Agendamentos -->
        <div class="table-responsive">
            <table class="table table-hover align-middle table-stack mb-0">
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
                                <td data-label="Horário"><strong><i class="bi bi-clock me-1 text-muted"></i><?= date('H:i', strtotime($row['horario'])); ?></strong></td>
                                <td data-label="Cliente"><?= htmlspecialchars($row['cliente_nome']); ?></td>
                                <td data-label="Telefone"><?= htmlspecialchars($row['cliente_telefone']); ?></td>
                                <td data-label="Serviço"><?= htmlspecialchars($row['servico_nome']); ?></td>
                                <td data-label="Barbeiro"><?= htmlspecialchars($row['barbeiro_nome']); ?></td>
                                <td data-label="Valor">R$ <?= number_format($row['valor'], 2, ',', '.'); ?></td>
                                <td data-label="Status">
                                    <?php 
                                        $statusClass = 'bg-info text-dark';
                                        if ($row['status'] === 'Concluído') $statusClass = 'bg-success';
                                        if ($row['status'] === 'Cancelado') $statusClass = 'bg-danger';
                                    ?>
                                    <span class="badge <?= $statusClass; ?>"><?= htmlspecialchars($row['status']); ?></span>
                                </td>
                                <td data-label="Ações" class="text-center">
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
                            <td colspan="8" class="text-center py-4 subtitulo">Nenhum agendamento encontrado para esta data.</td>
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

          <!-- Bloco de Disponibilidade + Almoço -->
<div class="card bg-dark text-white p-4 mt-4 rounded">
    <h4 class="mb-3">Definir Minha Disponibilidade & Almoço</h4>
    <form action="salvar_horario.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Dia da Semana:</label>
            <select name="dia_semana" class="form-select bg-secondary text-white" required>
                <option value="1">Segunda-feira</option>
                <option value="2">Terça-feira</option>
                <option value="3">Quarta-feira</option>
                <option value="4">Quinta-feira</option>
                <option value="5">Sexta-feira</option>
                <option value="6">Sábado</option>
                <option value="0">Domingo</option>
            </select>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Horário Inicial (Abertura):</label>
                <input type="time" name="horario_inicio" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Horário Final (Fecho):</label>
                <input type="time" name="horario_fim" class="form-control" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Início do Almoço:</label>
                <input type="time" name="almoco_inicio" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Fim do Almoço:</label>
                <input type="time" name="almoco_fim" class="form-control">
            </div>
        </div>

        <button type="submit" class="btn btn-success w-100">Guardar Horário</button>
    </form>
</div>

<!-- Bloco de Avisos / Fecho por Imprevisto -->
<div class="card bg-dark text-white p-4 mt-4 rounded border border-warning">
    <h4 class="text-warning mb-3">Aviso de Fecho / Imprevisto</h4>
    <form action="salvar_aviso.php" method="POST">
        <div class="mb-3">
            <label class="form-label">Título do Aviso:</label>
            <input type="text" name="titulo" class="form-control" placeholder="Ex: Encerrado por Motivo de Força Maior" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mensagem para os Clientes:</label>
            <textarea name="mensagem" class="form-control" rows="2" placeholder="Ex: Não haverá atendimento no dia de hoje devido a um imprevisto." required></textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Data Início do Fecho:</label>
                <input type="date" name="data_inicio" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Data Fim do Fecho:</label>
                <input type="date" name="data_fim" class="form-control" required>
            </div>
        </div>
        <button type="submit" class="btn btn-warning w-100 fw-bold">Publicar Aviso de Fecho</button>
    </form>
</div>

        </div>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>