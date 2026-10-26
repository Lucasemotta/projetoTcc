<?php
// GET api_barbeiros.php?id_servico=N  ->  {"barbeiros":[{id_barbeiro,nome,especialidade}, ...]}
require "agenda_lib.php";
require "conexao.php";
ini_set('display_errors', '0');          // nunca misturar erros do PHP com o JSON
header('Content-Type: application/json; charset=utf-8');

exigir_cliente(true);

$idServico = filter_input(INPUT_GET, 'id_servico', FILTER_VALIDATE_INT);
if (!$idServico || !agenda_servico($conn, $idServico)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Serviço inválido.']);
    exit;
}

echo json_encode(['barbeiros' => barbeiros_do_servico($conn, $idServico)], JSON_UNESCAPED_UNICODE);
