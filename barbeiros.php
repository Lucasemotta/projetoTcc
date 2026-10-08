<?php
include "conexao.php";

$sql = "SELECT * FROM barbeiros ORDER BY id_barbeiro DESC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Barbeiros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #111516; color: #f5f5f5; font-family: Arial, sans-serif; }
        .container { margin-top: 40px; }
        .card-barbeiros { background-color: #191e1f; border: 1px solid #303a3b; border-radius: 12px; padding: 30px; }
        .titulo span { color: #719999; }
        .table { color: white; }
        .table thead { background-color: #719999; color: #111516; }
        .btn-cortai { background-color: #719999; border: none; color: #111516; font-weight: bold; }
        .btn-cortai:hover { background-color: #86aaaa; }
        .badge-ativo { background-color: #28a745; }
        .badge-inativo { background-color: #dc3545; }
    </style>
</head>
<body>

<div class="container mb-3">
    <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">← Voltar ao Dashboard</a>
</div>

<div class="container">
    <div class="card-barbeiros">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="titulo m-0">Lista de <span>Barbeiros</span></h1>
            <a href="cadastro_barbeiro.php" class="btn btn-cortai">+ Novo Barbeiro</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Especialidade</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php while ($barbeiro = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td><?= $barbeiro['id_barbeiro']; ?></td>
                                <td><?= htmlspecialchars($barbeiro['nome']); ?></td>
                                <td><?= htmlspecialchars($barbeiro['telefone']); ?></td>
                                <td><?= htmlspecialchars($barbeiro['especialidade']); ?></td>
                                <td>
                                    <?php if ($barbeiro['status'] == 1): ?>
                                        <span class="badge badge-ativo">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge badge-inativo">Inativo</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Nenhum barbeiro cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>