const dias = document.getElementById("dias");
const mesAno = document.getElementById("mesAno");

const mesAnterior = document.getElementById("mesAnterior");
const proximoMes = document.getElementById("proximoMes");

let dataAtual = new Date(2026, 7, 1);

const nomesMeses = [
    "Janeiro",
    "Fevereiro",
    "Março",
    "Abril",
    "Maio",
    "Junho",
    "Julho",
    "Agosto",
    "Setembro",
    "Outubro",
    "Novembro",
    "Dezembro"
];


function criarCalendario() {

    dias.innerHTML = "";

    const ano = dataAtual.getFullYear();
    const mes = dataAtual.getMonth();

    mesAno.textContent = nomesMeses[mes] + " " + ano;

    const primeiroDia = new Date(ano, mes, 1);

    const ultimoDia = new Date(ano, mes + 1, 0);

    const quantidadeDias = ultimoDia.getDate();

    const diaSemanaInicial = primeiroDia.getDay();


    for (let i = 0; i < diaSemanaInicial; i++) {

        const espaco = document.createElement("div");

        dias.appendChild(espaco);
    }


    for (let dia = 1; dia <= quantidadeDias; dia++) {

        const botaoDia = document.createElement("button");

        botaoDia.type = "button";

        botaoDia.textContent = dia;

        botaoDia.classList.add("dia");


        const dataDoDia = new Date(ano, mes, dia);

        const diaSemana = dataDoDia.getDay();


        // Domingo e sábado ficam indisponíveis
        if (diaSemana === 0 || diaSemana === 6) {

            botaoDia.classList.add("indisponivel");

            botaoDia.disabled = true;

        } else {

            botaoDia.addEventListener("click", function() {

                selecionarData(
                    ano,
                    mes,
                    dia,
                    botaoDia
                );

            });

        }


        dias.appendChild(botaoDia);
    }
}



function selecionarData(ano, mes, dia, botao) {

    // Remove a seleção dos outros dias
    document.querySelectorAll(".dia").forEach(function(elemento) {

        elemento.classList.remove("selecionado");

    });


    // Seleciona o dia clicado
    botao.classList.add("selecionado");


    console.log(
        "Data escolhida:",
        dia + "/" + (mes + 1) + "/" + ano
    );


    // Mostrar os horários
    mostrarHorarios();
}




function mostrarHorarios() {

    const areaHorarios = document.getElementById("areaHorarios");

    const horarios = document.getElementById("horarios");


    areaHorarios.style.display = "block";


    horarios.innerHTML = "";


    const listaHorarios = [

        "09:00",
        "10:00",
        "11:00",
        "14:00",
        "15:00",
        "16:00",
        "17:00",

    ];


    // Criar os botões
    listaHorarios.forEach(function(hora) {

        const botao = document.createElement("button");

        botao.type = "button";

        botao.textContent = hora;

        botao.classList.add("horario");


        // Quando clicar no horário
        botao.addEventListener("click", function() {


            // Remove seleção dos outros horários
            document.querySelectorAll(".horario").forEach(function(elemento) {

                elemento.classList.remove("selecionado");

            });


            // Seleciona o horário clicado
            botao.classList.add("selecionado");


            console.log(
                "Horário escolhido:",
                hora
            );

        });


        // Coloca o botão dentro da div
        horarios.appendChild(botao);

    });
}



mesAnterior.addEventListener("click", function() {

    dataAtual.setMonth(
        dataAtual.getMonth() - 1
    );

    criarCalendario();

});


proximoMes.addEventListener("click", function() {

    dataAtual.setMonth(
        dataAtual.getMonth() + 1
    );

    criarCalendario();

});



criarCalendario();