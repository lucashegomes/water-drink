<?php

require_once __DIR__ . '/../models/Model.php';

class UserModel extends Model
{
    protected $_table = "USER";

    public $info = [
        'ST_NAME_USR',
        'ST_EMAIL_USR',
        'ST_PASSWORD_USR',
        'ST_TOKEN_USR',
    ];

    public $idUser = '';
    public $email = '';
    public $name = '';
    public $password = '';
    public $token = '';

    public function insert()
    {
        $columns = implode(",", $this->info);
        $query = ("INSERT INTO " . $this->_table . "($columns) 
            VALUES (:ST_NAME_USR, :ST_EMAIL_USR, :ST_PASSWORD_USR, :ST_TOKEN_USR)");

        if ($this->countUser($this->email) > 0) {
            throw new Exception('Usuário já cadastrado.');
        }

        $this->_insertAndUpdate($query, true);
    }

    public function update($token = '')
    {
        $columnsToSet = [];

        if ($this->email) {
            $columnsToSet [] = " ST_EMAIL_USR = '" . $this->email . "'";
        }

        if ($this->name) {
            $columnsToSet [] = " ST_NAME_USR = '" . $this->name . "'";
        }

        if ($this->password && $this->email) {
            $columnsToSet [] = " ST_PASSWORD_USR = '" . sha1($this->email . $this->password) . "'";
        }

        if (count($columnsToSet) > 0 && !(empty($token) && empty($this->idUser))) {
            $arDataUser = (new UserModel())->getUser(false, $token);

            if (count($arDataUser) > 0 && $arDataUser['ID_USER_USR'] != $this->idUser) {
                throw new Exception('O token não pertence ao usuário informado.');
            }

            $columnsToSet = implode(",", $columnsToSet);
            $query = "UPDATE " . $this->_table . " SET $columnsToSet WHERE ST_TOKEN_USR = '$token'";
            $this->_insertAndUpdate($query);
        }
    }

    public function delete(int $idUser = 0)
    {
        if (!empty($idUser)) {
            $query = "DELETE FROM " . $this->_table . " WHERE ID_USER_USR = " . $idUser;
            $connection = new DBConnection();
            $stmt = $connection->getConnection()->prepare($query);
            return $stmt->execute();
        }
    }

    public function loginUser($data = [])
    {
        if (count($data) > 0) {
            $where = [
                "ST_EMAIL_USR = '" . $data['email'] . "'",
            ];

            $arDataUser = $this->select([], $where);

            if(count($arDataUser) > 0 && $arDataUser['ST_PASSWORD_USR']) {
                $password = sha1($data['email'] . $data['password']);
                if ($password != $arDataUser['ST_PASSWORD_USR']) {
                    return [
                        'mensagem' => 'Senha inválida.'
                    ];
                }

                return $arDataUser;
            }

            return [
                'mensagem' => 'Usuário năo encontrado.'
            ];
        }
    }

    public function getUser($idUser = 0, $token = '')
    {
        if (!empty($idUser)) {
            $where [] = "ID_USER_USR = $idUser";
        } elseif (!empty($token)) {
            $where [] = "ST_TOKEN_USR = '" . $token . "'";
        }

        return $this->select([], $where);
    }

    public function countUser($email, $token = '')
    {
        $columns = [
            'COUNT(*)'
        ];

        if (!empty($email)) {
            $where [] = "ST_EMAIL_USR = '" . $email . "'";
        }
        
        if (!empty($token)) {
            $where [] = "ST_TOKEN_USR = '" . $token . "'";
        }

        return array_shift(array_values($this->select($columns, $where)));;
    }

    private function _insertAndUpdate($query = '', $isCreate = false)
    {
        if (empty($query)) {
            return;
        }

        try {
            $connection = new DBConnection();
            $stmt = $connection->getConnection()->prepare($query);
            $stmt->bindParam(':ST_NAME_USR', $this->name, PDO::PARAM_STR);
            $stmt->bindParam(':ST_EMAIL_USR', $this->email, PDO::PARAM_STR);

            if ($isCreate) {
                $password = sha1($this->email . $this->password);
                $this->token = sha1(microtime());
                $stmt->bindParam(':ST_PASSWORD_USR', $password, PDO::PARAM_STR);
                $stmt->bindParam(':ST_TOKEN_USR', $this->token, PDO::PARAM_STR);
            }

            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erro ao salvar usuário: ", $e->getMessage());
        }
    }
}
