<?php

include_once('./config.php');
include_once('./src/controllers/UserController.php');
include_once('./src/controllers/LoginController.php');
include_once('./src/controllers/DrinksByUserController.php');

$url = 'http://localhost:8080/';
$endpoint = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$request = str_replace($url, '', $endpoint);
$request = explode('/', strtolower($request));

/**
 * Request HTTP verb method 
 */
$request['method'] = $_SERVER['REQUEST_METHOD'];

/**
 * Get token param from the request header if exists
 */
if (isset($_SERVER['HTTP_TOKEN'])) {
    $request['token'] = $_SERVER['HTTP_TOKEN'];
}

/**
 * Print a message error in JSON format, setting the HTTP response code
 *
 * @param string $message Param with the string error set 
 * @param integer $httpResponseCode Param to set the code of HTTP response
 * @return void
 */
function messageError($message = '', $httpResponseCode = 500)
{
    if ($message) {
        http_response_code($httpResponseCode);
        echo json_encode([
            'mensagem' => $message
        ]);

        die();
    }
}

/**
 * Validate if param token exists on the header request
 *
 * @return void
 */
function checkToken($token = '')
{
    if (empty($token)) {
        messageError("Usuário não autenticado. Informar o token para continuar.");
    }
}

/**
 * Convert params from JSON to array
 */
$data = json_decode(file_get_contents('php://input'), true);

/**
 * Direct URL endpoint routes to the especific controller
 */
if ($request[0] == 'users') {

    if (isset($request[1]) && is_numeric($request[1])) {
        checkToken($request['token']);
        /**
         * Register quantity of water drinked
         */
        if ($request['method'] == 'POST' && $request[2] == 'drink') {
            return (new DrinksByUserController())->increaseDrinkAction($request['token'], $data);
        }

        /**
         * Get data from especific user
         */
        if ($request['method'] == 'GET') {
            return (new UserController())->indexAction($request[1], $request['token']);
        }

        /**
         * Edit especific user
         */
        if ($request['method'] == 'PUT') {
            $data['iduser'] = (isset($request[1]) && is_numeric($request[1])) ? $request[1] : null;
            return (new UserController())->putAction($request['token'], $data);
        }

        /**
         * Remove especific user
         */
        if ($request['method'] == 'DELETE') {
            return (new UserController())->deleteAction($request[1]);
        }
    }

    /**
     * Create a new user if all data required was set on request entry
     */
    if ($request['method'] == 'POST') {
        if ($data['email'] && $data['name'] && $data['password']) {
            return (new UserController())->postAction($data);
        } else {
            messageError("Para cadastrar um novo usuário será necessário informar os parâmetros name, email e password.");
        }
    }

    /**
     * Get all users with / without pagination
     */
    if ($request['method'] == 'GET') {
        checkToken($request['token']);
        $page = ($request[1] == 'pagina' && isset($request[2]) && is_numeric($request[2])) ? $request[2] : null;
        return (new UserController())->indexAction(null, null, $page);
    }
}

/**
 * Login user if required data is on the request
 */
if ($request[0] == 'login' && $request['method'] == 'POST') {
    if (!$data['email']) {
        messageError("Email não informado.");
    } elseif (!$data['password']) {
        messageError("Senha não informada.");
    } else {
        return new LoginController($data);
    }
}

/**
 * Get miligram ranking of current date
 */
if ($request[0] == 'ranking' && $request['method'] == 'GET') {
    return (new DrinksByUserController())->userRankingAction();
}

/**
 * Get user drink history
 */
if ($request[0] == 'history' && isset($request[1]) && $request['method'] == 'GET') {
    return (new DrinksByUserController())->userHistoryAction($request[1]);
}

/**
 * Dispatch 404 if endpoint not found
 */
messageError("Endpoint não encontrado", 404);
