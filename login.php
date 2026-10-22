<?php
session_start();
include "conexao.php";

$erro = "";
$sucesso = "";

// Define qual aba/perfil está ativo por padrão ('cliente' ou 'barbeiro')
$tipo = $_GET['tipo'] ?? 'cliente';
$modo = $_GET['modo'] ?? 'login'; // 'login' ou 'cadastro'

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tipo = $_POST['tipo'] ?? 'cliente';
    $modo = $_POST['modo'] ?? 'login';

    // -------------------------------------------------------------
    // LOGICA PARA O PERFIL: BARBEIRO / ADMINISTRADOR
    // -------------------------------------------------------------
    if ($tipo === 'barbeiro') {
        $login = trim($_POST['usuario'] ?? '');
        $senha = trim($_POST['senha'] ?? '');

        if ($modo === 'login') {
            if (!empty($login) && !empty($senha)) {
                $sql = "SELECT * FROM usuarios WHERE usuario = ? LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $login);
                $stmt->execute();
                $resultado = $stmt->get_result();

                if ($resultado->num_rows === 1) {
                    $u = $resultado->fetch_assoc();
                    if (password_verify($senha, $u['senha']) || $senha === $u['senha']) {
                        $_SESSION['usuario_id'] = $u['id_usuario'];
                        $_SESSION['usuario_nome'] = $u['nome'];
                        header("Location: dashboard.php");
                        exit;
                    } else {
                        $erro = "Senha incorreta.";
                    }
                } else {
                    $erro = "Barbeiro/Usuário não encontrado.";
                }
                $stmt->close();
            } else {
                $erro = "Preencha todos os campos do barbeiro.";
            }
        } elseif ($modo === 'cadastro') {
            $nome = trim($_POST['nome'] ?? '');
            if (!empty($nome) && !empty($login) && !empty($senha)) {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $sqlInsert = "INSERT INTO usuarios (nome, usuario, senha) VALUES (?, ?, ?)";
                $stmtInsert = $conn->prepare($sqlInsert);
                $stmtInsert->bind_param("sss", $nome, $login, $senhaHash);

                if ($stmtInsert->execute()) {
                    $sucesso = "Conta de barbeiro criada com sucesso! Faça login.";
                    $modo = 'login';
                } else {
                    $erro = "Erro ao cadastrar barbeiro. O nome de usuário já existe?";
                }
                $stmtInsert->close();
            } else {
                $erro = "Preencha todos os campos para cadastrar.";
            }
        }
    } 
    // -------------------------------------------------------------
    // LÓGICA PARA O PERFIL: CLIENTE
    // -------------------------------------------------------------
    elseif ($tipo === 'cliente') {
        $telefone = trim($_POST['telefone'] ?? '');

        if ($modo === 'login') {
            if (!empty($telefone)) {
                $sql = "SELECT id_cliente, nome FROM clientes WHERE telefone = ? LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $telefone);
                $stmt->execute();
                $res = $stmt->get_result();

                if ($res->num_rows === 1) {
                    $c = $res->fetch_assoc();
                    $_SESSION['id_cliente'] = $c['id_cliente'];
                    $_SESSION['nome_cliente'] = $c['nome'];
                    header("Location: index.php");
                    exit;
                } else {
                    $erro = "Telefone não encontrado. Crie uma conta abaixo.";
                    $modo = 'cadastro';
                }
                $stmt->close();
            } else {
                $erro = "Informe seu telefone.";
            }
        } elseif ($modo === 'cadastro') {
            $nome = trim($_POST['nome'] ?? '');
            if (!empty($nome) && !empty($telefone)) {
                // Verifica se o telefone já está em uso
                $sqlCheck = "SELECT id_cliente FROM clientes WHERE telefone = ? LIMIT 1";
                $stmtCheck = $conn->prepare($sqlCheck);
                $stmtCheck->bind_param("s", $telefone);
                $stmtCheck->execute();
                if ($stmtCheck->get_result()->num_rows > 0) {
                    $erro = "Este telefone já possui cadastro. Faça login.";
                    $modo = 'login';
                } else {
                    $sqlIns = "INSERT INTO clientes (nome, telefone) VALUES (?, ?)";
                    $stmtIns = $conn->prepare($sqlIns);
                    $stmtIns->bind_param("ss", $nome, $telefone);
                    if ($stmtIns->execute()) {
                        $_SESSION['id_cliente'] = $conn->insert_id;
                        $_SESSION['nome_cliente'] = $nome;
                        header("Location: index.php");
                        exit;
                    } else {
                        $erro = "Erro ao criar conta de cliente.";
                    }
                    $stmtIns->close();
                }
                $stmtCheck->close();
            } else {
                $erro = "Preencha nome e telefone.";
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
    <title>Cortaí - Acesso ao Sistema</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { 
            background-color: #111516; 
            color: white; 
            font-family: Arial, sans-serif; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0; 
            padding: 20px;
        }
        .card-login { 
            background-color: #191e1f; 
            border: 1px solid #303a3b; 
            border-radius: 14px; 
            padding: 35px; 
            width: 100%; 
            max-width: 440px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }
        .titulo { text-align: center; margin-bottom: 20px; font-weight: bold; }
        .titulo span { color: #719999; }
        
        /* Abas de Navegação */
        .nav-pills .nav-link {
            color: #8f9997;
            background-color: #111516;
            border: 1px solid #303a3b;
            font-weight: 600;
        }
        .nav-pills .nav-link.active {
            background-color: #719999;
            color: #111516;
        }

        .form-control { background-color: #111516; border: 1px solid #3b4547; color: white; padding: 12px; }
        .form-control:focus { background-color: #111516; color: white; border-color: #719999; box-shadow: none; }
        
        .btn-cortai { background-color: #719999; color: #111516; font-weight: bold; border: none; width: 100%; padding: 12px; margin-top: 10px; }
        .btn-cortai:hover { background-color: #86aaaa; color: #111516; }
        
        .alert-erro { background-color: #3d1c1d; border: 1px solid #842029; color: #ea868f; padding: 10px; border-radius: 6px; font-size: 14px; text-align: center; }
        .alert-sucesso { background-color: #1c3d26; border: 1px solid #20843d; color: #86ea9a; padding: 10px; border-radius: 6px; font-size: 14px; text-align: center; }
        
        .link-alternar { color: #719999; text-decoration: none; font-weight: 600; }
        .link-alternar:hover { text-decoration: underline; color: #86aaaa; }
    </style>
</head>
<body>

<div class="card-login">
    <h3 class="titulo">Cortaí <span>Barbearia</span></h3>

    <!-- Abas Cliente vs Barbeiro -->
    <ul class="nav nav-pills nav-justified mb-4">
        <li class="nav-item">
            <a class="nav-link <?= $tipo === 'cliente' ? 'active' : ''; ?>" href="login.php?tipo=cliente&modo=<?= $modo; ?>">
                <i class="bi bi-person me-1"></i> Cliente
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tipo === 'barbeiro' ? 'active' : ''; ?>" href="login.php?tipo=barbeiro&modo=<?= $modo; ?>">
                <i class="bi bi-scissors me-1"></i> Barbeiro
            </a>
        </li>
    </ul>

    <!-- Mensagens -->
    <?php if (!empty($erro)): ?>
        <div class="alert-erro mb-3"><?= $erro; ?></div>
    <?php endif; ?>
    <?php if (!empty($sucesso)): ?>
        <div class="alert-sucesso mb-3"><?= $sucesso; ?></div>
    <?php endif; ?>

    <!-- FORMULÁRIO DO CLIENTE -->
    <?php if ($tipo === 'cliente'): ?>
        <form method="POST" action="login.php">
            <input type="hidden" name="tipo" value="cliente">
            <input type="hidden" name="modo" value="<?= $modo; ?>">

            <h5 class="text-center mb-3" style="color: #719999;">
                <?= $modo === 'login' ? 'Entrar como Cliente' : 'Criar Conta de Cliente'; ?>
            </h5>

            <?php if ($modo === 'cadastro'): ?>
                <div class="mb-3">
                    <label class="form-label text-muted small">Nome Completo</label>
                    <input type="text" name="nome" class="form-control" placeholder="Seu nome" required>
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <label class="form-label text-muted small">Telefone / WhatsApp</label>
                <input type="tel" name="telefone" class="form-control" placeholder="(14) 99999-9999" required>
            </div>

            <button type="submit" class="btn btn-cortai">
                <?= $modo === 'login' ? 'ENTRAR' : 'CRIAR MINHA CONTA'; ?>
            </button>

            <div class="text-center mt-3 small text-muted">
                <?php if ($modo === 'login'): ?>
                    Ainda não tem conta? <a href="login.php?tipo=cliente&modo=cadastro" class="link-alternar">Criar conta</a>
                <?php else: ?>
                    Já tem conta? <a href="login.php?tipo=cliente&modo=login" class="link-alternar">Fazer Login</a>
                <?php endif; ?>
            </div>
        </form>

    <!-- FORMULÁRIO DO BARBEIRO / ADMIN -->
    <?php else: ?>
        <form method="POST" action="login.php">
            <input type="hidden" name="tipo" value="barbeiro">
            <input type="hidden" name="modo" value="<?= $modo; ?>">

            <h5 class="text-center mb-3" style="color: #719999;">
                <?= $modo === 'login' ? 'Painel Barbeiro / Admin' : 'Cadastrar Novo Barbeiro'; ?>
            </h5>

            <?php if ($modo === 'cadastro'): ?>
                <div class="mb-3">
                    <label class="form-label text-muted small">Nome do Barbeiro</label>
                    <input type="text" name="nome" class="form-control" placeholder="Ex: Carlos Barbeiro" required>
                </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label text-muted small">Usuário</label>
                <input type="text" name="usuario" class="form-control" placeholder="admin" required>
            </div>

            <div class="mb-4">
                <label class="form-label text-muted small">Senha</label>
                <input type="password" name="senha" class="form-control" placeholder="******" required>
            </div>

            <button type="submit" class="btn btn-cortai">
                <?= $modo === 'login' ? 'ENTRAR NO PAINEL' : 'CADASTRAR BARBEIRO'; ?>
            </button>

            <div class="text-center mt-3 small text-muted">
                <?php if ($modo === 'login'): ?>
                    Novo barbeiro? <a href="login.php?tipo=barbeiro&modo=cadastro" class="link-alternar">Criar conta</a>
                <?php else: ?>
                    Já tem acesso? <a href="login.php?tipo=barbeiro&modo=login" class="link-alternar">Fazer Login</a>
                <?php endif; ?>
            </div>
        </form>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>