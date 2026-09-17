CREATE TABLE clientes (

    id_cliente INT AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(100) NOT NULL,

    telefone VARCHAR(20) NOT NULL,

    email VARCHAR(100),

    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE barbeiros (

    id_barbeiro INT AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(100) NOT NULL,

    telefone VARCHAR(20),

    especialidade VARCHAR(100),

    status BOOLEAN DEFAULT TRUE
);

CREATE TABLE servicos (

    id_servico INT AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(100) NOT NULL,

    descricao VARCHAR(255),

    valor DECIMAL(10,2) NOT NULL,

    duracao INT NOT NULL
);


CREATE TABLE agendamentos (

    id_agendamento INT AUTO_INCREMENT PRIMARY KEY,

    id_cliente INT NOT NULL,

    id_barbeiro INT NOT NULL,

    id_servico INT NOT NULL,

    data_agendamento DATE NOT NULL,

    horario TIME NOT NULL,

    status VARCHAR(30) DEFAULT 'Agendado',


    FOREIGN KEY (id_cliente)
    REFERENCES clientes(id_cliente),


    FOREIGN KEY (id_barbeiro)
    REFERENCES barbeiros(id_barbeiro),


    FOREIGN KEY (id_servico)
    REFERENCES servicos(id_servico)

);


CREATE TABLE usuarios (

    id_usuario INT AUTO_INCREMENT PRIMARY KEY,

    nome VARCHAR(100),

    login VARCHAR(50),

    senha VARCHAR(255)

);