<?php
// backend/Models/Prato.php

// Garante que a classe de Conexão seja carregada
require_once __DIR__ . '/../Database/Connection.php';

class Prato {
    private $pdo;

    public function __construct() {
        $this->pdo = Connection::connect();
    }
    public function create($data) {
        $sql = "INSERT INTO pratos (nome, descricao, preco, categoria) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$data['nome'], $data['descricao'], $data['preco'], $data['categoria']]);
        return $this->pdo->lastInsertId();
    }
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM pratos");
        return $stmt->fetchAll();
    }
    public function find($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM pratos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    public function update($id, $data) {
        $sql = "UPDATE pratos SET nome = ?, descricao = ?, preco = ?, categoria = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$data['nome'], $data['descricao'], $data['preco'], $data['categoria'], $id]);
        return $stmt->rowCount(); 
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM pratos WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function exists($id) {
        $stmt = $this->pdo->prepare("SELECT 1 FROM pratos WHERE id = ?");
        $stmt->execute([$id]);
        return (bool)$stmt->fetchColumn();
    }
}