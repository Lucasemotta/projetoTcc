<?php
session_start();
include "conexao.php";
require "admin_guard.php";

$erro = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST['nome'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $descricao = substr(trim($_POST['descricao'] ?? ''), 0, 500);
    $foto = $minha['foto'] ?? null;
    $erroFoto = '';
    if (!empty($_FILES['foto']['tmp_name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['foto']['tmp_name']);
        $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
        if (!$ext || $_FILES['foto']['size'] > 2 * 1024 * 1024) {
            $erroFoto = 'A foto deve ser JPG, PNG ou WEBP de até 2 MB.';
        } else {
            if (!is_dir('uploads')) { mkdir('uploads', 0755, true); }
            $foto = 'uploads/b' . $_SESSION['usuario_id'] . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
        }
    }
    $lat = filter_var($_POST['latitude'] ?? '', FILTER_VALIDATE_FLOAT);
    $lng = filter_var($_POST['longitude'] ?? '', FILTER_VALIDATE_FLOAT);

    if ($nome === '' || $endereco === '') {
        $erro = "Informe o nome e o endereço da barbearia.";
    } elseif ($lat === false || $lng === false || abs($lat) > 90 || abs($lng) > 180) {
        $erro = "Marque a localização da barbearia no mapa.";
    } elseif ($erroFoto) {
        $erro = $erroFoto;
    } else {
        $sql = "INSERT INTO barbearias (id_usuario, nome, endereco, cidade, telefone, latitude, longitude, descricao, foto)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE nome = VALUES(nome), endereco = VALUES(endereco), cidade = VALUES(cidade),
                    telefone = VALUES(telefone), latitude = VALUES(latitude), longitude = VALUES(longitude), descricao = VALUES(descricao), foto = VALUES(foto)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssddss", $_SESSION['usuario_id'], $nome, $endereco, $cidade, $telefone, $lat, $lng, $descricao, $foto);
        $stmt->execute();
        $stmt->close();
        header("Location: dashboard.php");
        exit;
    }
    $minha = ['nome' => $nome, 'endereco' => $endereco, 'cidade' => $cidade, 'telefone' => $telefone, 'latitude' => $_POST['latitude'] ?? '', 'longitude' => $_POST['longitude'] ?? ''];
}
$v = fn($k) => htmlspecialchars((string) ($minha[$k] ?? ''));
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Minha Barbearia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="assets/cortai.css">
</head>
<body>
<nav class="navbar navbar-admin">
    <div class="container d-flex align-items-center justify-content-between gap-2">
        <a class="navbar-brand m-0" href="dashboard.php">CORTAÍ ADMIN</a>
        <div class="d-flex gap-2">
            <?php if ($idB): ?><a href="dashboard.php" class="btn btn-admin btn-sm">Painel</a><?php endif; ?>
            <a href="logout.php" class="btn btn-danger btn-sm">Sair</a>
        </div>
    </div>
</nav>
<div class="container pagina">
    <div class="row justify-content-center"><div class="col-lg-8">
        <div class="card-cortai">
            <h2 class="titulo mb-1">Minha <span>barbearia</span></h2>
            <p class="subtitulo mb-4">Os clientes encontram sua barbearia pelo nome, endereço e pelo ponto no mapa.</p>
            <?php if ($erro): ?><div class="alert-cortai mb-3" role="alert"><?= htmlspecialchars($erro); ?></div><?php endif; ?>

            <form method="POST" id="form-barbearia" enctype="multipart/form-data">
                <div class="mb-3"><label class="form-label" for="nome">Nome da barbearia</label>
                    <input class="form-control" id="nome" name="nome" maxlength="100" value="<?= $v('nome'); ?>" required></div>
                <div class="row g-3 mb-3">
                    <div class="col-md-8"><label class="form-label" for="endereco">Endereço</label>
                        <input class="form-control" id="endereco" name="endereco" maxlength="255" placeholder="Rua, número, bairro" value="<?= $v('endereco'); ?>" required></div>
                    <div class="col-md-4"><label class="form-label" for="cidade">Cidade</label>
                        <input class="form-control" id="cidade" name="cidade" maxlength="80" value="<?= $v('cidade'); ?>"></div>
                </div>
                <div class="mb-3"><label class="form-label" for="telefone">Telefone / WhatsApp</label>
                    <input class="form-control" id="telefone" name="telefone" maxlength="20" inputmode="tel" value="<?= $v('telefone'); ?>"></div>

                <div class="mb-3"><label class="form-label" for="descricao">Descrição (aparece para os clientes)</label>
                    <textarea class="form-control" id="descricao" name="descricao" maxlength="500" rows="3"><?= $v('descricao'); ?></textarea></div>
                <div class="mb-3"><label class="form-label" for="foto">Foto da barbearia (JPG, PNG ou WEBP, até 2 MB)</label>
                    <?php if (!empty($minha['foto'])): ?><img src="<?= $v('foto'); ?>" alt="Foto atual" class="foto-barbearia"><?php endif; ?>
                    <input class="form-control" type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp"></div>

                <span class="form-label d-block">Localização no mapa</span>
                <div class="d-flex flex-wrap gap-2 mb-2">
                    <button type="button" class="btn btn-cortai-outline btn-sm" id="btn-buscar"><i class="bi bi-search me-1"></i>Buscar pelo endereço</button>
                    <button type="button" class="btn btn-cortai-outline btn-sm" id="btn-gps"><i class="bi bi-geo-alt me-1"></i>Usar minha localização</button>
                </div>
                <div id="mapa" class="mapa"></div>
                <p class="subtitulo" id="mapa-aviso">Clique no mapa ou arraste o pino até a porta da barbearia.</p>
                <input type="hidden" name="latitude" id="latitude" value="<?= $v('latitude'); ?>">
                <input type="hidden" name="longitude" id="longitude" value="<?= $v('longitude'); ?>">

                <button class="btn btn-cortai w-100 mt-2">SALVAR BARBEARIA</button>
            </form>
        </div>
    </div></div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const $lat = document.getElementById('latitude'), $lng = document.getElementById('longitude'), $av = document.getElementById('mapa-aviso');
    const tem = $lat.value !== '' && $lng.value !== '' && Math.abs(+$lat.value) <= 90;
    const mapa = L.map('mapa').setView(tem ? [+$lat.value, +$lng.value] : [-14.2, -51.9], tem ? 16 : 4);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(mapa);
    let pino = null;

    function marcar(lat, lng, zoom) {
        $lat.value = (+lat).toFixed(7); $lng.value = (+lng).toFixed(7);
        if (!pino) {
            pino = L.marker([lat, lng], { draggable: true }).addTo(mapa);
            pino.on('dragend', () => { const p = pino.getLatLng(); marcar(p.lat, p.lng); });
        } else pino.setLatLng([lat, lng]);
        if (zoom) mapa.setView([lat, lng], zoom);
        $av.textContent = 'Local marcado. Arraste o pino se precisar ajustar.';
    }
    if (tem) marcar(+$lat.value, +$lng.value);
    mapa.on('click', ev => marcar(ev.latlng.lat, ev.latlng.lng));

    document.getElementById('btn-buscar').onclick = async () => {
        const q = [endereco.value, cidade.value].filter(Boolean).join(', ');
        if (!q) { $av.textContent = 'Digite o endereço primeiro.'; return; }
        $av.textContent = 'Buscando...';
        try {
            const r = await (await fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(q))).json();
            if (r.length) marcar(+r[0].lat, +r[0].lon, 17); else $av.textContent = 'Endereço não encontrado. Clique no mapa para marcar.';
        } catch (e) { $av.textContent = 'Não foi possível buscar agora. Clique no mapa para marcar.'; }
    };
    document.getElementById('btn-gps').onclick = () => {
        if (!navigator.geolocation) { $av.textContent = 'Seu navegador não permite localização.'; return; }
        navigator.geolocation.getCurrentPosition(p => marcar(p.coords.latitude, p.coords.longitude, 17),
            () => { $av.textContent = 'Localização bloqueada (só funciona em HTTPS ou localhost). Clique no mapa.'; });
    };
    document.getElementById('form-barbearia').onsubmit = ev => {
        if ($lat.value === '') { ev.preventDefault(); $av.textContent = 'Marque a localização da barbearia no mapa.'; }
    };
})();
</script>
</body>
</html>
