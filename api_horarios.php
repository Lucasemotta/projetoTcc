<?php
// GET api_horarios.php?id_servico=N&id_barbeiro=N&data=AAAA-MM-DD  ->  {"horarios":["09:00","10:00", ...]}
require "agenda_lib.php";
require "conexao.php";
ini_set('display_errors', '0');
header('Content-Type: application/json; charset=utf-8');

exigir_cliente(true);

$idServico  = filter_input(INPUT_GET, 'id_servico', FILTER_VALIDATE_INT);
$idBarbeiro = filter_input(INPUT_GET, 'id_barbeiro', FILTER_VALIDATE_INT);
$data       = (string) ($_GET['data'] ?? '');

$servico = $idServico ? agenda_servico($conn, $idServico) : null;

if (!$servico || !$idBarbeiro || !agenda_data_valida($data)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Escolha um serviço, um barbeiro e uma data a partir de hoje.']);
    exit;
}
if (!barbeiro_presta_servico($conn, $idBarbeiro, $idServico)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Este barbeiro não realiza o serviço escolhido.']);
    exit;
}

echo json_encode(
    ['horarios' => horarios_livres($conn, $idBarbeiro, $data, (int) $servico['duracao'])],
    JSON_UNESCAPED_UNICODE
);
