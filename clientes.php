<?php
session_start();
include "conexao.php";

// Garante que apenas usuários logados acessem a lista
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php?tipo=barbeiro");
    exit;
}

$sql = "SELECT * FROM clientes ORDER BY id_cliente DESC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Clientes</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/cortai.css">
</head>

<body>

    <div class="container pagina pb-0">
        <a href="dashboard.php" class="btn btn-voltar btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Voltar ao Dashboard
        </a>
    </div>

    <div class="container pt-3 pb-5">
        <div class="card-cortai">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="titulo m-0">Lista de <span>clientes</span></h2>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle table-stack mb-0">
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
                                    <td data-label="ID"><strong>#<?= $cliente['id_cliente']; ?></strong></td>
                                    <td data-label="Nome"><?= htmlspecialchars($cliente['nome']); ?></td>
                                    <td data-label="Telefone"><?= htmlspecialchars($cliente['telefone']); ?></td>
                                    <td data-label="E-mail"><?= htmlspecialchars($cliente['email'] ?? '-'); ?></td>
                                    <td data-label="Cadastro"><i class="bi bi-calendar3 me-1"></i><?= $dataFormatada; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 subtitulo">Nenhum cliente cadastrado até o momento.</td>
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