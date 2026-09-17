<?php

include "conexao.php";

$sql = "SELECT * FROM clientes ORDER BY id_cliente DESC";

$resultado = mysqli_query($conexao, $sql);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cortaí - Clientes</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


    <style>
        body {

            background-color: #111516;
            color: #f5f5f5;
            font-family: Arial, sans-serif;

        }


        .container {

            margin-top: 50px;

        }


        .card-clientes {

            background-color: #191e1f;
            border: 1px solid #303a3b;
            border-radius: 12px;
            padding: 30px;

        }


        h1 {

            color: #ffffff;
            margin-bottom: 25px;

        }


        .titulo span {

            color: #719999;

        }


        .table {

            color: white;

        }


        .table thead {

            background-color: #719999;
            color: #111516;

        }


        .table tbody tr {

            border-color: #303a3b;

        }


        .btn-cortai {

            background-color: #719999;
            border: none;
            color: #111516;
            font-weight: bold;

        }


        .btn-cortai:hover {

            background-color: #86aaaa;

        }


        .data {

            color: #aeb5b3;

        }
    </style>


</head>


<body>


    <div class="container">


        <div class="card-clientes">


            <h1 class="titulo">

                Lista de <span>Clientes</span>

            </h1>


            <div class="table-responsive">


                <table class="table table-hover align-middle">


                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Nome</th>

                            <th>Telefone</th>

                            <th>Email</th>

                            <th>Cadastro</th>

                            <th>Ações</th>

                        </tr>

                    </thead>



                    <tbody>


                        <?php


                        if (mysqli_num_rows($resultado) > 0) {


                            while ($cliente = mysqli_fetch_assoc($resultado)) {


                                ?>


                                <tr>


                                    <td>

                                        <?= $cliente['id_cliente']; ?>

                                    </td>


                                    <td>

                                        <?= $cliente['nome']; ?>

                                    </td>


                                    <td>

                                        <?= $cliente['telefone']; ?>

                                    </td>


                                    <td>

                                        <?= $cliente['email']; ?>

                                    </td>


                                    <td class="data">

                                        <?= date("d/m/Y H:i", strtotime($cliente['data_cadastro'])); ?>

                                    </td>


                                    <td>


                                        <a href="#" class="btn btn-cortai btn-sm">

                                            Editar

                                        </a>


                                        <a href="#" class="btn btn-danger btn-sm">

                                            Excluir

                                        </a>


                                    </td>


                                </tr>


                                <?php


                            }


                        } else {


                            ?>


                            <tr>

                                <td colspan="6" class="text-center">

                                    Nenhum cliente cadastrado.

                                </td>

                            </tr>


                            <?php


                        }


                        ?>


                    </tbody>


                </table>


            </div>


        </div>


    </div>



</body>

</html>