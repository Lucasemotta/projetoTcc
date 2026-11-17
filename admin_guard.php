<?php
// Incluir DEPOIS de conexao.php nas telas do painel.
// Exige login de barbeiro e uma barbearia cadastrada; define $minha e $idB (id da barbearia do usuário),
// que as telas usam para mostrar e alterar SOMENTE os dados da própria barbearia.
require_once "agenda_lib.php";

if (empty($_SESSION['usuario_id'])) {
    header("Location: entrar.php?tipo=barbeiro");
    exit;
}
$minha = barbearia_do_admin($conn);
if (!$minha && basename($_SERVER['SCRIPT_NAME']) !== 'barbearia.php') {
    header("Location: barbearia.php");
    exit;
}
$idB = (int) ($minha['id_barbearia'] ?? 0);
