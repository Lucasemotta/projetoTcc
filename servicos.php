<?php
include "conexao.php";

$sql = "SELECT * FROM servicos ORDER BY nome ASC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Serviços</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #111516; color: #f5f5f5; font-family: Arial, sans-serif; }
        .container { margin-top: 50px; }
        .card-servicos { background-color: #191e1f; border: 1px solid #303a3b; border-radius: 12px; padding: 30px; }
        .titulo span { color: #719999; }
        .table { color: white; }
        .table thead { background-color: #719999; color: #111516; }
        .btn-cortai { background-color: #719999; border: none; color: #111516; font-weight: bold; }
        .btn-cortai:hover { background-color: #86aaaa; }
        .modal-content { background-color: #191e1f; color: white; border: 1px solid #303a3b; }
        .form-control { background-color: #111516; border: 1px solid #3b4547; color: white; }
        .form-control:focus { background-color: #111516; color: white; border-color: #719999; box-shadow: none; }
    </style>
</head>
<body>

<div class="container">
    <div class="card-servicos">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="titulo m-0">Lista de <span>Serviços</span></h1>
            <button class="btn btn-cortai" data-bs-toggle="modal" data-bs-target="#modalServico">+ Novo Serviço</button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
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
                                <td><?= $servico['id_servico']; ?></td>
                                <td><?= htmlspecialchars($servico['nome']); ?></td>
                                <td><?= htmlspecialchars($servico['descricao']); ?></td>
                                <td>R$ <?= number_format($servico['valor'], 2, ',', '.'); ?></td>
                                <td><?= $servico['duracao']; ?> min</td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Nenhum serviço cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Novo Serviço -->
<div class="modal fade" id="modalServico" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cadastrar Novo Serviço</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
          <div class="modal-footer">
              <button type="submit" class="btn btn-cortai">Salvar Serviço</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>