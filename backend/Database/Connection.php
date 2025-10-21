<?php

require_once __DIR__ . '/../DotEnv.php';

class Connection
{
    private static $pdo = null;

    public static function connect(): PDO
    {
        if (self::$pdo === null) {
            
            $dotEnvPath = __DIR__ . '/../../.env';
            
            if (file_exists($dotEnvPath)) {
                 (new DotEnv($dotEnvPath))->load();
            } else {
                error_log("Atenção: Arquivo .env não encontrado em: " . $dotEnvPath);
            }

            $host = getenv('DB_HOST') ?: 'localhost';
            $port = getenv('DB_PORT') ?: '5432';
            $dbName = getenv('DB_NAME') ?: 'restaurante';
            $user = getenv('DB_USER') ?: 'postgres';
            $pass = getenv('DB_PASS') ?: '';
            
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbName";

            try {
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                
                self::$pdo = new PDO($dsn, $user, $pass, $options);
                
            } catch (\PDOException $e) {
                http_response_code(500);
                die("Erro de Conexão com o Banco de Dados: " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
    
    private function __clone() {}
    private function __wakeup() {}
}