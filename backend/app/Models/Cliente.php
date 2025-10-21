<?php

require_once __DIR__ . '/../Database/Connection.php';

class Cliente {
    private $pdo;

    public function __construct() {
        $this->pdo = Connection::connect();
    }

    public function create($data) {
        $hashedPassword = password_hash($data['senha_plana'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO clientes (nome, email, senha, telefone, endereco, cpf) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute([
            $data['nome'], 
            $data['email'], 
            $hashedPassword,
            $data['telefone'] ?? null,
            $data['endereco'] ?? null,
            $data['cpf']
        ]);
        
        return $this->pdo->lastInsertId();
    }

    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT id, nome, email, senha, cpf FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function emailExists($email) {
        $stmt = $this->pdo->prepare("SELECT 1 FROM clientes WHERE email = ?");
        $stmt->execute([$email]);
        return (bool)$stmt->fetchColumn();
    }

    public function cpfExists($cpf) {
        $stmt = $this->pdo->prepare("SELECT 1 FROM clientes WHERE cpf = ?");
        $stmt->execute([$cpf]);
        return (bool)$stmt->fetchColumn();
    }
}