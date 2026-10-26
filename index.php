<?php
require "agenda_lib.php";   // inicia a sessão e traz as regras da agenda
require "conexao.php";

// "Não sou eu": solta o cliente da sessão e volta à tela de entrada.
if (isset($_GET['trocar'])) {
    unset($_SESSION['id_cliente'], $_SESSION['nome_cliente']);
    header("Location: index.php");
    exit;
}

$erro = agenda_flash() ?? "";   // aviso vindo de outra tela (ex.: sessão expirada)
$sucesso = "";

$tipo = (($_GET['tipo'] ?? 'cliente') === 'barbeiro') ? 'barbeiro' : 'cliente';
$modo = (($_GET['modo'] ?? 'login') === 'cadastro') ? 'cadastro' : 'login';

// Quem já está identificado não precisa ver o formulário de novo.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    if ($tipo === 'cliente' && !empty($_SESSION['id_cliente'])) {
        header("Location: agenda.php");
        exit;
    }
    if ($tipo === 'barbeiro' && !empty($_SESSION['usuario_id'])) {
        header("Location: dashboard.php");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tipo = (($_POST['tipo'] ?? 'cliente') === 'barbeiro') ? 'barbeiro' : 'cliente';
    $modo = (($_POST['modo'] ?? 'login') === 'cadastro') ? 'cadastro' : 'login';

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
                        session_regenerate_id(true);
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

                try {
                    $stmtInsert->execute();
                    $sucesso = "Barbeiro cadastrado! Faça login.";
                    $modo = 'login';
                } catch (mysqli_sql_exception $e) {
                    $erro = "Esse usuário já existe. Escolha outro.";
                }
                $stmtInsert->close();
            } else {
                $erro = "Preencha todos os campos.";
            }
        }
    }
    elseif ($tipo === 'cliente') {
        $nome_input     = trim($_POST['nome'] ?? '');
        $telefone_input = trim($_POST['telefone'] ?? '');
        $digitos        = agenda_digitos($telefone_input);

        if ($digitos === '') {
            $erro = "Informe seu telefone.";
        } elseif (!agenda_telefone_valido($digitos)) {
            $erro = "Informe um telefone válido com DDD, por exemplo (14) 99999-9999.";
        } elseif ($modo === 'login') {
            $c = cliente_por_telefone($conn, $digitos);
            if ($c) {
                session_regenerate_id(true);
                $_SESSION['id_cliente']   = (int) $c['id_cliente'];
                $_SESSION['nome_cliente'] = $c['nome'];
                header("Location: agenda.php");
                exit;
            }
            $erro = "Telefone não encontrado. Crie sua conta abaixo.";
            $modo = 'cadastro';
        } else { // cadastro
            if (!preg_match('/^.{1,100}$/su', $nome_input)) {
                $erro = "Informe seu nome completo.";
            } elseif (cliente_por_telefone($conn, $digitos)) {
                $erro = "Este telefone já possui cadastro. Faça login.";
                $modo = 'login';
            } else {
                $telefoneSalvo = agenda_formatar_telefone($digitos);
                $stmtIns = $conn->prepare("INSERT INTO clientes (nome, telefone) VALUES (?, ?)");
                $stmtIns->bind_param("ss", $nome_input, $telefoneSalvo);
                if ($stmtIns->execute()) {
                    session_regenerate_id(true);
                    $_SESSION['id_cliente']   = (int) $conn->insert_id;
                    $_SESSION['nome_cliente'] = $nome_input;
                    header("Location: agenda.php");
                    exit;
                }
                $erro = "Não foi possível criar a conta agora. Tente novamente.";
                $stmtIns->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Agendamento</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/cortai.css">

    <style>
        /* Só o que é exclusivo da tela de entrada (cores e campos vêm do cortai.css) */
        body { min-height: 100vh; display: flex; flex-direction: column; }
        .wrapper-split { flex-grow: 1; display: flex; align-items: center; justify-content: center; padding: 40px 15px; }
        .card-split { width: 100%; max-width: 950px; padding: 0; overflow: hidden; }

        .col-banner { background: linear-gradient(135deg, #182223 0%, #111516 100%); padding: 50px 40px; display: flex; flex-direction: column; justify-content: center; border-right: 1px solid var(--cortai-border-soft); }
        .col-banner h2 { font-size: 36px; font-weight: 700; line-height: 1.2; }
        .col-banner h2 span { color: var(--cortai-accent); }
        .col-banner p { color: #9da8a6; font-size: 15px; margin-top: 15px; line-height: 1.6; }
        .banner-item { display: flex; align-items: center; gap: 12px; margin-top: 20px; color: #d1d8d6; font-size: 14px; }
        .banner-item i { color: var(--cortai-accent); font-size: 22px; }

        .col-form { padding: 45px 40px; display: flex; flex-direction: column; justify-content: center; }
        .col-form h3 { font-weight: 700; font-size: 26px; margin-bottom: 5px; }

        .abas { display: flex; background: var(--cortai-bg); border: 1px solid var(--cortai-border); padding: 4px; border-radius: 30px; margin-bottom: 25px; }
        .aba { flex: 1; text-align: center; padding: 10px 14px; border-radius: 26px; font-size: 13px; font-weight: 600; color: var(--cortai-muted); text-decoration: none; transition: background-color .2s, color .2s; }
        .aba:hover { color: #fff; }
        .aba.active { background: var(--cortai-accent); color: var(--cortai-bg); }

        .campo-icone { position: relative; margin-bottom: 18px; }
        .campo-icone > i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--cortai-muted); font-size: 18px; pointer-events: none; }
        .campo-icone .form-control { padding-left: 46px; }

        .link-rodape { color: var(--cortai-accent); font-weight: 600; text-decoration: none; }
        .link-rodape:hover { color: var(--cortai-accent-hover); text-decoration: underline; }
        .alert-sucesso { background: #14301f; border: 1px solid #1f6b3e; color: #7fd6a3; border-radius: 6px; padding: 10px 14px; font-size: 14px; }

        @media (max-width: 767.98px) {
            .col-banner { padding: 30px 22px; text-align: center; border-right: 0; border-bottom: 1px solid var(--cortai-border-soft); }
            .col-banner h2 { font-size: 26px; }
            .banner-item { justify-content: center; margin-top: 14px; }
            .col-form { padding: 28px 20px; }
            .wrapper-split { padding: 20px 12px; align-items: flex-start; }
        }
    </style>
</head>

<body>

<nav class="navbar navbar-cortai">
    <div class="container">
        <a class="navbar-brand m-0" href="index.php">
            <img src="logo.png" alt="Cortaí" class="logo">
        </a>
    </div>
</nav>

<div class="wrapper-split">
    <div class="card-cortai card-split">
        <div class="row g-0">

            <div class="col-md-6 col-banner">
                <h2>Bem-vindo ao <span>Cortaí!</span></h2>
                <p>Agende seus cortes, escolha seus profissionais favoritos e acompanhe seus horários em um só lugar.</p>

                <div class="mt-3">
                    <div class="banner-item"><i class="bi bi-calendar-check-fill"></i><span>Agendamento simples e sem filas</span></div>
                    <div class="banner-item"><i class="bi bi-scissors"></i><span>Escolha os melhores profissionais</span></div>
                    <div class="banner-item"><i class="bi bi-clock-fill"></i><span>Horários flexíveis para o seu dia</span></div>
                </div>
            </div>

            <div class="col-md-6 col-form">

                <div class="abas">
                    <a class="aba <?= $tipo === 'cliente' ? 'active' : ''; ?>" href="index.php?tipo=cliente&modo=<?= $modo; ?>">
                        <i class="bi bi-person me-1"></i> Cliente
                    </a>
                    <a class="aba <?= $tipo === 'barbeiro' ? 'active' : ''; ?>" href="index.php?tipo=barbeiro&modo=<?= $modo; ?>">
                        <i class="bi bi-scissors me-1"></i> Barbeiro
                    </a>
                </div>

                <h3><?= $modo === 'login' ? 'Entrar' : 'Criar conta'; ?></h3>
                <p class="subtitulo mb-4">
                    <?= $modo === 'login' ? 'Informe suas credenciais para continuar.' : 'Preencha seus dados para começar.'; ?>
                </p>

                <?php if (!empty($erro)): ?>
                    <div class="alert-cortai mb-3" role="alert"><?= htmlspecialchars($erro); ?></div>
                <?php endif; ?>
                <?php if (!empty($sucesso)): ?>
                    <div class="alert-sucesso mb-3" role="status"><?= htmlspecialchars($sucesso); ?></div>
                <?php endif; ?>

                <?php if ($tipo === 'cliente'): ?>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="tipo" value="cliente">
                        <input type="hidden" name="modo" value="<?= $modo; ?>">

                        <?php if ($modo === 'cadastro'): ?>
                            <div class="campo-icone">
                                <i class="bi bi-person"></i>
                                <input type="text" name="nome" class="form-control" maxlength="100" autocomplete="name"
                                       placeholder="Nome completo" aria-label="Nome completo" required>
                            </div>
                        <?php endif; ?>

                        <div class="campo-icone">
                            <i class="bi bi-whatsapp"></i>
                            <input type="tel" name="telefone" id="telefone" class="form-control" maxlength="15" inputmode="tel"
                                   autocomplete="tel-national" placeholder="(14) 99999-9999" aria-label="Telefone / WhatsApp" required>
                        </div>

                        <button type="submit" class="btn btn-cortai w-100 mt-1">
                            <?= $modo === 'login' ? 'ENTRAR' : 'FINALIZAR CADASTRO'; ?>
                        </button>

                        <div class="text-center mt-4 small subtitulo">
                            <?php if ($modo === 'login'): ?>
                                Novo por aqui? <a href="index.php?tipo=cliente&modo=cadastro" class="link-rodape">Crie uma conta</a>
                            <?php else: ?>
                                Já possui conta? <a href="index.php?tipo=cliente&modo=login" class="link-rodape">Faça login</a>
                            <?php endif; ?>
                        </div>
                    </form>

                <?php else: ?>
                    <form method="POST" action="index.php">
                        <input type="hidden" name="tipo" value="barbeiro">
                        <input type="hidden" name="modo" value="<?= $modo; ?>">

                        <?php if ($modo === 'cadastro'): ?>
                            <div class="campo-icone">
                                <i class="bi bi-person"></i>
                                <input type="text" name="nome" class="form-control" maxlength="100" autocomplete="name"
                                       placeholder="Nome do barbeiro" aria-label="Nome do barbeiro" required>
                            </div>
                        <?php endif; ?>

                        <div class="campo-icone">
                            <i class="bi bi-person-badge"></i>
                            <input type="text" name="usuario" class="form-control" maxlength="50" autocomplete="username"
                                   autocapitalize="none" placeholder="Usuário (ex: admin)" aria-label="Usuário" required>
                        </div>

                        <div class="campo-icone">
                            <i class="bi bi-lock"></i>
                            <input type="password" name="senha" class="form-control"
                                   autocomplete="<?= $modo === 'login' ? 'current-password' : 'new-password'; ?>"
                                   placeholder="Sua senha" aria-label="Senha" required>
                        </div>

                        <button type="submit" class="btn btn-cortai w-100 mt-1">
                            <?= $modo === 'login' ? 'ENTRAR NO PAINEL' : 'CADASTRAR'; ?>
                        </button>

                        <div class="text-center mt-4 small subtitulo">
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

<footer class="rodape" id="contato">
    <div class="container">
        <div class="row align-items-center text-center text-md-start gy-2">
            <div class="col-md-6">
                <span class="d-block">© 2026 Cortaí - Todos os direitos reservados.</span>
                <span>Mais que um corte, é estilo.</span>
            </div>
            <div class="col-md-6 text-md-end">
                <span class="d-block"><i class="bi bi-geo-alt me-1" style="color: var(--cortai-accent);"></i> Rua da Barbearia, 123 - Centro</span>
                <span><i class="bi bi-whatsapp me-1" style="color: var(--cortai-accent);"></i> (14) 99999-9999</span>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Máscara de telefone enquanto digita: (14) 99999-9999
    const tel = document.getElementById('telefone');
    if (tel) tel.addEventListener('input', function () {
        const d = this.value.replace(/\D/g, '').slice(0, 11);
        let v = d;
        if (d.length > 10)     v = '(' + d.slice(0, 2) + ') ' + d.slice(2, 7) + '-' + d.slice(7);
        else if (d.length > 6) v = '(' + d.slice(0, 2) + ') ' + d.slice(2, 6) + '-' + d.slice(6);
        else if (d.length > 2) v = '(' + d.slice(0, 2) + ') ' + d.slice(2);
        else if (d.length > 0) v = '(' + d;
        this.value = v;
    });
</script>
</body>
</html>
