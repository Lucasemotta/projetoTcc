<?php
// A entrada do sistema agora é uma só: entrar.php (abas Cliente / Barbeiro).
// Este arquivo fica só para links antigos continuarem funcionando.
$qs = $_SERVER['QUERY_STRING'] ?? '';
if ($qs === '') { $qs = 'tipo=barbeiro'; }
header("Location: entrar.php?" . $qs);
exit;
