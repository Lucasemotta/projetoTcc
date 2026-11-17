<?php
require "agenda_lib.php";
require "conexao.php";

exigir_cliente();

$nomeCliente = $_SESSION['nome_cliente'] ?? 'Cliente';
$erro        = agenda_flash();

$barbearias = barbearias_todas($conn);
$idBarb    = (int) filter_input(INPUT_GET, 'b', FILTER_VALIDATE_INT);
$bz        = $idBarb ? barbearia_por_id($conn, $idBarb) : null;

$servicos = !$bz ? null : $conn->query("SELECT id_servico, nome, valor, duracao FROM servicos WHERE id_barbearia = {$idBarb} ORDER BY nome ASC");
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
                <a href="entrar.php?trocar=1" class="btn btn-voltar btn-sm">
                    <i class="bi bi-person-x me-1"></i> Não sou <?= htmlspecialchars(explode(' ', $nomeCliente)[0]); ?>
                </a>
                <a href="meus_agendamentos.php" class="btn btn-voltar btn-sm ms-1"><i class="bi bi-calendar-check me-1"></i> Meus agendamentos</a>
            </div>

            <div class="card-cortai">
                <h2 class="titulo text-center mb-1">Agende seu <span>horário</span></h2>
                <p class="subtitulo text-center mb-4">
                    Olá, <strong><?= htmlspecialchars($nomeCliente); ?></strong>! Escolha os detalhes do seu atendimento.
                </p>

                <?php if ($erro): ?>
                    <div class="alert-cortai mb-4" role="alert"><?= htmlspecialchars($erro); ?></div>
                <?php endif; ?>

                <?php if (!$bz): ?>
                    <p class="subtitulo text-center">Primeiro, escolha a barbearia.</p>
                    <?php if (!$barbearias): ?><p class="slots-aviso text-center">Nenhuma barbearia cadastrada ainda.</p><?php endif; ?>
                    <div class="d-flex gap-2 mb-3"><input id="busca" class="form-control" placeholder="Buscar por nome, cidade ou bairro" aria-label="Buscar barbearia"><button type="button" id="perto" class="btn btn-cortai-outline text-nowrap"><i class="bi bi-geo-alt me-1"></i>Perto de mim</button></div>
                    <div id="mapa" class="mapa"<?= $barbearias ? '' : ' hidden'; ?>></div>
                    <div id="lista"><?php foreach ($barbearias as $b): ?>
                        <div class="barbearia-item" data-busca="<?= htmlspecialchars($b['nome'] . ' ' . $b['cidade'] . ' ' . $b['endereco']); ?>" data-lat="<?= (float) $b['latitude']; ?>" data-lng="<?= (float) $b['longitude']; ?>">
                            <?php if (!empty($b['foto'])): ?><img src="<?= htmlspecialchars($b['foto']); ?>" alt="" class="foto-barbearia"><?php endif; ?>
                            <strong><?= htmlspecialchars($b['nome']); ?></strong> <?= estrelas($b); ?> <span class="subtitulo dist"></span>
                            <?php if (!empty($b['descricao'])): ?><div class="subtitulo"><?= htmlspecialchars($b['descricao']); ?></div><?php endif; ?>
                            <div class="subtitulo"><?= htmlspecialchars($b['endereco'] . ($b['cidade'] ? ' - ' . $b['cidade'] : '')); ?></div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <a class="btn btn-cortai btn-sm" href="agenda.php?b=<?= (int) $b['id_barbearia']; ?>">Agendar aqui</a>
                                <a class="btn btn-cortai-outline btn-sm" target="_blank" rel="noopener" href="<?= htmlspecialchars(barbearia_rota($b)); ?>"><i class="bi bi-signpost-split me-1"></i>Como chegar</a>
                            </div>
                        </div>
                    <?php endforeach; ?></div>
                <?php else: ?>
                    <div class="barbearia-item">
                        <strong><i class="bi bi-shop me-1"></i><?= htmlspecialchars($bz['nome']); ?></strong> <?= estrelas($bz); ?>
                        <div class="subtitulo"><?= htmlspecialchars($bz['endereco'] . ($bz['cidade'] ? ' - ' . $bz['cidade'] : '')); ?></div>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            <a class="btn btn-cortai-outline btn-sm" target="_blank" rel="noopener" href="<?= htmlspecialchars(barbearia_rota($bz)); ?>"><i class="bi bi-signpost-split me-1"></i>Como chegar</a>
                            <a class="btn btn-cortai-outline btn-sm" href="agenda.php"><i class="bi bi-arrow-left-right me-1"></i>Trocar barbearia</a>
                        </div>
                    </div>
                <form action="salvar_agendamento.php" method="POST" id="form-agenda">
                    <input type="hidden" name="b" value="<?= $idBarb; ?>">

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
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if ($bz): ?>
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
        if (r.status === 401) { window.location.href = 'entrar.php'; return null; }
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
<?php endif; ?>
<?php if (!$bz && $barbearias): ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const lista = <?= json_encode(array_map(fn($b) => ['id' => (int) $b['id_barbearia'], 'n' => $b['nome'], 'e' => $b['endereco'], 'la' => (float) $b['latitude'], 'lo' => (float) $b['longitude']], $barbearias), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP); ?>;
const mapa = L.map('mapa');
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(mapa);
const pts = lista.map(b => {
    const m = L.marker([b.la, b.lo]).addTo(mapa), pop = document.createElement('div');
    const t = document.createElement('strong'); t.textContent = b.n;
    const a = document.createElement('a'); a.href = 'agenda.php?b=' + b.id; a.textContent = 'Agendar aqui'; a.style.display = 'block';
    pop.append(t, a); m.bindPopup(pop); return [b.la, b.lo];
});
pts.length === 1 ? mapa.setView(pts[0], 15) : mapa.fitBounds(pts, { padding: [30, 30] });
const norm = s => s.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
const itens = [...document.querySelectorAll('#lista .barbearia-item')];
document.getElementById('busca').oninput = e => { const q = norm(e.target.value); itens.forEach(i => i.hidden = !norm(i.dataset.busca).includes(q)); };
document.getElementById('perto').onclick = () => {
    if (!navigator.geolocation) return alert('Seu navegador não permite localização.');
    navigator.geolocation.getCurrentPosition(p => {
        const rad = x => x * Math.PI / 180, la = p.coords.latitude, lo = p.coords.longitude;
        itens.forEach(i => {
            const dLa = rad(i.dataset.lat - la), dLo = rad(i.dataset.lng - lo);
            const a = Math.sin(dLa / 2) ** 2 + Math.cos(rad(la)) * Math.cos(rad(i.dataset.lat)) * Math.sin(dLo / 2) ** 2;
            i.dataset.km = 12742 * Math.asin(Math.sqrt(a));
            i.querySelector('.dist').textContent = '· a ' + i.dataset.km.toFixed(1).replace('.', ',') + ' km';
        });
        itens.sort((x, y) => x.dataset.km - y.dataset.km).forEach(i => i.parentNode.appendChild(i));
        mapa.setView([la, lo], 12);
    }, () => alert('Localização bloqueada (só funciona em HTTPS ou localhost). Use a busca por nome.'));
};
</script>
<?php endif; ?>
</body>
</html>
