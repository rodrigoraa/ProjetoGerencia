# 🍽️ Sistema de Gestão para Restaurante — Banco de Dados (PostgreSQL)

## 📖 Descrição
Módulo de banco de dados do Sistema de Gestão para Restaurante.  
Responsável pelo armazenamento de **clientes, pratos, pedidos, pagamentos e estoque**.

---

## 🧩 Estrutura de Arquivos
| Arquivo | Função |
|----------|--------|
| `init_database.sql` | Cria o banco `restaurante` e todas as tabelas, views e funções |
| `.env.example` | Modelo de variáveis de ambiente para conexão |
| `.gitignore` | Ignora o `.env` e arquivos sensíveis |

---

## 🛠️ Requisitos
- necessario o PostgreSQL 18 instalado
- Acesso ao terminal `psql`
- extensões para o vscode: SQLTools PostgreSQL,SQLTools, PostegresSQL.
---

## 🚀 Como Executar

### 1️⃣ Clonar o projeto
```bash
git clone https://github.com/rodrigoraa/ProjetoGerencia.git
cd ProjetoGerencia
git checkout calebe

2️⃣ Executar o script SQL
psql -U postgres -f banco_de_dados/init_database.sql

3️⃣ Verificar o banco
\c restaurante
\dt

