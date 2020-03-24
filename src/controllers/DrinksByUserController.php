<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

class DrinksByUserController
{
    /**
     * Increase drink count and set current user miligram
     *
     * @param [type] $token key to authenticate / find user
     * @param array $data
     * @return JSON
     */
    public function increaseDrinkAction($token, $data = [])
    {
        try {
            $arDataUser = (new UserModel())->select([], ["ST_TOKEN_USR = '$token'"]);
            $drinkByUserModel = new DrinksByUserModel();
            $drinkByUserModel->idUser = $arDataUser['ID_USER_USR'];
            $drinkByUserModel->mlDrinked = $data['drink_ml'];
            $drinkByUserModel->insert();
            $countDrink = $drinkByUserModel->countDrink($arDataUser['ID_USER_USR']);
    
            $response = [
                'iduser' => $arDataUser['ID_USER_USR'],
                'email' => $arDataUser['ST_EMAIL_USR'],
                'name' => $arDataUser['ST_NAME_USR'],
                'drink_counter' => $countDrink,
            ];
        } catch(Exception $e) {
            $response = [
                'mensagem' => "Erro ao inserir quantidade de água bebida: " . $e->getMessage()
            ];
        }

        echo json_encode($response);

    }

    /**
     * Get ranking of miligram drinked on today
     *
     * @return JSON
     */
    public function userRankingAction()
    {
        try {
            $response = (new DrinksByUserModel())->getRankingCurrentDate();
        } catch(Exception $e) {
            $response = [
                'mensagem' => "Erro ao exibir ranking: " . $e->getMessage()
            ];
        }

        echo json_encode($response);
    }

    /**
     * Get user drink history
     *
     * @param $idUser User identity
     * @return JSON
     */
    public function userHistoryAction($idUser)
    {
        try {
            $response = (new DrinksByUserModel())->getHistoryByUser($idUser);
        } catch(Exception $e) {
            $response = [
                'mensagem' => "Erro ao exibir o histórico do usuário: " . $e->getMessage()
            ];
        }

        echo json_encode($response);
    }
}