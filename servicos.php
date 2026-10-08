<?php
// Lista de serviços: altere os valores aqui quando precisar
$servicos = [
    1 => ['nome' => 'Cabelo',      'descricao' => 'Corte masculino com acabamento', 'preco' => 40.00],
    2 => ['nome' => 'Barba',       'descricao' => 'Barba feita com toalha quente',  'preco' => 30.00],
    3 => ['nome' => 'Sobrancelha', 'descricao' => 'Design e limpeza da sobrancelha', 'preco' => 15.00],
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Serviços</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #121212;
            color: #f1f1f1;
        }
        .container {
            max-width: 520px;
            margin: 0 auto;
            padding: 24px 16px 40px;
        }
        .logo { display: block; margin: 0 auto 12px; max-width: 120px; }
        h1 { text-align: center; margin: 0 0 4px; }
        .sub { text-align: center; color: #aaa; margin: 0 0 24px; }

        .servico { display: block; cursor: pointer; margin-bottom: 12px; }
        .servico input { display: none; }
        .card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1e1e1e;
            border: 2px solid #2c2c2c;
            border-radius: 10px;
            padding: 16px;
            transition: .2s;
        }
        .card:hover { border-color: #c9a227; }
        .servico input:checked + .card {
            border-color: #c9a227;
            background: #2a2412;
        }
        .card strong { font-size: 18px; }
        .card small { display: block; color: #aaa; margin-top: 4px; }
        .preco { font-size: 18px; font-weight: bold; color: #c9a227; white-space: nowrap; }

        .total {
            display: flex;
            justify-content: space-between;
            margin: 24px 0 16px;
            padding: 16px;
            background: #1e1e1e;
            border-radius: 10px;
            font-size: 20px;
        }
        .total span:last-child { color: #c9a227; font-weight: bold; }

        button {
            width: 100%;
            padding: 14px;
            border: 0;
            border-radius: 10px;
            background: #c9a227;
            color: #121212;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        button:disabled { background: #555; color: #999; cursor: not-allowed; }
    </style>
</head>
<body>
<div class="container">
    <img src="logo.png" alt="Logo" class="logo">
    <h1>Nossos Serviços</h1>
    <p class="sub">Escolha os serviços que você deseja</p>

    <form action="salvar_servico.php" method="POST" id="form-servicos">
        <?php foreach ($servicos as $id => $s): ?>
            <label class="servico">
                <input type="checkbox" name="servicos[]" value="<?= $id ?>"
                       data-preco="<?= $s['preco'] ?>">
                <div class="card">
                    <div>
                        <strong><?= htmlspecialchars($s['nome']) ?></strong>
                        <small><?= htmlspecialchars($s['descricao']) ?></small>
                    </div>
                    <span class="preco">R$ <?= number_format($s['preco'], 2, ',', '.') ?></span>
                </div>
            </label>
        <?php endforeach; ?>

        <div class="total">
            <span>Total</span>
            <span id="total">R$ 0,00</span>
        </div>

        <button type="submit" id="btn" disabled>Continuar</button>
    </form>
</div>

<script>
    const checks = document.querySelectorAll('input[name="servicos[]"]');
    const totalEl = document.getElementById('total');
    const btn = document.getElementById('btn');

    function atualizarTotal() {
        let total = 0;
        let marcados = 0;
        checks.forEach(c => {
            if (c.checked) {
                total += parseFloat(c.dataset.preco);
                marcados++;
            }
        });
        totalEl.textContent = total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        btn.disabled = marcados === 0;
    }

    checks.forEach(c => c.addEventListener('change', atualizarTotal));
    atualizarTotal();
</script>
</body>
</html>
