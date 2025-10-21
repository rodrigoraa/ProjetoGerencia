<?php
require_once __DIR__ . '/../Models/Prato.php';

class PratosController
{
    private $pratoModel;

    public function __construct()
    {
        $this->pratoModel = new Prato();
    }

    public function index()
    {
        $pratos = $this->pratoModel->getAll();
        http_response_code(200);
        echo json_encode($pratos);
    }

    public function show($id)
    {
        $prato = $this->pratoModel->find($id);
        if ($prato) {
            http_response_code(200);
            echo json_encode($prato);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Prato não encontrado."]);
        }
    }

    public function store($data)
    {
        if (empty($data['nome']) || empty($data['preco'])) {
            http_response_code(400);
            echo json_encode(["message" => "O nome e o preço são obrigatórios."]);
            return;
        }

        $newId = $this->pratoModel->create($data);
        http_response_code(201);
        echo json_encode(["message" => "Prato criado com sucesso.", "id" => $newId]);
    }

    public function update($id, $data)
    {
        $affectedRows = $this->pratoModel->update($id, $data);

        if ($affectedRows > 0) {

            http_response_code(200);
            echo json_encode(["message" => "Prato atualizado com sucesso."]);
        } elseif ($affectedRows === 0) {
            http_response_code(404);
            echo json_encode(["message" => "Prato não encontrado para atualização."]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Erro interno na atualização."]);
        }
    }
    public function destroy($id)
    {
        if ($this->pratoModel->delete($id)) {
            http_response_code(200);
            echo json_encode(["message" => "Prato excluído com sucesso."]);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Prato não encontrado para exclusão."]);
        }
    }
}