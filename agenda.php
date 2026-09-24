<?php

session_start();

include "conexao.php";


if (!isset($_SESSION['id_cliente'])) {

    header("Location: cadastro_cliente.php");
    exit;

}


$sqlServicos = "SELECT * FROM servicos ORDER BY nome";

$resultadoServicos = $conn->query($sqlServicos);


$sqlBarbeiros = "SELECT * FROM barbeiros WHERE status = 1 ORDER BY nome";

$resultadoBarbeiros = $conn->query($sqlBarbeiros);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Cortaí - Agendamento</title>

<link 
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
rel="stylesheet">

<style>

body {
    background-color: #111516;
    color: white;
    font-family: Arial, sans-serif;
}

.container {
    margin-top: 50px;
    margin-bottom: 50px;
}

.card-agenda {
    background-color: #191e1f;
    border: 1px solid #303a3b;
    border-radius: 15px;
    padding: 40px;
}

.titulo {
    text-align: center;
    margin-bottom: 30px;
}

.titulo span {
    color: #719999;
}

.form-label {
    color: #ddd;
}

.form-control,
.form-select {
    background-color: #111516;
    border: 1px solid #3b4547;
    color: white;
}

.form-control:focus,
.form-select:focus {
    background-color: #111516;
    color: white;
    border-color: #719999;
    box-shadow: none;
}

.form-select option {
    background-color: #111516;
    color: white;
}

.btn-cortai {
    background-color: #719999;
    color: #111516;
    font-weight: bold;
    border: none;
}

.btn-cortai:hover {
    background-color: #86aaaa;
}

</style>

</head>

<body>

<div class="container">

<div class="row justify-content-center">

<div class="col-md-7">

<div class="card-agenda">

<h2 class="titulo">
Agende seu <span>horário</span>
</h2>


<form action="salvar_agendamento.php" method="POST">


<!-- SERVIÇO -->

<div class="mb-3">

<label class="form-label">
Escolha o serviço
</label>

<select name="id_servico" class="form-select" required>

<option value="">
Selecione um serviço
</option>

<?php

while ($servico = $resultadoServicos->fetch_assoc()) {

?>

<option value="<?= $servico['id_servico']; ?>">

<?= htmlspecialchars($servico['nome']); ?>

- R$ <?= number_format($servico['valor'], 2, ',', '.'); ?>

</option>

<?php

}

?>

</select>

</div>


<!-- BARBEIRO -->

<div class="mb-3">

<label class="form-label">
Escolha o barbeiro
</label>

<select name="id_barbeiro" class="form-select" required>

<option value="">
Selecione um barbeiro
</option>

<?php

while ($barbeiro = $resultadoBarbeiros->fetch_assoc()) {

?>

<option value="<?= $barbeiro['id_barbeiro']; ?>">

<?= htmlspecialchars($barbeiro['nome']); ?>

<?php if (!empty($barbeiro['especialidade'])) { ?>

- <?= htmlspecialchars($barbeiro['especialidade']); ?>

<?php } ?>

</option>

<?php

}

?>

</select>

</div>


<!-- DATA -->

<div class="mb-3">

<label class="form-label">
Escolha a data
</label>

<input
type="date"
name="data_agendamento"
class="form-control"
required>

</div>


<!-- HORÁRIO -->

<div class="mb-3">

<label class="form-label">
Escolha o horário
</label>

<select name="horario" class="form-select" required>

<option value="">
Selecione um horário
</option>

<option value="09:00">09:00</option>
<option value="10:00">10:00</option>
<option value="11:00">11:00</option>
<option value="12:00">12:00</option>
<option value="13:00">13:00</option>
<option value="14:00">14:00</option>
<option value="15:00">15:00</option>
<option value="16:00">16:00</option>
<option value="17:00">17:00</option>
<option value="18:00">18:00</option>

</select>

</div>


<div class="d-grid">

<button
type="submit"
class="btn btn-cortai">

CONFIRMAR AGENDAMENTO

</button>

</div>


</form>

</div>

</div>

</div>

</div>

</body>

</html>