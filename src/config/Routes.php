<?php

include_once('./src/controllers/UserController.php');
include_once('./src/controllers/LoginController.php');
include_once('./src/controllers/DrinksByUserController.php');

$url = 'http://localhost:8080/';
$endpoint = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$request = str_replace($url, '', $endpoint);
$request = explode('/', strtolower($request));
$request['method'] = $_SERVER['REQUEST_METHOD'];

if (isset($_SERVER['HTTP_TOKEN'])) {
    $request['token'] = $_SERVER['HTTP_TOKEN'];
}

function notFound()
{
    http_response_code(404);
    echo json_encode([
        'mensagem' => 'Endpoint não encontrado'
    ]);
}

$data = json_decode(file_get_contents('php://input'), true);

/**
 * VALIDAR SE TEM TOKEN E TRAVAR AQUI CASO NÃO TENHA (esteja autenticado)
 */
if ($request[0] == 'users') {
    if ($request['method'] == 'POST' && count($request) <= 2) {
        return (new UserController())->postAction($data);
    }

    if (isset($request[1]) && is_numeric($request[1])) {
        if ($request['method'] == 'POST' && $request[2] == 'drink') {
            return (new DrinksByUserController())->increaseDrinkAction($request['token'], $data);
        }

        if ($request['method'] == 'GET') {
            return (new UserController())->indexAction($request[1], $request['token']);
        }

        if ($request['method'] == 'PUT') {
            return (new UserController())->putAction($request['token'], $data);
        }

        if ($request['method'] == 'DELETE') {
            return (new UserController())->deleteAction($request[1]);
        }
    }

    if ($request['method'] == 'GET') {
        $page = ($request[1] == 'pagina' && isset($request[2]) && is_numeric($request[2])) ? $request[2] : null;
        return (new UserController())->indexAction(null, null, $page);
    }
}

if ($request[0] == 'login' && $request['method'] == 'POST') {
    return new LoginController($data);
}

if ($request[0] == 'rank') {
    return (new DrinksByUserController())->userRankAction();
}

if ($request[0] == 'history' && isset($request[1])) {
    return (new DrinksByUserController())->userHistoryAction($request[1]);
}

notFound();
