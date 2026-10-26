<?php
session_start();
include "conexao.php";

// Garante que apenas usuários logados acessem a página
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php?tipo=barbeiro");
    exit;
}

// Serviços disponíveis para marcar quais o barbeiro realiza
$servicos = $conn->query("SELECT id_servico, nome FROM servicos ORDER BY nome ASC");
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Cadastro de Barbeiro</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/cortai.css">
</head>

<body>

    <div class="container pagina pb-0">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <a href="barbeiros.php" class="btn btn-voltar btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Voltar para a lista
                </a>
            </div>
        </div>
    </div>

    <div class="container pt-3 pb-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card-cortai">

                    <h2 class="titulo text-center mb-4">
                        Cadastro de <span>barbeiro</span>
                    </h2>

                    <!-- Alerta de Sucesso após envio -->
                    <?php if (isset($_GET['sucesso'])): ?>
                        <div class="alert alert-success text-center py-2" role="alert">
                            <i class="bi bi-check-circle me-1"></i> Barbeiro cadastrado com sucesso!
                        </div>
                    <?php endif; ?>

                    <form action="salvar_barbeiro.php" method="POST">

                        <div class="mb-3">
                            <label class="form-label">Nome completo</label>
                            <input type="text" name="nome" class="form-control" placeholder="Digite o nome do barbeiro" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="tel" name="telefone" class="form-control" placeholder="(14) 99999-9999">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Especialidade</label>
                            <input type="text" name="especialidade" class="form-control" placeholder="Ex: Corte masculino, barba, navalhado">
                        </div>

                        <div class="mb-3">
                            <span class="form-label d-block">Serviços que realiza</span>
                            <?php if ($servicos && $servicos->num_rows > 0): ?>
                                <?php while ($sv = $servicos->fetch_assoc()): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="servicos[]"
                                               value="<?= (int) $sv['id_servico']; ?>" id="sv<?= (int) $sv['id_servico']; ?>" checked>
                                        <label class="form-check-label" for="sv<?= (int) $sv['id_servico']; ?>"><?= htmlspecialchars($sv['nome']); ?></label>
                                    </div>
                                <?php endwhile; ?>
                                <div class="subtitulo mt-1">A agenda só mostra este barbeiro nos serviços marcados.</div>
                            <?php else: ?>
                                <div class="subtitulo">Cadastre um serviço primeiro (Painel &gt; Serviços).</div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="1" selected>Ativo</option>
                                <option value="0">Inativo</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-cortai">
                                <i class="bi bi-person-check-fill me-1"></i> CADASTRAR BARBEIRO
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>