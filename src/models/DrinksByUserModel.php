<?php

require_once __DIR__ . '/../config/DBConnection.php';

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

    public function delete(int $idUser = 0)
    {
        if (!empty($idUser)) {
            $query = "DELETE FROM " . $this->_table . " WHERE ID_USER_USR = " . $idUser;
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
        }
    }

    public function select($columns = [], $where = [], $join = [], $groupBy = [], $orderBy = [], $isOrderDesc = false)
    {
        $query = "SELECT * FROM " . $this->_table;

        if (count($columns) > 0) {
            $columns = implode(",", $columns);
            $query = "SELECT $columns FROM " . $this->_table;
        }

        if (count($join) > 0) {
            $join = implode(" LEFT JOIN ", $join);
            $query .= " LEFT JOIN $join ";
        }

        if (count($where) > 0) {
            $where = implode(" AND ", $where);
            $query .= " WHERE ($where) ";
        }

        if (count($groupBy) > 0) {
            $groupBy = implode(",", $groupBy);
            $query .= " GROUP BY $groupBy";
        }

        if (count($orderBy) > 0) {
            $orderBy = implode(",", $orderBy);
            $query .= " ORDER BY $orderBy" . ($isOrderDesc ? " DESC " : " ASC ");
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

        return $result;
    }

    public function countDrink(int $idUser)
    {
        if (!empty($idUser)) {
            return $this->select(["COUNT(*)"], ["ID_USER_USR = $idUser"])[0];
        }
    }

    public function getRank()
    {
        $columns = [
            "ST_NAME_USR AS name",
            "SUM(NM_MLDRINKED_DKS) AS drinked_ml"
        ];

        $join = ["USER USING(ID_USER_USR)"];
        $groupBy = ["ID_USER_USR"];
        $orderBy = ["drinked_ml"];

        return $this->select($columns, [], $join, $groupBy, $orderBy, true);
    }

    public function getHistory(int $idUser)
    {
        $columns = [
            'DATE_FORMAT(DT_REGISTER_DKS, "%d/%m/%Y %H:%i:%s") AS date',
            "NM_MLDRINKED_DKS AS drinked_ml"
        ];

        $where = ["ID_USER_USR = $idUser"];

        return $this->select($columns, $where);
    }
}
