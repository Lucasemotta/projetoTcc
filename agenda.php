<?php
session_start();
include "conexao.php";

// Se o cliente não estiver identificado na sessão, redireciona para o cadastro
if (!isset($_SESSION['id_cliente'])) {
    header("Location: cadastro_cliente.php");
    exit;
}

// Procura os serviços e barbeiros ativos
$sqlServicos = "SELECT * FROM servicos ORDER BY nome ASC";
$resultadoServicos = $conn->query($sqlServicos);

$sqlBarbeiros = "SELECT * FROM barbeiros WHERE status = 1 ORDER BY nome ASC";
$resultadoBarbeiros = $conn->query($sqlBarbeiros);

$nomeCliente = $_SESSION['nome_cliente'] ?? 'Cliente';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Agendamento</title>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Fonte Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body { 
            background-color: #111516; 
            color: white; 
            font-family: 'Montserrat', Arial, sans-serif; 
        }
        .container { 
            margin-top: 50px; 
            margin-bottom: 50px; 
        }
        .card-agenda { 
            background-color: #191e1f; 
            border: 1px solid #303a3b; 
            border-radius: 15px; 
            padding: 40px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.35);
        }
        .titulo { 
            text-align: center; 
            margin-bottom: 5px; 
            font-weight: 700;
        }
        .titulo span { 
            color: #719999; 
        }
        .boas-vindas {
            text-align: center;
            color: #8f9997;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .form-label { 
            color: #ddd; 
            font-size: 14px;
        }
        .form-control, .form-select { 
            background-color: #111516; 
            border: 1px solid #3b4547; 
            color: white; 
            padding: 10px 12px;
        }
        .form-control:focus, .form-select:focus { 
            background-color: #111516; 
            color: white; 
            border-color: #719999; 
            box-shadow: none; 
        }
        .form-select option { 
            background-color: #111516; 
            color: white; 
        }
        .btn-cortai { 
            background-color: #719999; 
            color: #111516; 
            font-weight: bold; 
            border: none; 
            width: 100%; 
            padding: 12px; 
            border-radius: 6px;
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
    </style>
</head>
<body>

<div class="container mb-3">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <a href="index.php" class="btn btn-voltar btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Voltar ao Início
            </a>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card-agenda">
                <h2 class="titulo">Agende seu <span>horário</span></h2>
                <p class="boas-vindas">Olá, <strong><?= htmlspecialchars($nomeCliente); ?></strong>! Escolha os detalhes do seu atendimento.</p>

                <form action="salvar_agendamento.php" method="POST">
                    
                    <!-- SERVIÇO -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-scissors me-1"></i>Escolha o serviço</label>
                        <select name="id_servico" class="form-select" required>
                            <option value="">Selecione um serviço</option>
                            <?php if ($resultadoServicos && $resultadoServicos->num_rows > 0): ?>
                                <?php while ($servico = $resultadoServicos->fetch_assoc()): ?>
                                    <option value="<?= $servico['id_servico']; ?>">
                                        <?= htmlspecialchars($servico['nome']); ?> - R$ <?= number_format($servico['valor'], 2, ',', '.'); ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- BARBEIRO -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-person-badge me-1"></i>Escolha o barbeiro</label>
                        <select name="id_barbeiro" class="form-select" required>
                            <option value="">Selecione um barbeiro</option>
                            <?php if ($resultadoBarbeiros && $resultadoBarbeiros->num_rows > 0): ?>
                                <?php while ($barbeiro = $resultadoBarbeiros->fetch_assoc()): ?>
                                    <option value="<?= $barbeiro['id_barbeiro']; ?>">
                                        <?= htmlspecialchars($barbeiro['nome']); ?>
                                        <?php if (!empty($barbeiro['especialidade'])): ?>
                                            (<?= htmlspecialchars($barbeiro['especialidade']); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- DATA -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-calendar-event me-1"></i>Escolha a data</label>
                        <input type="date" name="data_agendamento" class="form-control" min="<?= date('Y-m-d'); ?>" required>
                    </div>

                    <!-- HORÁRIO -->
                    <div class="mb-4">
                        <label class="form-label"><i class="bi bi-clock me-1"></i>Escolha o horário</label>
                        <select name="horario" class="form-select" required>
                            <option value="">Selecione um horário</option>
                            <option value="09:00:00">09:00</option>
                            <option value="10:00:00">10:00</option>
                            <option value="11:00:00">11:00</option>
                            <option value="12:00:00">12:00</option>
                            <option value="13:00:00">13:00</option>
                            <option value="14:00:00">14:00</option>
                            <option value="15:00:00">15:00</option>
                            <option value="16:00:00">16:00</option>
                            <option value="17:00:00">17:00</option>
                            <option value="18:00:00">18:00</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-cortai">
                        <i class="bi bi-check-lg me-1"></i> CONFIRMAR AGENDAMENTO
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>