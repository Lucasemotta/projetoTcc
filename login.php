<!DOCTYPE html>
<html lang="pt-br">
 
<head>
 
<meta charset="UTF-8">
 
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 
<title>Cortaí - Login</title>
 
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
    margin-top: 80px;
}
 
.card-login {
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
 
.form-control {
    background-color: #111516;
    border: 1px solid #3b4547;
    color: white;
}
 
.form-control:focus {
    background-color: #111516;
    color: white;
    border-color: #719999;
    box-shadow: none;
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
 
<div class="col-md-5">
 
<div class="card-login">
 
<h2 class="titulo">
 
Área <span>Administrativa</span>
 
</h2>
 
<form action="autenticar.php" method="POST">
 
<div class="mb-3">
 
<label class="form-label">
Login
</label>
 
<input
type="text"
name="login"
class="form-control"
placeholder="Digite seu login"
required>
 
</div>
 
 
<div class="mb-4">
 
<label class="form-label">
Senha
</label>
 
<input
type="password"
name="senha"
class="form-control"
placeholder="Digite sua senha"
required>
 
</div>
 
 
<div class="d-grid">
 
<button
type="submit"
class="btn btn-cortai">
 
ENTRAR
 
</button>
 
</div>
 
</form>
 
</div>
 
</div>
 
</div>
 
</div>
 
</body>
 
</html>