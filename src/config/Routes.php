<?php

include_once('./config.php');
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

function messageError($mensagem = '', $httpResponseCode = 500)
{
    if ($mensagem) {
        http_response_code($httpResponseCode);
        echo json_encode([
            'mensagem' => $mensagem
        ]);

        die();
    }
}

function checkToken()
{
    if (empty($request['token'])) {
        messageError("Usuário não autenticado. Informar o token para continuar.");
    }
}

$data = json_decode(file_get_contents('php://input'), true);

if ($request[0] == 'users') {

    if (isset($request[1]) && is_numeric($request[1])) {
        checkToken();
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

    if ($request['method'] == 'POST') {
        if ($data['email'] && $data['name'] && $data['password']) {
            return (new UserController())->postAction($data);
        } else {
            messageError("Para cadastrar um novo usuário será necessário informar os parâmetros name, email e password.");
        }
    }

    if ($request['method'] == 'GET') {
        checkToken();
        $page = ($request[1] == 'pagina' && isset($request[2]) && is_numeric($request[2])) ? $request[2] : null;
        return (new UserController())->indexAction(null, null, $page);
    }
}

if ($request[0] == 'login' && $request['method'] == 'POST') {
    if (!$data['email']) {
        messageError("Email não informado.");
    } elseif (!$data['password']) {
        messageError("Senha não informada.");
    } else {
        return new LoginController($data);
    }
}

if ($request[0] == 'ranking' && $request['method'] == 'GET') {
    return (new DrinksByUserController())->userRankingAction();
}

if ($request[0] == 'history' && isset($request[1]) && $request['method'] == 'GET') {
    return (new DrinksByUserController())->userHistoryAction($request[1]);
}

messageError("Endpoint não encontrado", 404);
