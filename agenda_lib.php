<?php
/*
 * Regras da agenda do Cortaí.
 * Usado por agenda.php, api_barbeiros.php, api_horarios.php e salvar_agendamento.php,
 * para que a tela e o servidor sempre concordem sobre o que está livre.
 */

date_default_timezone_set('America/Sao_Paulo');

// Grade de horários de início oferecidos (um por hora) e horário de fechamento.
// Para mudar o expediente, altere só estas duas linhas.
const AGENDA_HORARIOS    = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'];
const AGENDA_FECHAMENTO  = '19:00';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/** Converte "HH:MM" (ou "HH:MM:SS") em minutos desde 00:00. */
function agenda_minutos(string $hora): int
{
    return ((int) substr($hora, 0, 2)) * 60 + (int) substr($hora, 3, 2);
}

/**
 * Garante que há um cliente identificado na sessão e devolve o id dele.
 * Em páginas redireciona para o início; em APIs (JSON) responde 401.
 */
function exigir_cliente(bool $json = false): int
{
    if (empty($_SESSION['id_cliente'])) {
        if ($json) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['erro' => 'Sua sessão expirou. Informe seus dados novamente.']);
        } else {
            agenda_flash('Entre ou crie sua conta para agendar.');
            header('Location: index.php');
        }
        exit;
    }
    return (int) $_SESSION['id_cliente'];
}

/** Guarda uma mensagem de erro para a próxima tela e a devolve (uma vez só) quando pedida. */
function agenda_flash(?string $mensagem = null): ?string
{
    if ($mensagem !== null) {
        $_SESSION['flash_erro'] = $mensagem;
        return null;
    }
    $atual = $_SESSION['flash_erro'] ?? null;
    unset($_SESSION['flash_erro']);
    return $atual;
}

/** Aceita só datas reais no formato AAAA-MM-DD, de hoje em diante. */
function agenda_data_valida(string $data): bool
{
    $d = DateTime::createFromFormat('Y-m-d', $data);
    return $d !== false && $d->format('Y-m-d') === $data && $data >= date('Y-m-d');
}

/** Devolve o serviço (id, nome, valor, duracao) ou null se não existir. */
function agenda_servico(mysqli $conn, int $idServico): ?array
{
    $stmt = $conn->prepare("SELECT id_servico, nome, valor, duracao FROM servicos WHERE id_servico = ? LIMIT 1");
    $stmt->bind_param("i", $idServico);
    $stmt->execute();
    $linha = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $linha ?: null;
}

/** Barbeiros ativos que prestam o serviço informado. */
function barbeiros_do_servico(mysqli $conn, int $idServico): array
{
    $sql = "SELECT b.id_barbeiro, b.nome, b.especialidade
            FROM barbeiros b
            INNER JOIN barbeiro_servico bs ON bs.id_barbeiro = b.id_barbeiro
            WHERE bs.id_servico = ? AND b.status = 1
            ORDER BY b.nome ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idServico);
    $stmt->execute();
    $lista = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $lista;
}

/** O barbeiro está ativo e presta esse serviço? */
function barbeiro_presta_servico(mysqli $conn, int $idBarbeiro, int $idServico): bool
{
    $sql = "SELECT 1 FROM barbeiros b
            INNER JOIN barbeiro_servico bs ON bs.id_barbeiro = b.id_barbeiro
            WHERE b.id_barbeiro = ? AND bs.id_servico = ? AND b.status = 1 LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $idBarbeiro, $idServico);
    $stmt->execute();
    $ok = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $ok;
}

/**
 * Horários ("HH:MM") em que o barbeiro pode começar um atendimento de $duracaoMin
 * no dia $data. Fica de fora o horário que:
 *  - já passou (quando a data é hoje);
 *  - terminaria depois do fechamento;
 *  - se sobrepõe a um atendimento já marcado (considerando a duração de cada um).
 * Atendimentos cancelados não ocupam agenda.
 */
function horarios_livres(mysqli $conn, int $idBarbeiro, string $data, int $duracaoMin, ?int $agoraMin = null): array
{
    $sql = "SELECT TIME_FORMAT(a.horario, '%H:%i') AS inicio, s.duracao
            FROM agendamentos a
            INNER JOIN servicos s ON s.id_servico = a.id_servico
            WHERE a.id_barbeiro = ? AND a.data_agendamento = ? AND a.status <> 'Cancelado'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $idBarbeiro, $data);
    $stmt->execute();
    $ocupados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $fechamento = agenda_minutos(AGENDA_FECHAMENTO);
    // $agoraMin (minutos desde 00:00) existe só para testes; normalmente usa o relógio do servidor.
    $agora      = ($data === date('Y-m-d')) ? ($agoraMin ?? agenda_minutos(date('H:i'))) : -1;
    $livres     = [];

    foreach (AGENDA_HORARIOS as $h) {
        $ini = agenda_minutos($h);
        $fim = $ini + $duracaoMin;

        if ($ini <= $agora || $fim > $fechamento) {
            continue;
        }
        $conflito = false;
        foreach ($ocupados as $o) {
            $oIni = agenda_minutos($o['inicio']);
            $oFim = $oIni + (int) $o['duracao'];
            if ($ini < $oFim && $fim > $oIni) {
                $conflito = true;
                break;
            }
        }
        if (!$conflito) {
            $livres[] = $h;
        }
    }
    return $livres;
}

/* ---------- Telefone (login e cadastro de cliente) ---------- */

/** Só os dígitos: "(14) 99999-0000" e "14999990000" viram a mesma coisa. */
function agenda_digitos(string $telefone): string
{
    return preg_replace('/\D/', '', $telefone);
}

/** DDD + 8 ou 9 dígitos. */
function agenda_telefone_valido(string $digitos): bool
{
    return strlen($digitos) === 10 || strlen($digitos) === 11;
}

/** "14999990000" -> "(14) 99999-0000" */
function agenda_formatar_telefone(string $digitos): string
{
    $ddd  = substr($digitos, 0, 2);
    $resto = substr($digitos, 2);
    $corte = strlen($resto) === 9 ? 5 : 4;
    return "($ddd) " . substr($resto, 0, $corte) . '-' . substr($resto, $corte);
}

/** Acha o cliente pelo telefone, ignorando a máscara com que foi gravado. */
function cliente_por_telefone(mysqli $conn, string $digitos): ?array
{
    $sql = "SELECT id_cliente, nome FROM clientes
            WHERE REPLACE(REPLACE(REPLACE(REPLACE(telefone, '(', ''), ')', ''), '-', ''), ' ', '') = ?
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $digitos);
    $stmt->execute();
    $linha = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $linha ?: null;
}
