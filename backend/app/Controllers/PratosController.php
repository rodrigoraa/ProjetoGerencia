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
        try {
            $pratos = $this->pratoModel->getAll();
            echo json_encode($pratos);
        } catch (Throwable $t) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao listar pratos: " . $t->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $prato = $this->pratoModel->find($id);
            if ($prato) {
                echo json_encode($prato);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Prato não encontrado."]);
            }
        } catch (Throwable $t) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao buscar prato: " . $t->getMessage()]);
        }
    }

    public function store($data)
    {
        try {
            if (empty($data['nome']) || empty($data['preco'])) {
                http_response_code(400);
                echo json_encode(["message" => "O nome e o preço são obrigatórios."]);
                return;
            }

            $newId = $this->pratoModel->create($data);
            http_response_code(201);
            echo json_encode(["message" => "Prato criado com sucesso.", "id" => $newId]);
        } catch (Throwable $t) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao salvar prato: " . $t->getMessage()]);
        }
    }

    public function update($id, $data)
    {
        try {
            $affectedRows = $this->pratoModel->update($id, $data);
            if ($affectedRows > 0) {
                echo json_encode(["message" => "Prato atualizado com sucesso."]);
            } elseif ($affectedRows === 0) {
                http_response_code(404);
                echo json_encode(["message" => "Prato não encontrado para atualização."]);
            }
        } catch (Throwable $t) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao atualizar prato: " . $t->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            if ($this->pratoModel->delete($id)) {
                echo json_encode(["message" => "Prato excluído com sucesso."]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Prato não encontrado para exclusão."]);
            }
        } catch (Throwable $t) {
            http_response_code(500);
            echo json_encode(["message" => "Erro ao excluir prato: " . $t->getMessage()]);
        }
    }
}
