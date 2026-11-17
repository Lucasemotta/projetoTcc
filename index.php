<?php
// Página inicial pública: apresenta o Cortaí e as barbearias em destaque.
require "agenda_lib.php";
require "conexao.php";
$destaques = array_slice(barbearias_todas($conn), 0, 3);
usort($destaques, fn($a, $b) => ((float) $b['media']) <=> ((float) $a['media']));
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Agende seu corte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/cortai.css">
</head>
<body>
<nav class="navbar navbar-cortai">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand m-0" href="index.php"><img src="logo.png" alt="Cortaí" class="logo"></a>
        <a href="entrar.php" class="btn btn-cortai-outline btn-sm px-3">Entrar</a>
    </div>
</nav>

<header class="hero container">
    <h1>Seu próximo corte <span>começa aqui.</span></h1>
    <p class="subtitulo my-4 mx-auto" style="max-width:560px;font-size:16px">Encontre barbearias perto de você, escolha o serviço e o barbeiro, e agende em menos de um minuto.</p>
    <div class="d-flex flex-wrap gap-2 justify-content-center">
        <a href="agenda.php" class="btn btn-cortai px-4">AGENDAR AGORA</a>
        <a href="entrar.php?tipo=barbeiro&modo=cadastro" class="btn btn-cortai-outline px-4 d-flex align-items-center">Sou barbeiro: cadastrar minha barbearia</a>
    </div>
</header>

<section class="container pb-4">
    <div class="row g-3">
        <?php foreach ([['geo-alt','1. Escolha a barbearia','Veja no mapa, busque por nome ou use "perto de mim".'],['scissors','2. Serviço e horário','Só aparecem barbeiros e horários realmente livres.'],['check2-circle','3. Pronto','Receba o comprovante e acompanhe em "Meus agendamentos".']] as [$ic, $t, $d]): ?>
            <div class="col-md-4"><div class="passo"><i class="bi bi-<?= $ic; ?>"></i><h2 class="h6 mt-2"><?= $t; ?></h2><p class="subtitulo mb-0"><?= $d; ?></p></div></div>
        <?php endforeach; ?>
    </div>
</section>

<section class="container pb-5">
    <h2 class="titulo h4 mb-3">Barbearias em <span>destaque</span></h2>
    <div class="row g-3">
        <?php foreach ($destaques as $b): ?>
            <div class="col-md-4"><div class="card-cortai p-3 h-100">
                <?php if (!empty($b['foto'])): ?><img src="<?= htmlspecialchars($b['foto']); ?>" alt="" class="foto-barbearia"><?php endif; ?>
                <strong><?= htmlspecialchars($b['nome']); ?></strong> <?= estrelas($b); ?>
                <div class="subtitulo"><?= htmlspecialchars($b['endereco']); ?></div>
                <a href="agenda.php?b=<?= (int) $b['id_barbearia']; ?>" class="btn btn-cortai btn-sm mt-3">Agendar aqui</a>
            </div></div>
        <?php endforeach; ?>
        <?php if (!$destaques): ?><p class="subtitulo">Nenhuma barbearia cadastrada ainda. Seja o primeiro!</p><?php endif; ?>
    </div>
</section>
<footer class="rodape"><div class="container">© 2026 Cortaí — Mais que um corte, é estilo.</div></footer>
</body>
</html>
