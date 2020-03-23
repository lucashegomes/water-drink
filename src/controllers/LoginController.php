<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/../config/DBConnection.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/DrinksByUserModel.php';

class LoginController
{
    public function __construct($data)
    {
        if (empty($data) || empty($data['email']) || empty($data['password'])) {
            return false;
        }

        $arDataUser = (new UserModel())->loginUser($data);
        
        if (count($arDataUser) > 0) {
            if ($arDataUser['mensagem']) {
                $response = $arDataUser;
            } else {
                $countDrink =  (new DrinksByUserModel())->countDrink($arDataUser['ID_USER_USR']);
                $response = [
                    'token' => $arDataUser['ST_TOKEN_USR'],
                    'iduser' => $arDataUser['ID_USER_USR'],
                    'email' => $arDataUser['ST_EMAIL_USR'],
                    'name' => $arDataUser['ST_NAME_USR'],
                    'drink_counter' => $countDrink,
                ];
            }
        }

        echo json_encode($response);
    }
}
