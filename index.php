<?php

require_once('./src/config/Routes.php');
include_once('./src/controllers/UserController.php');
include_once('./src/controllers/LoginController.php');
// include_once(__DIR__ . './src/controllers/UserController.php');

function notFound()
{
    http_response_code(404);
    echo json_encode([
        'mensagem' => 'Endpoint não encontrado'
    ]);

    die();
}

$data = json_decode(file_get_contents('php://input'), true);

/**
 * VALIDAR SE TEM TOKEN E TRAVAR AQUI CASO NÃO TENHA (esteja autenticado)
 */
if ($request[0] == 'users') {
    if ($request['method'] == 'POST' && count($request) <= 2) {
        return (new UserController())->postAction($data);
    }
    
    if (is_numeric($request[1])) {
        if ($request['method'] == 'POST' && $request[2] == 'drink') {
            return (new UserController())->increaseDrinkAction($request['token'], $data);
        }

        if($request['method'] == 'GET') {
            return (new UserController())->indexAction($request[1], $request['token']);
        }
        
        if($request['method'] == 'PUT') {
            return (new UserController())->putAction($request['token'], $data);
        }
        
        if($request['method'] == 'DELETE') {
            return (new UserController())->deleteAction($request[1]);
        }
    }

    if ($request['method'] == 'GET') {
        return (new UserController())->indexAction();
    }
}

if ($request[0] == 'login' && $request['method'] == 'POST') {
    return new LoginController($data);
}

if ($request == 'contact-us') {
}

notFound();
