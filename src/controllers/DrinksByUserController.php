<?php

class DrinksByUserController
{
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
                'mensagem' => "Erro ao inserir quantidade de água bebida: $e"
            ];
        }

        echo json_encode($response);

    }

    public function userRankingAction()
    {
        try {
            $response = (new DrinksByUserModel())->getRankingCurrentDate();
        } catch(Exception $e) {
            $response = [
                'mensagem' => "Erro ao exibir ranking: $e"
            ];
        }

        echo json_encode($response);
    }

    public function userHistoryAction($idUser)
    {
        try {
            $response = (new DrinksByUserModel())->getHistoryByUser($idUser);
        } catch(Exception $e) {
            $response = [
                'mensagem' => "Erro ao exibir o histórico do usuário: $e"
            ];
        }

        echo json_encode($response);
    }
}