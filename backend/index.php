<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

spl_autoload_register(function ($className) {
    $directories = [
        'backend/Controllers/',
        'backend/Models/',
        'backend/Database/',
        'backend/',
    ];

    foreach ($directories as $dir) {
        $file = __DIR__ . '/' . $dir . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

$method = $_SERVER['REQUEST_METHOD'];

$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$uriSegments = explode('/', $uri);

$inputData = [];
if ($method === 'POST' || $method === 'PUT') {
    $content = file_get_contents("php://input");
    $inputData = json_decode($content, true);
    if ($method === 'PUT' && isset($uriSegments[2])) {
        $inputData['id'] = (int)$uriSegments[2];
    }
}

$resource = $uriSegments[1] ?? '';
$id = $uriSegments[2] ?? null;

switch ($resource) {
    case 'cadastro':
        if ($method === 'POST') {
            $controller = new AuthController();
            $controller->register($inputData);
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método não permitido para /cadastro"]);
        }
        break;

    case 'login':
        if ($method === 'POST') {
            $controller = new AuthController();
            $controller->login($inputData);
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método não permitido para /login"]);
        }
        break;

    case 'pratos':
        $controller = new PratosController();

        if ($method === 'GET') {
            if ($id) {
                $controller->show($id);
            } else {
                $controller->index();
            }
        } elseif ($method === 'POST') {
            $controller->store($inputData);
        } elseif ($method === 'PUT') {
            if ($id) {
                $controller->update($id, $inputData); 
            } else {
                http_response_code(400);
                echo json_encode(["message" => "O ID é obrigatório para atualização."]);
            }
        } elseif ($method === 'DELETE') {
            if ($id) {
                $controller->destroy($id);
            } else {
                http_response_code(400);
                echo json_encode(["message" => "O ID é obrigatório para exclusão."]);
            }
        } else {
            http_response_code(405);
            echo json_encode(["message" => "Método não permitido para /pratos"]);
        }
        break;

    case '':
        http_response_code(200);
        echo json_encode(["message" => "Bem-vindo à API do Restaurante!", "status" => "online"]);
        break;
        
    default:
        http_response_code(404);
        echo json_encode(["message" => "Recurso não encontrado."]);
        break;
}