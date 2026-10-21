<?php
session_start();
include "conexao.php";

// Garante que apenas usuários logados acessem a lista
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$sql = "SELECT * FROM clientes ORDER BY id_cliente DESC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Clientes</title>

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

        .card-clientes {
            background-color: #191e1f;
            border: 1px solid #303a3b;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        .titulo {
            color: #ffffff;
            font-weight: 600;
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
            transition: 0.3s;
        }

        .btn-cortai:hover {
            background-color: #86aaaa;
            color: #111516;
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

        .data {
            color: #aeb5b3;
            font-size: 14px;
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
        <div class="card-clientes">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="titulo m-0">Lista de <span>Clientes</span></h2>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Cadastro</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php if ($resultado && $resultado->num_rows > 0): ?>
                            <?php while ($cliente = $resultado->fetch_assoc()): ?>
                                <?php 
                                    // Identifica qual coluna de data existe no registro
                                    $dataReg = $cliente['data_cadastro'] ?? $cliente['criado_em'] ?? null;
                                    $dataFormatada = $dataReg ? date("d/m/Y H:i", strtotime($dataReg)) : '-';
                                ?>
                                <tr>
                                    <td><strong>#<?= $cliente['id_cliente']; ?></strong></td>
                                    <td><?= htmlspecialchars($cliente['nome']); ?></td>
                                    <td><?= htmlspecialchars($cliente['telefone']); ?></td>
                                    <td><?= htmlspecialchars($cliente['email'] ?? '-'); ?></td>
                                    <td class="data"><i class="bi bi-calendar3 me-1"></i><?= $dataFormatada; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Nenhum cliente cadastrado até o momento.</td>
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