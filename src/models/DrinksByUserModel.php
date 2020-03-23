<?php

require_once __DIR__ . '/../models/Model.php';

class DrinksByUserModel extends Model
{
    protected $_table = "DRINKS_BY_USER";

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

            $stmt = $this->getDbConnection()->prepare($query);
            $stmt->bindParam(':ID_USER_USR', $this->idUser, PDO::PARAM_INT);
            $stmt->bindParam(':NM_MLDRINKED_DKS', $this->mlDrinked, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erro ao inserir quantidade de água bebida: ", $e);
        }
    }

    public function countDrink(int $idUser)
    {
        if (!empty($idUser)) {
            return $this->select(["COUNT(*)"], ["ID_USER_USR = $idUser"])[0];
        }
    }

    public function getRankingCurrentDate()
    {
        $columns = [
            "ST_NAME_USR AS name",
            "SUM(NM_MLDRINKED_DKS) AS drinked_ml"
        ];

        $where = ["DT_REGISTER_DKS = DATE(NOW())"];
        $join = ["USER USING(ID_USER_USR)"];
        $groupBy = ["ID_USER_USR"];
        $orderBy = ["drinked_ml DESC"];

        return $this->select($columns, $where, $join, $groupBy, $orderBy);
    }

    public function getHistoryByUser(int $idUser)
    {
        $columns = [
            'DATE_FORMAT(DT_REGISTER_DKS, "%d/%m/%Y %H:%i:%s") AS date',
            "NM_MLDRINKED_DKS AS drinked_ml"
        ];

        $where = ["ID_USER_USR = $idUser"];

        return $this->select($columns, $where);
    }
}
