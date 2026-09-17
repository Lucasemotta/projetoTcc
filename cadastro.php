<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cortaí - Agendamento</title>

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- Fonte -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background-color: #111516;
            color: #f4f4f1;
            font-family: 'Montserrat', sans-serif;
        }

        /* NAVBAR */

        .navbar-cortai {
            background-color: #111516;
            border-bottom: 1px solid #293436;
            padding: 15px 0;
        }

        .logo {
            height: 55px;
            width: auto;
        }

        .nav-link {
            color: #c8cecc !important;
            font-size: 14px;
            margin-left: 20px;
            transition: 0.3s;
        }

        .nav-link:hover {
            color: #6f9696 !important;
        }

        /* ÁREA PRINCIPAL */

        .hero {
            min-height: calc(100vh - 87px);
        }

        .hero-content {
            padding: 70px 50px;
        }

        .titulo {
            font-size: 48px;
            font-weight: 600;
            line-height: 1.15;
            margin-bottom: 25px;
        }

        .titulo span {
            color: #719999;
        }

        .descricao {
            color: #aeb5b3;
            font-size: 16px;
            line-height: 1.8;
            max-width: 500px;
        }

        .destaques {
            margin-top: 45px;
        }

        .destaque {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            color: #d8dddb;
            font-size: 14px;
        }

        .destaque i {
            color: #719999;
            font-size: 20px;
        }

        /* FORMULÁRIO */

        .form-area {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px;
        }

        .card-cadastro {
            width: 100%;
            max-width: 470px;
            background-color: #191e1f;
            border: 1px solid #303a3b;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        .card-cadastro h2 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .card-subtitulo {
            color: #8f9997;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-label {
            color: #dce1df;
            font-size: 13px;
            font-weight: 500;
        }

        .form-control {
            background-color: #111516;
            border: 1px solid #374143;
            color: #ffffff;
            border-radius: 6px;
            padding: 13px;
        }

        .form-control::placeholder {
            color: #68716f;
        }

        .form-control:focus {
            background-color: #111516;
            color: white;
            border-color: #719999;
            box-shadow: 0 0 0 0.15rem rgba(113, 153, 153, 0.15);
        }

        .btn-cortai {
            background-color: #719999;
            border: none;
            color: #101414;
            font-weight: 600;
            padding: 13px;
            border-radius: 6px;
            transition: 0.3s;
        }

        .btn-cortai:hover {
            background-color: #86aaaa;
            color: #101414;
        }

        .observacao {
            text-align: center;
            color: #68716f;
            font-size: 12px;
            margin-top: 20px;
        }

        /* RODAPÉ */

        .footer {
            border-top: 1px solid #293436;
            padding: 18px 0;
            color: #69716f;
            font-size: 12px;
        }

        /* RESPONSIVO */

        @media (max-width: 991px) {

            .hero-content {
                padding: 50px 30px;
                text-align: center;
            }

            .descricao {
                margin: auto;
            }

            .destaques {
                text-align: left;
                max-width: 350px;
                margin-left: auto;
                margin-right: auto;
            }

            .form-area {
                padding: 20px 30px 50px;
            }

            .titulo {
                font-size: 38px;
            }

        }

    </style>

</head>

<body>


<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-cortai">

    <div class="container">

        <a class="navbar-brand" href="#">

            <img
                src="logo.png"
                alt="Cortaí"
                class="logo"
            >

        </a>


        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menu"
        >

            <span class="navbar-toggler-icon"></span>

        </button>


        <div class="collapse navbar-collapse" id="menu">

            <ul class="navbar-nav ms-auto">

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        Início
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        Serviços
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        Contato
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- PRINCIPAL -->

<main class="hero">

    <div class="container">

        <div class="row min-vh-100">


            <!-- LADO ESQUERDO -->

            <div class="col-lg-6 hero-content d-flex flex-column justify-content-center">

                <p class="text-uppercase small mb-3" style="color: #719999; letter-spacing: 3px;">
                    Agendamento de barbearia
                </p>

                <h1 class="titulo">

                    Seu próximo
                    <br>

                    corte começa
                    <br>

                    <span>aqui.</span>

                </h1>

                <p class="descricao">

                    Escolha seu serviço, encontre o melhor horário
                    e faça seu agendamento de forma rápida e simples.

                </p>


                <div class="destaques">

                    <div class="destaque">

                        <i class="bi bi-calendar-check"></i>

                        <span>
                            Agendamento rápido e organizado
                        </span>

                    </div>


                    <div class="destaque">

                        <i class="bi bi-scissors"></i>

                        <span>
                            Escolha o serviço ideal para você
                        </span>

                    </div>


                    <div class="destaque">

                        <i class="bi bi-clock"></i>

                        <span>
                            Escolha o melhor horário
                        </span>

                    </div>

                </div>

            </div>


            <!-- LADO DIREITO -->

            <div class="col-lg-6 form-area">


                <div class="card-cadastro">

                    <h2>
                        Cadastre-se
                    </h2>

                    <p class="card-subtitulo">

                        Informe seus dados para começar seu agendamento.

                    </p>


                    <form action="salvar_cliente.php" method="POST">


                        <!-- NOME -->

                        <div class="mb-4">

                            <label
                                for="nome"
                                class="form-label"
                            >
                                Nome completo
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="nome"
                                name="nome"
                                placeholder="Digite seu nome completo"
                                required
                            >

                        </div>


                        <!-- TELEFONE -->

                        <div class="mb-4">

                            <label
                                for="telefone"
                                class="form-label"
                            >
                                Telefone
                            </label>

                            <input
                                type="tel"
                                class="form-control"
                                id="telefone"
                                name="telefone"
                                placeholder="(14) 99999-9999"
                                required
                            >

                        </div>


                        <!-- BOTÃO -->

                        <div class="d-grid">

                            <button
                                type="submit"
                                class="btn btn-cortai"
                            >

                                CONTINUAR

                                <i class="bi bi-arrow-right ms-2"></i>

                            </button>

                        </div>


                        <p class="observacao">

                            Seus dados serão utilizados apenas para o agendamento.

                        </p>


                    </form>

                </div>

            </div>

        </div>

    </div>

</main>


<!-- RODAPÉ -->

<footer class="footer">

    <div class="container">

        <div class="d-flex justify-content-between align-items-center">

            <span>
                © 2026 Cortaí
            </span>

            <span>
                Mais que um corte, é estilo.
            </span>

        </div>

    </div>

</footer>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

</body>

</html>