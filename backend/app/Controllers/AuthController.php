<?php

require_once __DIR__ . '/../Models/Cliente.php';

class AuthController
{
    private $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new Cliente();
    }

    public function register($data)
    {
        if (empty($data['nome']) || empty($data['email']) || empty($data['senha_plana']) || empty($data['cpf'])) {
            http_response_code(400);
            echo json_encode(["message" => "Nome, email, senha e CPF são obrigatórios para o cadastro."]);
            return;
        }

        if ($this->clienteModel->emailExists($data['email'])) {
            http_response_code(409);
            echo json_encode(["message" => "Este email já está cadastrado."]);
            return;
        }
        
        if ($this->clienteModel->cpfExists($data['cpf'])) {
            http_response_code(409);
            echo json_encode(["message" => "Este CPF já está cadastrado."]);
            return;
        }

        try {
            $newId = $this->clienteModel->create($data);
            
            http_response_code(201);
            echo json_encode([
                "message" => "Cadastro realizado com sucesso! 🎉",
                "id" => $newId,
            ]);
        } catch (\PDOException $e) {
            http_response_code(500); 
            echo json_encode(["message" => "Erro interno ao cadastrar o cliente."]);
        }
    }

    public function login($data)
    {
        if (empty($data['email']) || empty($data['senha_plana'])) {
            http_response_code(400);
            echo json_encode(["message" => "Email e senha são obrigatórios para o login."]);
            return;
        }

        $cliente = $this->clienteModel->findByEmail($data['email']);

        if ($cliente && password_verify($data['senha_plana'], $cliente['senha'])) {
            
            http_response_code(200);
            
            unset($cliente['senha']); 

            echo json_encode([
                "message" => "Login bem-sucedido! 👋",
                "cliente" => $cliente
            ]);
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Email ou senha incorretos."]);
        }
    }
}