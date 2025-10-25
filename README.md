Passo a passo para executar a aplicação

Instalar as seguintes extensões no VSCode
-> SQLTools: https://marketplace.visualstudio.com/items?itemName=mtxr.sqltools / ID: mtxr.sqltools
-> SQLTools PostgreSQL/Cockroach Driver: https://marketplace.visualstudio.com/items?itemName=mtxr.sqltools-driver-pg mtxr.sqltools-driver-pg / ID: mtxr.sqltools-driver-pg

Instalar o PostgreSQL 18 e adicionar ao PATH -> https://www.enterprisedb.com/downloads/postgres-postgresql-downloads

Instalar o XAMPP -> https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.12/xampp-windows-x64-8.2.12-0-VS16-installer.exe

Configuração do XAMPP -> Após instalado, iniciar o XAMPP Control Panel -> Em APACHE, ir em CONFIG > PHP(php.ini) e remover o ";"(ponto e vírgula) das seguintes extensões
;extension=pdo_pgsql
;extension=pgsql

Voltando a tela inicial do XAMPP Control Panel, novamente em APACHE > CONFIG > Apache(httpd.conf) e remover o "#"(hashtag) do seguinte módulo
#LoadModule rewrite_module modules/mod_rewrite.so

Ainda dentro de APACHE > CONFIG > Apache(httpd.conf) - Procure por Listen 80 - Se refere a porta do servidor, mude a porta para 8080

DETALHE IMPORTANTE ⚠️⚠️⚠️
O projeto deve estar dentro da pasta dos arquivos do XAMP - Normalmente localizada em: C:\xampp\htdocs

Antes de iniciar a conexão com o banco no SQLTools, executar esse comando no terminal -> psql -U postgres -f C:\xampp\htdocs\ProjetoGerencia\banco_de_dados\init_database.sql

Agora o servidor Apache já pode ser iniciado.

link para acesso -> http://localhost:8080/ProjetoGerencia/frontend/
