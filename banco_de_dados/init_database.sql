-- ======================================================
-- SISTEMA DE GESTÃO PARA RESTAURANTE
-- Banco de Dados: restaurante
-- Autor: Calebe Henrique dos Santos Delmatta
-- RMG: 802.556
-- ======================================================

CREATE DATABASE restaurante;
\c restaurante;

-- ======================================================
-- TABELAS PRINCIPAIS
-- ======================================================

-- CLIENTES (com autenticação)
CREATE TABLE clientes (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20),
    endereco VARCHAR(255),
    cpf VARCHAR(14) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL
);

-- PRATOS
CREATE TABLE pratos (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    categoria VARCHAR(50)
);

-- INGREDIENTES / ESTOQUE
CREATE TABLE ingredientes (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    quantidade INT NOT NULL DEFAULT 0,
    quantidade_minima INT NOT NULL DEFAULT 5
);

-- PEDIDOS
CREATE TABLE pedidos (
    id SERIAL PRIMARY KEY,
    cliente_id INT NOT NULL,
    data_pedido TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'Pendente',
    forma_pagamento VARCHAR(20),
    valor_total DECIMAL(10,2) DEFAULT 0,
    CONSTRAINT pedidos_cliente_id_fkey
        FOREIGN KEY (cliente_id)
        REFERENCES clientes(id)
        ON DELETE CASCADE
);

-- ITENS DO PEDIDO
CREATE TABLE itens_pedido (
    id SERIAL PRIMARY KEY,
    pedido_id INT NOT NULL,
    prato_id INT NOT NULL,
    quantidade INT NOT NULL,
    subtotal DECIMAL(10,2),
    CONSTRAINT itens_pedido_pedido_id_fkey
        FOREIGN KEY (pedido_id)
        REFERENCES pedidos(id)
        ON DELETE CASCADE,
    CONSTRAINT itens_pedido_prato_id_fkey
        FOREIGN KEY (prato_id)
        REFERENCES pratos(id)
        ON DELETE RESTRICT
);

-- PAGAMENTOS
CREATE TABLE pagamentos (
    id SERIAL PRIMARY KEY,
    pedido_id INT NOT NULL,
    tipo VARCHAR(20),
    comprovante VARCHAR(255),
    valor DECIMAL(10,2),
    data_pagamento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pagamentos_pedido_id_fkey
        FOREIGN KEY (pedido_id)
        REFERENCES pedidos(id)
        ON DELETE CASCADE
);

-- ======================================================
-- ÍNDICES
-- ======================================================
CREATE INDEX idx_clientes_cpf ON clientes (cpf);
CREATE INDEX idx_clientes_email ON clientes (email);
CREATE INDEX idx_clientes_nome ON clientes (nome);
CREATE INDEX idx_pedidos_status ON pedidos (status);
CREATE INDEX idx_pagamentos_data ON pagamentos (data_pagamento);

-- ======================================================
-- VIEWS
-- ======================================================

-- Histórico de pedidos detalhado
CREATE OR REPLACE VIEW vw_pedidos_detalhados AS
SELECT 
    p.id AS pedido_id,
    c.nome AS cliente,
    p.data_pedido,
    p.status,
    p.forma_pagamento,
    SUM(i.subtotal) AS total_itens,
    p.valor_total,
    COUNT(i.id) AS qtd_itens
FROM pedidos p
JOIN clientes c ON c.id = p.cliente_id
LEFT JOIN itens_pedido i ON i.pedido_id = p.id
GROUP BY p.id, c.nome, p.data_pedido, p.status, p.forma_pagamento, p.valor_total;

-- Estoque com alerta
CREATE OR REPLACE VIEW vw_estoque_alerta AS
SELECT 
    id,
    nome,
    quantidade,
    quantidade_minima,
    CASE 
        WHEN quantidade < quantidade_minima THEN '⚠️ Estoque Baixo'
        ELSE 'OK'
    END AS status_estoque
FROM ingredientes;

-- Faturamento diário
CREATE OR REPLACE VIEW vw_faturamento_diario AS
SELECT 
    DATE(data_pagamento) AS data,
    SUM(valor) AS total_dia,
    COUNT(*) AS qtd_pagamentos
FROM pagamentos
GROUP BY DATE(data_pagamento)
ORDER BY data DESC;

-- Pratos mais vendidos
CREATE OR REPLACE VIEW vw_top_pratos AS
SELECT 
    p.nome AS prato,
    SUM(i.quantidade) AS total_vendido,
    SUM(i.subtotal) AS receita_gerada
FROM itens_pedido i
JOIN pratos p ON p.id = i.prato_id
GROUP BY p.nome
ORDER BY total_vendido DESC;

-- Clientes (dados públicos, sem senha)
CREATE OR REPLACE VIEW vw_clientes_publicos AS
SELECT
    id,
    nome,
    email,
    telefone,
    endereco,
    cpf,
    data_cadastro
FROM clientes;

-- Resumo de clientes com total de pedidos
CREATE OR REPLACE VIEW vw_clientes_resumo AS
SELECT 
    c.id,
    c.nome,
    c.cpf,
    COUNT(p.id) AS total_pedidos,
    COALESCE(SUM(p.valor_total), 0) AS gasto_total
FROM clientes c
LEFT JOIN pedidos p ON p.cliente_id = c.id
GROUP BY c.id, c.nome, c.cpf;

-- ======================================================
-- FUNCTIONS
-- ======================================================

-- Atualiza valor total do pedido
CREATE OR REPLACE FUNCTION atualizar_valor_total(p_pedido_id INT)
RETURNS VOID AS $$
BEGIN
    UPDATE pedidos
    SET valor_total = (
        SELECT COALESCE(SUM(subtotal), 0)
        FROM itens_pedido
        WHERE pedido_id = p_pedido_id
    )
    WHERE id = p_pedido_id;
END;
$$ LANGUAGE plpgsql;

-- Reduz estoque
CREATE OR REPLACE FUNCTION reduzir_estoque(p_ingrediente_id INT, p_qtd INT)
RETURNS VOID AS $$
BEGIN
    UPDATE ingredientes
    SET quantidade = GREATEST(quantidade - p_qtd, 0)
    WHERE id = p_ingrediente_id;
END;
$$ LANGUAGE plpgsql;

-- Faturamento por período
CREATE OR REPLACE FUNCTION faturamento_periodo(data_inicial DATE, data_final DATE)
RETURNS NUMERIC AS $$
DECLARE
    total NUMERIC;
BEGIN
    SELECT COALESCE(SUM(valor), 0)
    INTO total
    FROM pagamentos
    WHERE DATE(data_pagamento) BETWEEN data_inicial AND data_final;
    RETURN total;
END;
$$ LANGUAGE plpgsql;

-- Listar pedidos por status
CREATE OR REPLACE FUNCTION listar_pedidos_por_status(p_status VARCHAR)
RETURNS TABLE (
    pedido_id INT,
    cliente_nome VARCHAR,
    data_pedido TIMESTAMP,
    valor_total NUMERIC
) AS $$
BEGIN
    RETURN QUERY
    SELECT p.id, c.nome, p.data_pedido, p.valor_total
    FROM pedidos p
    JOIN clientes c ON c.id = p.cliente_id
    WHERE p.status = p_status
    ORDER BY p.data_pedido DESC;
END;
$$ LANGUAGE plpgsql;

-- Total de clientes ativos
CREATE OR REPLACE FUNCTION total_clientes()
RETURNS INT AS $$
DECLARE
    qtd INT;
BEGIN
    SELECT COUNT(*) INTO qtd FROM clientes;
    RETURN qtd;
END;
$$ LANGUAGE plpgsql;

-- Trigger: atualiza automaticamente valor total
CREATE OR REPLACE FUNCTION trg_atualizar_total()
RETURNS TRIGGER AS $$
BEGIN
    PERFORM atualizar_valor_total(NEW.pedido_id);
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER atualizar_total_pedido
AFTER INSERT OR UPDATE OR DELETE ON itens_pedido
FOR EACH ROW
EXECUTE FUNCTION trg_atualizar_total();

-- Autenticação de cliente (login)
CREATE OR REPLACE FUNCTION autenticar_cliente(p_email VARCHAR, p_senha VARCHAR)
RETURNS TABLE (
    id INT,
    nome VARCHAR,
    email VARCHAR,
    telefone VARCHAR
) AS $$
BEGIN
    RETURN QUERY
    SELECT c.id, c.nome, c.email, c.telefone
    FROM clientes c
    WHERE c.email = p_email
      AND c.senha = p_senha;
END;
$$ LANGUAGE plpgsql;
