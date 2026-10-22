<?php
session_start();
include "conexao.php";

$erro = "";
$sucesso = "";

$tipo = $_GET['tipo'] ?? 'cliente'; 
$modo = $_GET['modo'] ?? 'login';   

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tipo = $_POST['tipo'] ?? 'cliente';
    $modo = $_POST['modo'] ?? 'login';

    if ($tipo === 'barbeiro') {
        $usuario_input = trim($_POST['usuario'] ?? '');
        $senha_input   = trim($_POST['senha'] ?? '');

        if ($modo === 'login') {
            if (!empty($usuario_input) && !empty($senha_input)) {
                $sql = "SELECT * FROM usuarios WHERE usuario = ? LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $usuario_input);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows === 1) {
                    $u = $resultado->fetch_assoc();
                    if (password_verify($senha_input, $u['senha']) || $senha_input === $u['senha']) {
                        $_SESSION['usuario_id']   = $u['id_usuario'];
                        $_SESSION['usuario_nome'] = $u['nome'];
                        header("Location: dashboard.php");
                        exit;
                    } else {
                        $erro = "Senha incorreta.";
                    }
                } else {
                    $erro = "Barbeiro não encontrado.";
                }
                $stmt->close();
            } else {
                $erro = "Preencha todos os campos.";
            }
        } elseif ($modo === 'cadastro') {
            $nome_input = trim($_POST['nome'] ?? '');
            if (!empty($nome_input) && !empty($usuario_input) && !empty($senha_input)) {
                $senhaHash = password_hash($senha_input, PASSWORD_DEFAULT);
                $sqlInsert = "INSERT INTO usuarios (nome, usuario, senha) VALUES (?, ?, ?)";
                $stmtInsert = $conn->prepare($sqlInsert);
                $stmtInsert->bind_param("sss", $nome_input, $usuario_input, $senhaHash);

                if ($stmtInsert->execute()) {
                    $sucesso = "Barbeiro cadastrado! Faça login.";
                    $modo = 'login';
                } else {
                    $erro = "Erro ao cadastrar. O usuário já existe?";
                }
                $stmtInsert->close();
            } else {
                $erro = "Preencha todos os campos.";
            }
        }
    } 
    elseif ($tipo === 'cliente') {
        $telefone_input = trim($_POST['telefone'] ?? '');

        if ($modo === 'login') {
            if (!empty($telefone_input)) {
                $sql = "SELECT id_cliente, nome FROM clientes WHERE telefone = ? LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $telefone_input);
                $stmt->execute();
                $res = $stmt->get_result();

                if ($res->num_rows === 1) {
                    $c = $res->fetch_assoc();
                    $_SESSION['id_cliente']   = $c['id_cliente'];
                    $_SESSION['nome_cliente'] = $c['nome'];
                    header("Location: agenda.php");
                    exit;
                } else {
                    $erro = "Telefone não encontrado. Crie sua conta abaixo.";
                    $modo = 'cadastro';
                }
                $stmt->close();
            } else {
                $erro = "Informe seu telefone.";
            }
        } elseif ($modo === 'cadastro') {
            $nome_input = trim($_POST['nome'] ?? '');
            if (!empty($nome_input) && !empty($telefone_input)) {
                $sqlCheck = "SELECT id_cliente FROM clientes WHERE telefone = ? LIMIT 1";
                $stmtCheck = $conn->prepare($sqlCheck);
                $stmtCheck->bind_param("s", $telefone_input);
                $stmtCheck->execute();
                if ($stmtCheck->get_result()->num_rows > 0) {
                    $erro = "Este telefone já possui cadastro. Faça login.";
                    $modo = 'login';
                } else {
                    $sqlIns = "INSERT INTO clientes (nome, telefone) VALUES (?, ?)";
                    $stmtIns = $conn->prepare($sqlIns);
                    $stmtIns->bind_param("ss", $nome_input, $telefone_input);
                    if ($stmtIns->execute()) {
                        $_SESSION['id_cliente']   = $conn->insert_id;
                        $_SESSION['nome_cliente'] = $nome_input;
                        header("Location: agenda.php");
                        exit;
                    } else {
                        $erro = "Erro ao cadastrar cliente.";
                    }
                    $stmtIns->close();
                }
                $stmtCheck->close();
            } else {
                $erro = "Preencha o nome e o telefone.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Agendamento</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Fonte Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { 
            margin: 0; 
            background-color: #111516; 
            color: #f4f4f1; 
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar-cortai { background-color: #111516; border-bottom: 1px solid #293436; padding: 12px 0; }
        .logo { height: 50px; width: auto; }
        .nav-link { color: #c8cecc !important; font-size: 14px; margin-left: 20px; transition: 0.3s; }
        .nav-link:hover { color: #719999 !important; }

        /* CARD SPLIT SCREEN (ESTILO IMAGEM) */
        .wrapper-split {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-grow: 1;
            padding: 40px 15px;
        }

        .card-split {
            width: 100%;
            max-width: 950px;
            background-color: #191e1f;
            border: 1px solid #303a3b;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.5);
        }

        /* LADO ESQUERDO - DESTAQUE / BOAS-VINDAS */
        .col-banner {
            background: linear-gradient(135deg, #182223 0%, #111516 100%);
            padding: 50px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            border-right: 1px solid #263032;
        }

        .col-banner h2 { font-size: 38px; font-weight: 700; color: #ffffff; line-height: 1.2; }
        .col-banner h2 span { color: #719999; }
        .col-banner p { color: #9da8a6; font-size: 15px; margin-top: 15px; line-height: 1.6; }

        .banner-item { display: flex; align-items: center; gap: 12px; margin-top: 20px; color: #d1d8d6; font-size: 14px; }
        .banner-item i { color: #719999; font-size: 22px; }

        /* LADO DIREITO - FORMULÁRIO PARECIDO COM A REFERÊNCIA */
        .col-form {
            background-color: #ffffff;
            color: #111516;
            padding: 45px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .col-form h3 { font-weight: 700; color: #111516; margin-bottom: 5px; font-size: 26px; }
        .col-form p.subtitulo { color: #6c757d; font-size: 14px; margin-bottom: 25px; }

        /* ABAS CLIENTE / BARBEIRO */
        .nav-pills-custom {
            background-color: #f1f3f4;
            padding: 5px;
            border-radius: 30px;
            margin-bottom: 25px;
            display: flex;
        }

        .nav-pills-custom .nav-link-custom {
            flex: 1;
            text-align: center;
            padding: 8px 15px;
            border-radius: 25px;
            font-size: 13px;
            font-weight: 600;
            color: #5f6368;
            text-decoration: none;
            transition: 0.3s;
        }

        .nav-pills-custom .nav-link-custom.active {
            background-color: #719999;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(113, 153, 153, 0.3);
        }

        /* INPUTS ARREDONDADOS COM ÍCONES */
        .input-group-custom {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group-custom i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #8c9795;
            font-size: 18px;
            z-index: 10;
        }

        .form-control-custom {
            width: 100%;
            padding: 12px 18px 12px 48px;
            border-radius: 30px;
            border: 1px solid #ced4da;
            background-color: #f8f9fa;
            color: #212529;
            font-size: 14px;
            transition: 0.3s;
        }

        .form-control-custom:focus {
            outline: none;
            border-color: #719999;
            background-color: #ffffff;
            box-shadow: 0 0 0 0.2rem rgba(113, 153, 153, 0.2);
        }

        /* BOTÃO PRINCIPAL ARREDONDADO */
        .btn-split {
            width: 100%;
            padding: 13px;
            border-radius: 30px;
            border: none;
            background-color: #719999;
            color: #ffffff;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.5px;
            transition: 0.3s;
            box-shadow: 0 8px 20px rgba(113, 153, 153, 0.3);
        }

        .btn-split:hover {
            background-color: #5c8282;
            color: #ffffff;
        }

        .link-rodape {
            color: #719999;
            font-weight: 600;
            text-decoration: none;
        }

        .link-rodape:hover { text-decoration: underline; }

        .alert-erro { background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px 15px; border-radius: 20px; font-size: 13px; text-align: center; margin-bottom: 20px; }
        .alert-sucesso { background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 10px 15px; border-radius: 20px; font-size: 13px; text-align: center; margin-bottom: 20px; }

        .footer { border-top: 1px solid #293436; padding: 20px 0; color: #69716f; font-size: 13px; }

        @media (max-width: 767px) {
            .col-banner { padding: 35px 25px; text-align: center; }
            .col-form { padding: 35px 25px; }
            .col-banner h2 { font-size: 28px; }
        }
    </style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-cortai sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#inicio">
            <img src="logo.jpg" alt="Cortaí" class="logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            
        </div>
    </div>
</nav>

<!-- ÁREA CENTRAL COM O CARD SPLIT -->
<div class="wrapper-split" id="inicio">
    <div class="card-split">
        <div class="row g-0">
            
            <!-- LADO ESQUERDO: BOAS-VINDAS / INFORMAÇÕES -->
            <div class="col-md-6 col-banner">
                <h2>Bem-vindo de volta ao <span>Cortaí!</span></h2>
                <p>Agende seus cortes, escolha seus profissionais favoritos e acompanhe seus horários em um só lugar.</p>

                <div class="mt-3">
                    <div class="banner-item">
                        <i class="bi bi-calendar-check-fill"></i>
                        <span>Agendamento simples e sem filas</span>
                    </div>
                    <div class="banner-item">
                        <i class="bi bi-scissors"></i>
                        <span>Escolha os melhores profissionais</span>
                    </div>
                    <div class="banner-item">
                        <i class="bi bi-clock-fill"></i>
                        <span>Horários flexíveis para o seu dia</span>
                    </div>
                </div>
            </div>

            <!-- LADO DIREITO: FORMULÁRIO ESTILO MODERNO -->
            <div class="col-md-6 col-form">
                
                <!-- Abas para alternar Cliente / Barbeiro -->
                <div class="nav-pills-custom">
                    <a class="nav-link-custom <?= $tipo === 'cliente' ? 'active' : ''; ?>" href="index.php?tipo=cliente&modo=<?= $modo; ?>">
                        <i class="bi bi-person me-1"></i> Cliente
                    </a>
                    <a class="nav-link-custom <?= $tipo === 'barbeiro' ? 'active' : ''; ?>" href="index.php?tipo=barbeiro&modo=<?= $modo; ?>">
                        <i class="bi bi-scissors me-1"></i> Barbeiro
                    </a>
                </div>

                <h3><?= $modo === 'login' ? 'Entrar' : 'Criar Conta'; ?></h3>
                <p class="subtitulo">
                    <?= $modo === 'login' ? 'Informe suas credenciais para continuar.' : 'Preencha seus dados para começar.'; ?>
                </p>

                <!-- Mensagens de Alerta -->
                <?php if (!empty($erro)): ?>
                    <div class="alert-erro"><?= $erro; ?></div>
                <?php endif; ?>
                <?php if (!empty($sucesso)): ?>
                    <div class="alert-sucesso"><?= $sucesso; ?></div>
                <?php endif; ?>

                <!-- PERFIL CLIENTE -->
                <?php if ($tipo === 'cliente'): ?>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="tipo" value="cliente">
                        <input type="hidden" name="modo" value="<?= $modo; ?>">

                        <?php if ($modo === 'cadastro'): ?>
                            <div class="input-group-custom">
                                <i class="bi bi-person"></i>
                                <input type="text" name="nome" class="form-control-custom" placeholder="Nome completo" required>
                            </div>
                        <?php endif; ?>

                        <div class="input-group-custom">
                            <i class="bi bi-whatsapp"></i>
                            <input type="tel" name="telefone" class="form-control-custom" placeholder="Telefone / WhatsApp" required>
                        </div>

                        <button type="submit" class="btn-split mt-2">
                            <?= $modo === 'login' ? 'ENTRAR' : 'FINALIZAR CADASTRO'; ?>
                        </button>

                        <div class="text-center mt-4 small" style="color: #6c757d;">
                            <?php if ($modo === 'login'): ?>
                                Novo por aqui? <a href="index.php?tipo=cliente&modo=cadastro" class="link-rodape">Crie uma conta</a>
                            <?php else: ?>
                                Já possui conta? <a href="index.php?tipo=cliente&modo=login" class="link-rodape">Faça login</a>
                            <?php endif; ?>
                        </div>
                    </form>

                <!-- PERFIL BARBEIRO -->
                <?php else: ?>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="tipo" value="barbeiro">
                        <input type="hidden" name="modo" value="<?= $modo; ?>">

                        <?php if ($modo === 'cadastro'): ?>
                            <div class="input-group-custom">
                                <i class="bi bi-person"></i>
                                <input type="text" name="nome" class="form-control-custom" placeholder="Nome do Barbeiro" required>
                            </div>
                        <?php endif; ?>

                        <div class="input-group-custom">
                            <i class="bi bi-person-badge"></i>
                            <input type="text" name="usuario" class="form-control-custom" placeholder="Usuário (ex: admin)" required>
                        </div>

                        <div class="input-group-custom">
                            <i class="bi bi-lock"></i>
                            <input type="password" name="senha" class="form-control-custom" placeholder="Sua senha" required>
                        </div>

                        <button type="submit" class="btn-split mt-2">
                            <?= $modo === 'login' ? 'ENTRAR NO PAINEL' : 'CADASTRAR'; ?>
                        </button>

                        <div class="text-center mt-4 small" style="color: #6c757d;">
                            <?php if ($modo === 'login'): ?>
                                Novo barbeiro? <a href="index.php?tipo=barbeiro&modo=cadastro" class="link-rodape">Crie uma conta</a>
                            <?php else: ?>
                                Já possui acesso? <a href="index.php?tipo=barbeiro&modo=login" class="link-rodape">Faça login</a>
                            <?php endif; ?>
                        </div>
                    </form>
                <?php endif; ?>

            </div>

        </div>
    </div>
</div>

<!-- RODAPÉ / CONTATO -->
<footer class="footer" id="contato">
    <div class="container">
        <div class="row align-items-center text-center text-md-start">
            <div class="col-md-6 mb-3 mb-md-0">
                <span class="d-block mb-1">© 2026 Cortaí - Todos os direitos reservados.</span>
                <span class="text-muted">Mais que um corte, é estilo.</span>
            </div>
            <div class="col-md-6 text-md-end">
                <span class="d-block mb-1"><i class="bi bi-geo-alt me-1" style="color: #719999;"></i> Rua da Barbearia, 123 - Centro</span>
                <span><i class="bi bi-whatsapp me-1" style="color: #719999;"></i> (14) 99999-9999</span>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>