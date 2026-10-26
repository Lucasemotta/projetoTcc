<?php
session_start();
include "conexao.php";

// Garante que apenas usuários logados acessem a lista
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php?tipo=barbeiro");
    exit;
}

// GROUP_CONCAT traz, em uma coluna só, os serviços que cada barbeiro realiza
$sql = "SELECT b.*, GROUP_CONCAT(s.nome ORDER BY s.nome SEPARATOR ', ') AS servicos_lista
        FROM barbeiros b
        LEFT JOIN barbeiro_servico bs ON bs.id_barbeiro = b.id_barbeiro
        LEFT JOIN servicos s ON s.id_servico = bs.id_servico
        GROUP BY b.id_barbeiro
        ORDER BY b.id_barbeiro DESC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Barbeiros</title>

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
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <h2 class="titulo m-0">Lista de <span>barbeiros</span></h2>
            <a href="cadastro_barbeiro.php" class="btn btn-cortai">
                <i class="bi bi-person-plus-fill me-1"></i> Novo Barbeiro
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle table-stack mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Especialidade</th>
                        <th>Serviços</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado &&$resultado->num_rows > 0): ?>
                        <?php while ($barbeiro =$resultado->fetch_assoc()): ?>
                            <tr>
                                <td data-label="ID"><strong>#<?= $barbeiro['id_barbeiro']; ?></strong></td>
                                <td data-label="Nome"><?= htmlspecialchars($barbeiro['nome']); ?></td>
                                <td data-label="Telefone"><?= htmlspecialchars($barbeiro['telefone'] ?? '-'); ?></td>
                                <td data-label="Especialidade"><?= htmlspecialchars($barbeiro['especialidade'] ?? '-'); ?></td>
                                <td data-label="Serviços"><?= htmlspecialchars($barbeiro['servicos_lista'] ?? '') ?: '<span class="subtitulo">nenhum</span>'; ?></td>
                                <td data-label="Status">
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
                            <td colspan="6" class="text-center py-4 subtitulo">Nenhum barbeiro cadastrado até o momento.</td>
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