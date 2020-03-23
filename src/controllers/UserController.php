<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/../config/DBConnection.php';
require_once __DIR__ . '/../models/UserModel.php';

class UserController
{

    public function indexAction($idUser, $token = '', $page = null)
    {
        try {
            $userModel = new UserModel();
            http_response_code(200);

            if (!empty($idUser) || !empty($token)) {
                $arDataUser = $userModel->getUser($idUser, $token);
                $countDrink =  (new DrinksByUserModel())->countDrink($arDataUser['ID_USER_USR']);

                $response = [
                    'iduser' => $arDataUser['ID_USER_USR'],
                    'name' => $arDataUser['ST_NAME_USR'],
                    'email' => $arDataUser['ST_EMAIL_USR'],
                    'drink_counter' => $countDrink,
                ];
            } else {
                $columns = [
                    'ID_USER_USR AS iduser',
                    'ST_EMAIL_USR AS email',
                    'ST_NAME_USR AS name',
                    'ST_TOKEN_USR AS token',
                ];

                $response = $userModel->select($columns, [], $page);
            }
        } catch (Exception $e) {
            http_response_code(503);
            $response = [
                'mensagem' => 'Erro ao criar usuário: ' . $e
            ];
        }

        echo json_encode($response);
    }

    public function postAction($data = [])
    {
        try {
            $userModel = new UserModel();
            $userModel->email = $data['email'] ?? '';
            $userModel->name = $data['name'] ?? '';
            $userModel->password = $data['password'] ?? '';

            $userModel->insert();
            http_response_code(201);
            $response = ['mensagem' => 'Usuário cadastrado com sucesso.'];

        } catch (Exception $e) {
            http_response_code(503);
            $response = ['mensagem' => 'Erro ao criar usuário: ' . $e];
        }

        echo json_encode($response);
    }

    public function putAction($token, $data = [])
    {
        try {
            $user = new UserModel();
            $user->email = $data['email'] ?? '';
            $user->name = $data['name'] ?? '';
            $user->password = $data['password'] ?? '';
            $user->update($token);
        } catch (Exception $e) {
            http_response_code(503);
            echo json_encode([
                'mensagem' => 'Erro ao criar usuário: ' . $e
            ]);
        }
    }

    public function deleteAction($idUser = null)
    {
        try {
            $userModel = new UserModel();
            $userModel->delete($idUser);
            
            http_response_code(200);
            $response = [
                'mensagem' => 'Usuario removido com sucesso.' 
            ];
        } catch (Exception $e) {
            http_response_code(503);
            $response = [
                'mensagem' => 'Erro ao criar usuário: ' . $e
            ];
        }

        echo json_encode($response);
    }
}
