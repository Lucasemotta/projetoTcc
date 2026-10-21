<?php
session_start();

// (Opcional) Garante que apenas usuários logados acessem a página
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Cadastro Barbeiro</title>

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

        .card-cadastro {
            background-color: #191e1f;
            border: 1px solid #303a3b;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        .titulo {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .titulo span {
            color: #719999;
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

        .btn-cortai {
            background-color: #719999;
            color: #111516;
            font-weight: bold;
            border: none;
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
            <div class="col-md-6">
                <a href="barbeiros.php" class="btn btn-voltar btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Voltar para a lista
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card-cadastro">

                    <h2 class="titulo">
                        Cadastro de <span>Barbeiro</span>
                    </h2>

                    <!-- Alerta de Sucesso após envio -->
                    <?php if (isset($_GET['sucesso'])): ?>
                        <div class="alert alert-success text-center py-2 style='font-size: 14px;'" role="alert">
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