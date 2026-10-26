<?php
require "agenda_lib.php";
require "conexao.php";

exigir_cliente();

$nomeCliente = $_SESSION['nome_cliente'] ?? 'Cliente';
$erro        = agenda_flash();

$servicos = $conn->query("SELECT id_servico, nome, valor, duracao FROM servicos ORDER BY nome ASC");
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
</head>
<body>

<div class="container pagina">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">

            <div class="mb-3">
                <a href="index.php?trocar=1" class="btn btn-voltar btn-sm">
                    <i class="bi bi-person-x me-1"></i> Não sou <?= htmlspecialchars(explode(' ', $nomeCliente)[0]); ?>
                </a>
            </div>

            <div class="card-cortai">
                <h2 class="titulo text-center mb-1">Agende seu <span>horário</span></h2>
                <p class="subtitulo text-center mb-4">
                    Olá, <strong><?= htmlspecialchars($nomeCliente); ?></strong>! Escolha os detalhes do seu atendimento.
                </p>

                <?php if ($erro): ?>
                    <div class="alert-cortai mb-4" role="alert"><?= htmlspecialchars($erro); ?></div>
                <?php endif; ?>

                <form action="salvar_agendamento.php" method="POST" id="form-agenda">

                    <div class="mb-3">
                        <label class="form-label" for="id_servico"><i class="bi bi-scissors me-1"></i>Serviço</label>
                        <select name="id_servico" id="id_servico" class="form-select" required>
                            <option value="">Selecione um serviço</option>
                            <?php if ($servicos): while ($s = $servicos->fetch_assoc()): ?>
                                <option value="<?= (int) $s['id_servico']; ?>">
                                    <?= htmlspecialchars($s['nome']); ?> — R$ <?= number_format($s['valor'], 2, ',', '.'); ?> (<?= (int) $s['duracao']; ?> min)
                                </option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="id_barbeiro"><i class="bi bi-person-badge me-1"></i>Barbeiro</label>
                        <select name="id_barbeiro" id="id_barbeiro" class="form-select" required disabled>
                            <option value="">Escolha o serviço primeiro</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="data_agendamento"><i class="bi bi-calendar-event me-1"></i>Data</label>
                        <input type="date" name="data_agendamento" id="data_agendamento" class="form-control"
                               min="<?= date('Y-m-d'); ?>" required>
                    </div>

                    <div class="mb-4">
                        <span class="form-label d-block" id="rotulo-horario"><i class="bi bi-clock me-1"></i>Horário</span>
                        <div id="horarios" class="slots" role="radiogroup" aria-labelledby="rotulo-horario"></div>
                        <p id="horarios-aviso" class="slots-aviso" aria-live="polite">Escolha o serviço, o barbeiro e a data para ver os horários livres.</p>
                    </div>

                    <button type="submit" class="btn btn-cortai w-100" id="btn-confirmar" disabled>
                        <i class="bi bi-check-lg me-1"></i> CONFIRMAR AGENDAMENTO
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    const $servico  = document.getElementById('id_servico');
    const $barbeiro = document.getElementById('id_barbeiro');
    const $data     = document.getElementById('data_agendamento');
    const $slots    = document.getElementById('horarios');
    const $aviso    = document.getElementById('horarios-aviso');
    const $confirma = document.getElementById('btn-confirmar');

    // Cada chamada ganha um número; respostas atrasadas de escolhas antigas são ignoradas.
    let reqBarbeiros = 0, reqHorarios = 0;

    async function buscar(url) {
        const r = await fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
        if (r.status === 401) { window.location.href = 'index.php'; return null; }
        const corpo = await r.json().catch(() => ({}));
        if (!r.ok) throw new Error(corpo.erro || 'Não foi possível carregar. Tente novamente.');
        return corpo;
    }

    function limparHorarios(mensagem) {
        $slots.replaceChildren();
        $aviso.textContent = mensagem;
        $aviso.hidden = false;
        $confirma.disabled = true;
    }

    function resetarBarbeiros(texto) {
        $barbeiro.replaceChildren(new Option(texto, ''));
        $barbeiro.disabled = true;
    }

    async function carregarBarbeiros() {
        const idServico = $servico.value;
        const anterior  = $barbeiro.value;
        const minha     = ++reqBarbeiros;

        if (!idServico) {
            resetarBarbeiros('Escolha o serviço primeiro');
            limparHorarios('Escolha o serviço, o barbeiro e a data para ver os horários livres.');
            return;
        }

        resetarBarbeiros('Carregando barbeiros...');
        limparHorarios('Carregando...');

        try {
            const dados = await buscar('api_barbeiros.php?id_servico=' + encodeURIComponent(idServico));
            if (!dados || minha !== reqBarbeiros) return;

            if (dados.barbeiros.length === 0) {
                resetarBarbeiros('Nenhum barbeiro disponível para este serviço');
                limparHorarios('No momento nenhum barbeiro atende este serviço. Escolha outro serviço.');
                return;
            }

            $barbeiro.replaceChildren(new Option('Selecione um barbeiro', ''));
            dados.barbeiros.forEach(b => {
                const texto = b.especialidade ? b.nome + ' (' + b.especialidade + ')' : b.nome;
                $barbeiro.add(new Option(texto, b.id_barbeiro));
            });
            $barbeiro.disabled = false;

            // Mantém o barbeiro já escolhido se ele também presta o novo serviço.
            if ([...$barbeiro.options].some(o => o.value === anterior)) $barbeiro.value = anterior;

            carregarHorarios();
        } catch (e) {
            resetarBarbeiros('Erro ao carregar');
            limparHorarios(e.message);
        }
    }

    async function carregarHorarios() {
        const idServico = $servico.value, idBarbeiro = $barbeiro.value, data = $data.value;
        const minha = ++reqHorarios;

        if (!idServico || !idBarbeiro || !data) {
            limparHorarios('Escolha o serviço, o barbeiro e a data para ver os horários livres.');
            return;
        }
        limparHorarios('Buscando horários livres...');

        try {
            const qs = new URLSearchParams({ id_servico: idServico, id_barbeiro: idBarbeiro, data: data });
            const dados = await buscar('api_horarios.php?' + qs);
            if (!dados || minha !== reqHorarios) return;

            $slots.replaceChildren();
            if (dados.horarios.length === 0) {
                limparHorarios('Sem horários livres neste dia. Tente outra data ou outro barbeiro.');
                return;
            }
            $aviso.hidden = true;

            dados.horarios.forEach((h, i) => {
                const id = 'h' + i;
                const wrap = document.createDocumentFragment();

                const input = document.createElement('input');
                input.type = 'radio'; input.name = 'horario'; input.value = h;
                input.id = id; input.className = 'slot-input'; input.required = true;
                input.addEventListener('change', () => { $confirma.disabled = false; });

                const label = document.createElement('label');
                label.htmlFor = id; label.className = 'slot'; label.textContent = h;

                wrap.append(input, label);
                $slots.append(wrap);
            });
        } catch (e) {
            limparHorarios(e.message);
        }
    }

    $servico.addEventListener('change', carregarBarbeiros);
    $barbeiro.addEventListener('change', carregarHorarios);
    $data.addEventListener('change', carregarHorarios);

    // Navegador restaurou valores ao voltar para a página: recarrega as listas.
    if ($servico.value) carregarBarbeiros();
})();
</script>
</body>
</html>
