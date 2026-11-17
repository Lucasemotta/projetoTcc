<?php
session_start();
include "conexao.php";
require "admin_guard.php";

// Redireciona para a página inicial caso a sessão do usuário não esteja ativa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: entrar.php?tipo=barbeiro");
    exit;
}

$dataFiltro = $_GET['data'] ?? date('Y-m-d');
$statusFiltro = $_GET['status'] ?? '';

// Lógica para atualizar status ou excluir agendamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_ok() && isset($_POST['acao'], $_POST['id'])) {
    $id_agendamento = intval($_POST['id']);
    $acao = $_POST['acao'];

    if ($acao === 'concluir') {
        $stmtUp = $conn->prepare("UPDATE agendamentos SET status = 'Concluído' WHERE id_agendamento = ? AND id_barbeiro IN (SELECT id_barbeiro FROM barbeiros WHERE id_barbearia = {$idB})");
        $stmtUp->bind_param("i", $id_agendamento);
        $stmtUp->execute();
        $stmtUp->close();
    } elseif ($acao === 'cancelar') {
        $stmtUp = $conn->prepare("UPDATE agendamentos SET status = 'Cancelado' WHERE id_agendamento = ? AND id_barbeiro IN (SELECT id_barbeiro FROM barbeiros WHERE id_barbearia = {$idB})");
        $stmtUp->bind_param("i", $id_agendamento);
        $stmtUp->execute();
        $stmtUp->close();
    } elseif (in_array($acao, ['confirmar', 'faltou'], true)) {
        $novo = $acao === 'confirmar' ? 'Confirmado' : 'Não compareceu';
        $stmtUp = $conn->prepare("UPDATE agendamentos SET status = ? WHERE id_agendamento = ? AND id_barbeiro IN (SELECT id_barbeiro FROM barbeiros WHERE id_barbearia = {$idB})");
        $stmtUp->bind_param("si", $novo, $id_agendamento);
        $stmtUp->execute();
        $stmtUp->close();
    } elseif ($acao === 'excluir') {
        $stmtDel = $conn->prepare("DELETE FROM agendamentos WHERE id_agendamento = ? AND id_barbeiro IN (SELECT id_barbeiro FROM barbeiros WHERE id_barbearia = {$idB})");
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
        WHERE a.data_agendamento = ? AND b.id_barbearia = {$idB} AND ('' = ? OR a.status = ?)
        ORDER BY a.horario ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $dataFiltro, $statusFiltro, $statusFiltro);
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
            <a href="barbearia.php" class="btn btn-admin btn-sm"><i class="bi bi-shop me-1"></i>Barbearia</a>
            <a href="clientes.php" class="btn btn-admin btn-sm"><i class="bi bi-people me-1"></i>Clientes</a>
            <a href="servicos.php" class="btn btn-admin btn-sm"><i class="bi bi-scissors me-1"></i>Serviços</a>
            <a href="barbeiros.php" class="btn btn-admin btn-sm"><i class="bi bi-person-badge me-1"></i>Barbeiros</a>
            <a href="logout.php" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right me-1"></i>Sair</a>
        </div>
    </div>
</nav>

<?php
$hojeStr = date('Y-m-d'); $mesStr = date('Y-m');
$n = $conn->query("SELECT
  (SELECT COUNT(*) FROM agendamentos a JOIN barbeiros b USING(id_barbeiro) WHERE b.id_barbearia = {$idB} AND a.data_agendamento = '$hojeStr' AND a.status NOT IN ('Cancelado','Não compareceu')) AS hoje,
  (SELECT COALESCE(SUM(s.valor),0) FROM agendamentos a JOIN barbeiros b USING(id_barbeiro) JOIN servicos s ON s.id_servico = a.id_servico WHERE b.id_barbearia = {$idB} AND a.status = 'Concluído' AND DATE_FORMAT(a.data_agendamento,'%Y-%m') = '$mesStr') AS mes,
  (SELECT ROUND(AVG(nota),1) FROM avaliacoes WHERE id_barbearia = {$idB}) AS media")->fetch_assoc();
?>
<div class="container pagina pb-0">
    <div class="row g-3">
        <div class="col-md-4"><div class="card-cortai stat"><div class="subtitulo">Agendamentos hoje</div><div class="num"><?= (int) $n['hoje']; ?></div></div></div>
        <div class="col-md-4"><div class="card-cortai stat"><div class="subtitulo">Faturamento do mês (concluídos)</div><div class="num">R$ <?= number_format((float) $n['mes'], 2, ',', '.'); ?></div></div></div>
        <div class="col-md-4"><div class="card-cortai stat"><div class="subtitulo">Avaliação média</div><div class="num"><?= $n['media'] !== null ? '★ ' . str_replace('.', ',', $n['media']) : '—'; ?></div></div></div>
    </div>
</div>
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
                <select name="status" class="form-select" style="max-width:170px" aria-label="Filtrar por status" onchange="this.form.submit()"><option value="">Todos</option><?php foreach (["Agendado","Confirmado","Concluído","Não compareceu","Cancelado"] as $stt): ?><option <?= $statusFiltro === $stt ? "selected" : ""; ?>><?= $stt; ?></option><?php endforeach; ?></select>
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
                                if (!in_array($row['status'], ['Cancelado', 'Não compareceu'], true)) {
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
                                        if ($row['status'] === 'Confirmado') $statusClass = 'bg-primary';
                                        if ($row['status'] === 'Não compareceu') $statusClass = 'bg-secondary';
                                    ?>
                                    <span class="badge <?= $statusClass; ?>"><?= htmlspecialchars($row['status']); ?></span>
                                </td>
                                <td data-label="Ações" class="text-center">
                                    <form method="POST" action="dashboard.php?data=<?= htmlspecialchars($dataFiltro); ?>" class="btn-group btn-group-sm">
                                        <input type="hidden" name="csrf" value="<?= csrf_token(); ?>"><input type="hidden" name="id" value="<?= (int) $row['id_agendamento']; ?>">
                                        <button name="acao" value="confirmar" class="btn btn-outline-info" title="Confirmar"><i class="bi bi-hand-thumbs-up"></i></button>
                                        <button name="acao" value="concluir" class="btn btn-outline-success" title="Concluído"><i class="bi bi-check-lg"></i></button>
                                        <button name="acao" value="faltou" class="btn btn-outline-secondary" title="Não compareceu"><i class="bi bi-person-x"></i></button>
                                        <button name="acao" value="cancelar" class="btn btn-outline-warning" title="Cancelar"><i class="bi bi-x-lg"></i></button>
                                        <button name="acao" value="excluir" class="btn btn-outline-danger" title="Excluir" onclick="return confirm('Excluir este agendamento?')"><i class="bi bi-trash"></i></button>
                                    </form>
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
        </div>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>