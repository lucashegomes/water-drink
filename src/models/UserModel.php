<?php

require_once __DIR__ . '/../config/DBConnection.php';

class UserModel
{
    private $_table = "USER";

    public $info = [
        'ST_NAME_USR',
        'ST_EMAIL_USR',
        'ST_PASSWORD_USR',
        'ST_TOKEN_USR',
    ];

    public $email = '';
    public $name = '';
    public $password = '';
    public $token = '';

    // Necessário verificar se o email já existe
    public function create()
    {
        $columns = implode(",", $this->info);
        $query = ("INSERT INTO " . $this->_table . "($columns) 
            VALUES (:ST_NAME_USR, :ST_EMAIL_USR, :ST_PASSWORD_USR, :ST_TOKEN_USR)");

        $this->_insertAndUpdate($query, true);
    }

    public function update($idUser)
    {
        $query = "UPDATE " . $this->_table . " SET name = "; //Falta terminar

        $this->_insertAndUpdate($query);
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

    public function select($columns = [], $where = [])
    {
        $query = "SELECT * FROM " . $this->_table;

        if (count($columns) > 0) {
            $columns = implode(",", $columns);
            $query = "SELECT $columns FROM " . $this->_table;
        }

        if (count($where) > 0) {
            $where = implode(" AND ", $where);
            $query .= " WHERE ($where)";
        }

        try {
            $result = (new DBConnection())
                ->getConnection()
                ->query($query)
                ->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $result[] = "PDOException: $e";
        } catch (Exception $e) {
            $result[] = "Exception: $e";
        }

        return count($result) > 1 ? $result : $result[0];
    }

    public function loginUser($data = [])
    {
        if (count($data) > 0) {
            $where = [
                "ST_EMAIL_USR = '" . $data['email'] . "'",
                "ST_PASSWORD_USR = '" . sha1($data['email'] . $data['password']) . "'",
            ];

            return $this->select(null, $where);
        }
    }

    public function getUser(int $idUser = 0, string $token = '')
    {
        if (!(empty($idUser) && empty($token))) {
            $where = [
                "ST_TOKEN_USR = '" . $token . "'",
                "ID_USER_USR = " . $idUser,
            ];

            return $this->select(null, $where);
        }
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
            throw $e;
        }
    }
}
