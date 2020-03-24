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
    /**
     * Validate informated data to login user
     *
     * @param array $data Array data with login params
     * @return JSON
     */
    public function __construct($data = [])
    {
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
