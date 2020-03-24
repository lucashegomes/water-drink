<?php

require_once __DIR__ . '/../config/DBConnection.php';

abstract class Model
{

    /**
     * Check if table name was especified
     */
    public final function __construct()
    {
        if (empty($this->_table)) {
            throw new LogicException(get_class($this) . ' não possui a propriedade $_table declarada.');
        }
    }

    /**
     * Statement that defines the structure to select on tables 
     *
     * @param array $columns Query columns to return
     * @param array $where Query conditions to filter
     * @param array $join Query table joins
     * @param array $groupBy Query groupings
     * @param array $orderBy Query orderings
     * @param int $page Query pagination
     * @return array
     */
    public function select(
        array $columns = [],
        array $where = [],
        array $join = [],
        array $groupBy = [],
        array $orderBy = [],
        $page = null
    ) {
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
            $connection = new DBConnection();
            $result = $connection->getConnection()->query($query)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $result[] = "PDOException: $e";
        } catch (Exception $e) {
            $result[] = "Exception: $e";
        }

        return $result;
    }

}
