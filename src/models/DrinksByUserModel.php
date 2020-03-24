<?php

require_once __DIR__ . '/../models/Model.php';

class DrinksByUserModel extends Model
{
    /**
     * Database table name
     *
     * @var string
     */
    protected $_table = "DRINKS_BY_USER";

    /**
     * User identity
     *
     * @var int
     */
    public $idUser = 0;

    /**
     * Miligram drinked
     *
     * @var int
     */
    public $mlDrinked = 0;

    /**
     * Database table columns
     *
     * @var array
     */
    public $info = [
        'ID_USER_USR',
        'NM_MLDRINKED_DKS',
    ];

    public function insert()
    {
        try {
            $columns = implode(',', $this->info);
            $query = "INSERT INTO $this->_table ($columns) VALUES (:ID_USER_USR, :NM_MLDRINKED_DKS)";

            $connection = new DBConnection();
            $stmt = $connection->getConnection()->prepare($query);
            $stmt->bindParam(':ID_USER_USR', $this->idUser, PDO::PARAM_INT);
            $stmt->bindParam(':NM_MLDRINKED_DKS', $this->mlDrinked, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erro ao inserir quantidade de água bebida: ", $e);
        }
    }

    /**
     * Count drink registers by user
     *
     * @param integer $idUser User identity
     * @return PDO
     */
    public function countDrink(int $idUser)
    {
        if (!empty($idUser)) {
            return $this->select(["COUNT(*)"], ["ID_USER_USR = $idUser"])[0];
        }
    }

    /**
     * Get user ranking by miligram drink
     *
     * @return PDO
     */
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

    /**
     * Get user history by id
     *
     * @param [type] $idUser User identity
     * @return PDO
     */
    public function getHistoryByUser($idUser)
    {
        $columns = [
            'DATE_FORMAT(DT_REGISTER_DKS, "%d/%m/%Y %H:%i:%s") AS date',
            "NM_MLDRINKED_DKS AS drinked_ml"
        ];

        $where = ["ID_USER_USR = $idUser"];

        return $this->select($columns, $where);
    }
}
