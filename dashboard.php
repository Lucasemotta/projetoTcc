<?php
session_start();
include "conexao.php";

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$dataFiltro = $_GET['data'] ?? date('Y-m-d');

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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Dashboard Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #111516; color: white; font-family: Arial, sans-serif; }
        .navbar-admin { background-color: #191e1f; border-bottom: 1px solid #303a3b; }
        .container { margin-top: 40px; }
        .card-panel { background-color: #191e1f; border: 1px solid #303a3b; border-radius: 12px; padding: 30px; }
        .titulo span { color: #719999; }
        .table { color: white; }
        .table thead { background-color: #719999; color: #111516; }
        .form-control { background-color: #111516; border: 1px solid #3b4547; color: white; width: auto; display: inline-block; }
        .btn-cortai { background-color: #719999; color: #111516; font-weight: bold; border: none; }
        .btn-cortai:hover { background-color: #86aaaa; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-admin px-4">
    <a class="navbar-brand font-weight-bold" href="dashboard.php" style="color: #719999; font-weight: bold;">CORTAÍ ADMIN</a>
    <div class="ms-auto">
        <a href="servicos.php" class="btn btn-outline-light btn-sm me-2">Serviços</a>
        <a href="barbeiros.php" class="btn btn-outline-light btn-sm me-2">Barbeiros</a>
        <a href="logout.php" class="btn btn-danger btn-sm">Sair</a>
    </div>
</nav>

<div class="container">
    <div class="card-panel mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="titulo m-0">Agendamentos do <span>Dia</span></h2>
            <form method="GET" action="dashboard.php" class="d-flex align-items-center gap-2">
                <label>Data:</label>
                <input type="date" name="data" class="form-control" value="<?= $dataFiltro; ?>" onchange="this.form.submit()">
            </form>
        </div>

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
                    </tr>
                </thead>
                <tbody>
                    <?php if ($agendamentos && $agendamentos->num_rows > 0): ?>
                        <?php while ($row = $agendamentos->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?= date('H:i', strtotime($row['horario'])); ?></strong></td>
                                <td><?= htmlspecialchars($row['cliente_nome']); ?></td>
                                <td><?= htmlspecialchars($row['cliente_telefone']); ?></td>
                                <td><?= htmlspecialchars($row['servico_nome']); ?></td>
                                <td><?= htmlspecialchars($row['barbeiro_nome']); ?></td>
                                <td>R$ <?= number_format($row['valor'], 2, ',', '.'); ?></td>
                                <td><span class="badge bg-info text-dark"><?= $row['status']; ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Nenhum agendamento encontrado para esta data.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>