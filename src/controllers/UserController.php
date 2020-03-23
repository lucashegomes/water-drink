<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/../config/DBConnection.php';
require_once __DIR__ . '/../models/UserModel.php';

class UserController
{

    public function indexAction($idUser = 0, $token = '')
    {
        try {
            $userModel = new UserModel();

            if (!(empty($idUser) && empty($token))) {
                $arDataUser = $userModel->getUser($idUser, $token);
                $countDrink =  (new DrinksByUserModel())->countDrink($arDataUser['ID_USER_USR']);

                http_response_code(201);
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

                $response = $userModel->select($columns);
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
            $user = new UserModel();
            $user->email = $data['email'] ?? '';
            $user->name = $data['name'] ?? '';
            $user->password = $data['password'] ?? '';
            $user->token = $data['token'] ?? '';
            $user->create();
        } catch (Exception $e) {
            http_response_code(503);
            echo json_encode([
                'mensagem' => 'Erro ao criar usuário: ' . $e
            ]);
        }
    }

    //falta terminar
    public function putAction($token = '', $data = [])
    {
        try {
            $user = new UserModel();
            $user->email = $data['email'] ?? '';
            $user->name = $data['name'] ?? '';
            $user->password = $data['password'] ?? '';
            $user->token = $data['token'] ?? '';
            $user->create();
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

    public function increaseDrinkAction($token, $data = [])
    {
        try {
            $arDataUser = (new UserModel())->select([], ["ST_TOKEN_USR = '$token'"]);
            $drinkByUserModel = new DrinksByUserModel();
            $drinkByUserModel->idUser = $arDataUser['ID_USER_USR'];
            $drinkByUserModel->mlDrinked = $data['drink_ml'];
            $teste = $drinkByUserModel->insert();
            $countDrink = $drinkByUserModel->countDrink($arDataUser['ID_USER_USR']);
    
            $response = [
                'iduser' => $arDataUser['ID_USER_USR'],
                'email' => $arDataUser['ST_EMAIL_USR'],
                'name' => $arDataUser['ST_NAME_USR'],
                'drink_counter' => $countDrink,
            ];
        } catch(Exception $e) {
            $response = [
                'mensagem' => "Erro ao inserir quantidade de água bebida: $e"
            ];
        }

        echo json_encode($response);

    }
}
