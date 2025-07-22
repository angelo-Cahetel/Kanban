-- TABLEA DE USUÁRIOS
CREATE TABLE usuarios (
    usuario_id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('COMUM', 'GERENTE') DEFAULT 'COMUM' NOT NULL, -- adicionando para controle de acesso
    data_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- TABLEA DE TAREFAS
CREATE TABLE tarefas (
    tarefa_id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT,
    prioridade ENUM('BAIXA', 'MEDIA', 'ALTA', 'URGENTE') DEFAULT 'MEDIA' NOT NULL, -- adicionando prioridade
    status ENUM('A_FAZER', 'EM_ANDAMENTO', 'REVISAO', 'CONCLUIDA') DEFAULT 'A_FAZER' NOT NULL, 
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_inicio DATETIME NULL, -- computar o tempo total
    data_fim DATETIME NULL, -- computar o tempo total
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id)
);