<!DOCTYPE html>
<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Cortaí - Cadastro Barbeiro</title>


<link 
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
rel="stylesheet">


<style>

body{

    background-color:#111516;
    color:white;
    font-family:Arial, sans-serif;

}


.container{

    margin-top:60px;

}


.card-cadastro{

    background-color:#191e1f;
    border:1px solid #303a3b;
    border-radius:15px;
    padding:40px;

}


.titulo{

    text-align:center;
    margin-bottom:30px;

}


.titulo span{

    color:#719999;

}


.form-label{

    color:#ddd;

}


.form-control{

    background-color:#111516;
    border:1px solid #3b4547;
    color:white;

}


.form-control:focus{

    background-color:#111516;
    color:white;
    border-color:#719999;
    box-shadow:none;

}


.btn-cortai{

    background-color:#719999;
    color:#111516;
    font-weight:bold;
    border:none;

}


.btn-cortai:hover{

    background-color:#86aaaa;

}


</style>


</head>


<body>


<div class="container">


<div class="row justify-content-center">


<div class="col-md-6">


<div class="card-cadastro">


<h2 class="titulo">

Cadastro de <span>Barbeiro</span>

</h2>



<form action="salvar_barbeiro.php" method="POST">



<div class="mb-3">

<label class="form-label">

Nome completo

</label>


<input 
type="text"
name="nome"
class="form-control"
placeholder="Digite o nome do barbeiro"
required>

</div>




<div class="mb-3">

<label class="form-label">

Telefone

</label>


<input 
type="tel"
name="telefone"
class="form-control"
placeholder="(14) 99999-9999">

</div>




<div class="mb-3">

<label class="form-label">

Especialidade

</label>


<input 
type="text"
name="especialidade"
class="form-control"
placeholder="Ex: Corte masculino, barba, navalhado">

</div>




<div class="mb-3">

<label class="form-label">

Status

</label>


<select 
name="status"
class="form-control">


<option value="1">

Ativo

</option>


<option value="0">

Inativo

</option>


</select>


</div>




<div class="d-grid">

<button 
type="submit"
class="btn btn-cortai">

CADASTRAR BARBEIRO

</button>

</div>



</form>


</div>


</div>


</div>


</div>


</body>

</html>