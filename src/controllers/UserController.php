<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once __DIR__ . '/../models/UserModel.php';

class UserController
{

    /**
     * Get user or a list of users
     *
     * @param int $idUser User identity
     * @param string $token Param to find user by token
     * @param int $page Page to offset data from table
     * @return JSON
     */
    public function indexAction($idUser, $token = '', $page = null)
    {
        try {
            $userModel = new UserModel();
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

                $response = $userModel->select($columns, [], [], [], [], $page);
            }
        } catch (Exception $e) {
            http_response_code(503);
            $response = [
                'mensagem' => 'Erro ao criar usuário: ' . $e->getMessage()
            ];
        }

        echo json_encode($response);
    }

    /**
     * Create a new user
     *
     * @param array $data User information data to save 
     * @return JSON
     */
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
            $response = ['mensagem' => 'Erro ao criar usuário: ' . $e->getMessage()];
        }

        echo json_encode($response);
    }

    /**
     * Edit user
     *
     * @param string $token Param to find logged user
     * @param array $data User data that will be modify
     * @return JSON
     */
    public function putAction($token, $data = [])
    {
        try {
            $user = new UserModel();
            $user->email = $data['email'] ?? '';
            $user->name = $data['name'] ?? '';
            $user->password = $data['password'] ?? '';
            $user->idUser = $data['iduser'] ?? '';
            $user->update($token);
            $response = [
                'mensagem' => 'Usuário atualizado com sucesso.'
            ];
        } catch (Exception $e) {
            http_response_code(503);
            $response = [
                'mensagem' => 'Erro ao criar usuário: ' . $e->getMessage()
            ];
        }

        echo json_encode($response);
    }

    /**
     * Remove user
     *
     * @param int $idUser User identification
     * @return JSON
     */
    public function deleteAction($idUser = null)
    {
        try {
            $userModel = new UserModel();
            $userModel->delete($idUser);
            $response = [
                'mensagem' => 'Usuario removido com sucesso.'
            ];
        } catch (Exception $e) {
            http_response_code(503);
            $response = [
                'mensagem' => 'Erro ao criar usuário: ' . $e->getMessage()
            ];
        }

        echo json_encode($response);
    }
}
