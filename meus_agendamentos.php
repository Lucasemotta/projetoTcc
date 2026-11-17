<?php
require "agenda_lib.php";
require "conexao.php";

$idCliente = exigir_cliente();
$erro = $ok = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nota'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $nota = (int) $_POST['nota'];
    $com = substr(trim($_POST['comentario'] ?? ''), 0, 300);
    if (!csrf_ok()) {
        $erro = "Sessão inválida. Tente novamente.";
    } elseif ($nota < 1 || $nota > 5) {
        $erro = "Escolha uma nota de 1 a 5.";
    } else {
        try {
            $av = $conn->prepare("INSERT INTO avaliacoes (id_agendamento, id_cliente, id_barbearia, nota, comentario)
                SELECT a.id_agendamento, a.id_cliente, b.id_barbearia, ?, ? FROM agendamentos a
                JOIN barbeiros b ON b.id_barbeiro = a.id_barbeiro
                WHERE a.id_agendamento = ? AND a.id_cliente = ? AND a.status = 'Concluído' AND b.id_barbearia IS NOT NULL");
            $av->bind_param("isii", $nota, $com, $id, $idCliente);
            $av->execute();
            $ok = $av->affected_rows === 1 ? "Obrigado pela sua avaliação!" : "";
            $erro = $ok ? "" : "Só é possível avaliar atendimentos concluídos.";
        } catch (mysqli_sql_exception $e) {
            $erro = "Você já avaliou este atendimento.";
        }
    }
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int) ($_POST['id'] ?? 0);
    $stmt = $conn->prepare("SELECT status, data_agendamento, horario FROM agendamentos WHERE id_agendamento = ? AND id_cliente = ?");
    $stmt->bind_param("ii", $id, $idCliente);
    $stmt->execute();
    $a = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!csrf_ok()) {
        $erro = "Sessão inválida. Tente novamente.";
    } elseif (!$a) {
        $erro = "Agendamento não encontrado.";
    } elseif (!in_array($a['status'], ['Agendado', 'Confirmado'], true)) {
        $erro = "Este agendamento não pode mais ser cancelado.";
    } elseif (strtotime($a['data_agendamento'] . ' ' . $a['horario']) - time() < AGENDA_CANCELA_ANTECEDENCIA_MIN * 60) {
        $erro = "O cancelamento só é possível com pelo menos 2 horas de antecedência. Fale com a barbearia.";
    } else {
        $up = $conn->prepare("UPDATE agendamentos SET status = 'Cancelado' WHERE id_agendamento = ? AND id_cliente = ?");
        $up->bind_param("ii", $id, $idCliente);
        $up->execute();
        $up->close();
        $ok = "Agendamento cancelado.";
    }
}

$sql = "SELECT a.*, s.nome AS servico, b.nome AS barbeiro, bz.nome AS barbearia, av.nota
        FROM agendamentos a
        INNER JOIN servicos s ON s.id_servico = a.id_servico
        INNER JOIN barbeiros b ON b.id_barbeiro = a.id_barbeiro
        LEFT JOIN barbearias bz ON bz.id_barbearia = b.id_barbearia
        LEFT JOIN avaliacoes av ON av.id_agendamento = a.id_agendamento
        WHERE a.id_cliente = ?
        ORDER BY a.data_agendamento DESC, a.horario DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idCliente);
$stmt->execute();
$lista = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="pt-br" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cortaí - Meus agendamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/cortai.css">
</head>
<body>
<div class="container pagina">
    <div class="row justify-content-center"><div class="col-lg-10">
        <div class="mb-3"><a href="agenda.php" class="btn btn-voltar btn-sm"><i class="bi bi-arrow-left me-1"></i> Novo agendamento</a></div>
        <div class="card-cortai">
            <h2 class="titulo mb-4">Meus <span>agendamentos</span></h2>
            <?php if ($erro): ?><div class="alert-cortai mb-3" role="alert"><?= htmlspecialchars($erro); ?></div><?php endif; ?>
            <?php if ($ok): ?><div class="alert alert-success py-2" role="status"><?= htmlspecialchars($ok); ?></div><?php endif; ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle table-stack mb-0">
                    <thead><tr><th>Data</th><th>Horário</th><th>Barbearia</th><th>Serviço</th><th>Barbeiro</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($lista as $a):
                        $cancelavel = in_array($a['status'], ['Agendado', 'Confirmado'], true)
                            && strtotime($a['data_agendamento'] . ' ' . $a['horario']) - time() >= AGENDA_CANCELA_ANTECEDENCIA_MIN * 60; ?>
                        <tr>
                            <td data-label="Data"><?= date('d/m/Y', strtotime($a['data_agendamento'])); ?></td>
                            <td data-label="Horário"><?= date('H:i', strtotime($a['horario'])); ?></td>
                            <td data-label="Barbearia"><?= htmlspecialchars($a['barbearia'] ?? '-'); ?></td>
                            <td data-label="Serviço"><?= htmlspecialchars($a['servico']); ?></td>
                            <td data-label="Barbeiro"><?= htmlspecialchars($a['barbeiro']); ?></td>
                            <td data-label="Status"><span class="badge bg-secondary"><?= htmlspecialchars($a['status']); ?></span></td>
                            <td data-label="">
                                <?php if ($cancelavel): ?>
                                    <form method="POST" onsubmit="return confirm('Cancelar este agendamento?')">
                                        <input type="hidden" name="csrf" value="<?= csrf_token(); ?>">
                                        <input type="hidden" name="id" value="<?= (int) $a['id_agendamento']; ?>">
                                        <button class="btn btn-outline-warning btn-sm">Cancelar</button>
                                    </form>
                                <?php endif; ?>
                                <?php if ($a['status'] === 'Concluído' && !$a['nota']): ?>
                                    <form method="POST" class="d-flex gap-1 flex-wrap">
                                        <input type="hidden" name="csrf" value="<?= csrf_token(); ?>">
                                        <input type="hidden" name="id" value="<?= (int) $a['id_agendamento']; ?>">
                                        <select name="nota" class="form-select form-select-sm" style="width:auto" aria-label="Nota"><?php for ($n = 5; $n >= 1; $n--): ?><option value="<?= $n; ?>"><?= str_repeat('★', $n); ?></option><?php endfor; ?></select>
                                        <input name="comentario" maxlength="300" class="form-control form-control-sm" style="width:170px" placeholder="Comentário (opcional)">
                                        <button class="btn btn-outline-success btn-sm">Avaliar</button>
                                    </form>
                                <?php elseif ($a['nota']): ?><span class="estrelas" title="Sua avaliação">★ <?= (int) $a['nota']; ?></span><?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$lista): ?><tr><td colspan="7" class="text-center py-4 subtitulo">Você ainda não tem agendamentos.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div></div>
</div>
</body>
</html>
