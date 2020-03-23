<?php

class DrinksByUserModel
{
    private $_table = "DRINKS_BY_USER";

    public $idUser = '';
    public $mlDrinked = '';

    public $info = [
        'ID_USER_USR',
        'NM_MLDRINKED_DKS',
    ];

    public function insert()
    {
        try {
            $columns = implode(',', $this->info);
            $query = "INSERT INTO $this->_table ($columns) VALUES (:ID_USER_USR, :NM_MLDRINKED_DKS)";

            $stmt = (new DBConnection())->getConnection()->prepare($query);
            $stmt->bindParam(':ID_USER_USR', $this->idUser, PDO::PARAM_INT);
            $stmt->bindParam(':NM_MLDRINKED_DKS', $this->mlDrinked, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function update($idUser)
    {
        $query = "UPDATE " . $this->_table . " SET name = ";

        $this->_insertAndUpdate($query);
    }

    public function delete(int $idUser = 0)
    {
        if (!empty($idUser)) {
            $query = "DELETE FROM " . $this->_table . " WHERE ID_USER_USR = " . $idUser;
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
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
                ->fetch();
        } catch (PDOException $e) {
            $result[] = "PDOException: $e";
        } catch (Exception $e) {
            $result[] = "Exception: $e";
        }

        return $result;
    }

    public function countDrink(int $idUser)
    {
        if (!empty($idUser)) {
            return $this->select(["COUNT(*)"], ["ID_USER_USR = $idUser"])[0];
        }
    }

    private function _insertAndUpdate($query = '', $isCreate = false)
    {
        if (empty($query)) {
            return;
        }

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $this->email, PDO::PARAM_STR);

            if ($isCreate) {
                $stmt->bindParam(':password', sha1($this->name . $this->email . $this->password), PDO::PARAM_STR);
                $stmt->bindParam(':token', sha1(microtime()), PDO::PARAM_STR);
            }

            $stmt->execute();
        } catch (PDOException $e) {
            throw $e;
        }
    }
}
