<?php

require_once __DIR__ . '/../config/DBConnection.php';

abstract class Model
{

    public final function __construct()
    {
        if (empty($this->_table)) {
            throw new LogicException(get_class($this) . ' não possui a propriedade $_table declarada.');
        }
    }

    public function select($columns = [], $where = [], $join = [], $groupBy = [], $orderBy = [], $page = null)
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
            $query .= " ORDER BY $orderBy";
        }

        if (!empty($page)) {
            $query .= " LIMIT 4 OFFSET $page";
        }

        try {
            $result = ($this->getDbConnection())->query($query)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $result[] = "PDOException: $e";
        } catch (Exception $e) {
            $result[] = "Exception: $e";
        }

        return $result;
    }

    protected function getDbConnection()
    {
        return (new DBConnection())->getConnection();
    }
}
