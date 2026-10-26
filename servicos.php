<?php
session_start();
include "conexao.php";

// Garante que apenas usuários logados acessem a lista
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php?tipo=barbeiro");
    exit;
}

$sql = "SELECT * FROM servicos ORDER BY nome ASC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Serviços</title>

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
            <h2 class="titulo m-0">Lista de <span>serviços</span></h2>
            <button class="btn btn-cortai" data-bs-toggle="modal" data-bs-target="#modalServico">
                <i class="bi bi-plus-circle me-1"></i> Novo Serviço
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle table-stack mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Valor</th>
                        <th>Duração (min)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado && $resultado->num_rows > 0): ?>
                        <?php while ($servico = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td data-label="ID"><strong>#<?= $servico['id_servico']; ?></strong></td>
                                <td data-label="Nome"><?= htmlspecialchars($servico['nome']); ?></td>
                                <td data-label="Descrição"><?= htmlspecialchars($servico['descricao'] ?? '-'); ?></td>
                                <td data-label="Valor">R$ <?= number_format($servico['valor'], 2, ',', '.'); ?></td>
                                <td data-label="Duração"><?= htmlspecialchars($servico['duracao'] ?? 30); ?> min</td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 subtitulo">Nenhum serviço cadastrado até o momento.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Novo Serviço -->
<div class="modal fade" id="modalServico" tabindex="-1" aria-labelledby="modalServicoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header border-secondary">
        <h5 class="modal-title" id="modalServicoLabel"><i class="bi bi-scissors me-2"></i>Cadastrar Novo Serviço</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="salvar_servico.php" method="POST">
          <div class="modal-body">
              <div class="mb-3">
                  <label class="form-label">Nome do Serviço</label>
                  <input type="text" name="nome" class="form-control" required placeholder="Ex: Corte de Cabelo">
              </div>
              <div class="mb-3">
                  <label class="form-label">Descrição</label>
                  <input type="text" name="descricao" class="form-control" placeholder="Ex: Corte com máquina e tesoura">
              </div>
              <div class="mb-3">
                  <label class="form-label">Valor (R$)</label>
                  <input type="number" step="0.01" name="valor" class="form-control" required placeholder="30.00">
              </div>
              <div class="mb-3">
                  <label class="form-label">Duração (minutos)</label>
                  <input type="number" name="duracao" class="form-control" required placeholder="30">
              </div>
          </div>
          <div class="modal-footer border-secondary">
              <button type="button" class="btn btn-cortai-outline" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-cortai">Salvar Serviço</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>