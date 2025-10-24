<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

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
        'app/Controllers/',
        'app/Models/',
        'Database/',
        '',
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
if (in_array($method, ['POST', 'PUT'])) {
    $content = file_get_contents("php://input");
    $inputData = json_decode($content, true) ?? [];
    if ($method === 'PUT' && isset($uriSegments[2])) {
        $inputData['id'] = (int)$uriSegments[2];
    }
}

$resource = $uriSegments[2] ?? '';
$id = $uriSegments[3] ?? null;

function respond($status, $data) {
    http_response_code($status);
    echo json_encode($data);
    exit();
}

try {
    switch ($resource) {
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
                    respond(400, ["message" => "O ID é obrigatório para atualização."]);
                }
            } elseif ($method === 'DELETE') {
                if ($id) {
                    $controller->destroy($id);
                } else {
                    respond(400, ["message" => "O ID é obrigatório para exclusão."]);
                }
            } else {
                respond(405, ["message" => "Método não permitido para /pratos"]);
            }
            break;

        case 'login':
case 'cadastro':
    $authController = new AuthController();

    if ($resource === 'cadastro' && $method === 'POST') {
        $authController->register($inputData);
    } elseif ($resource === 'login' && $method === 'POST') {
        $authController->login($inputData);
    } else {
        respond(405, ["message" => "Método não permitido."]);
    }
    break;


        case '':
            respond(200, ["message" => "Bem-vindo à API do Restaurante!", "status" => "online"]);
            break;

        default:
            respond(404, ["message" => "Recurso não encontrado."]);
            break;
    }
} catch (Throwable $t) {
    respond(500, ["message" => "Erro interno: " . $t->getMessage()]);
}
