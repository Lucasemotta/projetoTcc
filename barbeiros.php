<?php
session_start();
include "conexao.php";

// Garante que apenas usuários logados acessem a lista
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$sql = "SELECT * FROM barbeiros ORDER BY id_barbeiro DESC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Barbeiros</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Fonte Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { 
            background-color: #111516; 
            color: #f5f5f5; 
            font-family: 'Montserrat', Arial, sans-serif; 
        }

        .container { 
            margin-top: 40px; 
            margin-bottom: 40px;
        }

        .card-barbeiros { 
            background-color: #191e1f; 
            border: 1px solid #303a3b; 
            border-radius: 12px; 
            padding: 30px; 
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        .titulo span { 
            color: #719999; 
        }

        .table { 
            color: white; 
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

        .btn-cortai { 
            background-color: #719999; 
            border: none; 
            color: #111516; 
            font-weight: bold; 
            padding: 10px 20px;
            border-radius: 6px;
            transition: 0.3s;
        }

        .btn-cortai:hover { 
            background-color: #86aaaa; 
            color: #111516;
        }

        .badge-ativo { 
            background-color: #198754; 
            color: #fff;
            padding: 6px 12px;
        }

        .badge-inativo { 
            background-color: #dc3545; 
            color: #fff;
            padding: 6px 12px;
        }

        .btn-voltar {
            color: #8f9997;
            border-color: #3b4547;
            text-decoration: none;
        }

        .btn-voltar:hover {
            color: #719999;
            background-color: #191e1f;
            border-color: #719999;
        }
    </style>
</head>
<body>

<div class="container mb-3">
    <a href="dashboard.php" class="btn btn-voltar btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Voltar ao Dashboard
    </a>
</div>

<div class="container">
    <div class="card-barbeiros">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <h2 class="titulo m-0">Lista de <span>Barbeiros</span></h2>
            <a href="cadastro_barbeiro.php" class="btn btn-cortai">
                <i class="bi bi-person-plus-fill me-1"></i> Novo Barbeiro
            </a>
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
                    <?php if ($resultado &&$resultado->num_rows > 0): ?>
                        <?php while ($barbeiro =$resultado->fetch_assoc()): ?>
                            <tr>
                                <td><strong>#<?= $barbeiro['id_barbeiro']; ?></strong></td>
                                <td><?= htmlspecialchars($barbeiro['nome']); ?></td>
                                <td><?= htmlspecialchars($barbeiro['telefone'] ?? '-'); ?></td>
                                <td><?= htmlspecialchars($barbeiro['especialidade'] ?? '-'); ?></td>
                                <td>
                                    <?php if ($barbeiro['status'] == 1): ?>
                                        <span class="badge badge-ativo"><i class="bi bi-check-circle me-1"></i>Ativo</span>
                                    <?php else: ?>
                                        <span class="badge badge-inativo"><i class="bi bi-x-circle me-1"></i>Inativo</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Nenhum barbeiro cadastrado até o momento.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>