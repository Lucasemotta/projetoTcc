<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Cadastro Cliente</title>

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

        .form-control {
            background-color: #111516;
            border: 1px solid #3b4547;
            color: white;
            padding: 10px 12px;
        }

        .form-control:focus {
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

        .voltar-site {
            text-align: center;
            margin-top: 20px;
        }

        .voltar-site a {
            color: #8f9997;
            text-decoration: none;
            font-size: 13px;
            transition: 0.3s;
        }

        .voltar-site a:hover {
            color: #719999;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card-cadastro">

                    <h2 class="titulo">
                        Cadastro de <span>Cliente</span>
                    </h2>

                    <!-- Alerta de Sucesso após cadastro -->
                    <?php if (isset($_GET['sucesso'])): ?>
                        <div class="alert alert-success text-center py-2 style='font-size: 14px;'" role="alert">
                            <i class="bi bi-check-circle me-1"></i> Cadastro realizado com sucesso!
                        </div>
                    <?php endif; ?>

                    <form action="salvar_cliente.php" method="POST">

                        <div class="mb-3">
                            <label class="form-label">Nome completo</label>
                            <input type="text" name="nome" class="form-control" placeholder="Digite seu nome" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Telefone / WhatsApp</label>
                            <input type="tel" name="telefone" class="form-control" placeholder="(14) 99999-9999" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">E-mail (opcional)</label>
                            <input type="email" name="email" class="form-control" placeholder="Digite seu e-mail">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-cortai">
                                <i class="bi bi-person-check-fill me-1"></i> CADASTRAR E CONTINUAR
                            </button>
                        </div>

                        <div class="voltar-site">
                            <a href="agenda.php"><i class="bi bi-arrow-left me-1"></i> Voltar para o agendamento</a>
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