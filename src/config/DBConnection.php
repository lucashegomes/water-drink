<?php

class DBConnection
{
    private $_host = "localhost";
    private $_username = "waterdrinken";
    private $_password = "waterdrinken";
    private $_database = "waterdrinken";
    private $_connection = null;

    public function getConnection()
    {
        try {
            $this->_connection = new PDO(
                "mysql:host=" . $this->_host . ";dbname=" . $this->_database,
                $this->_username,
                $this->_password
            );
            $this->_connection->exec("set names utf8");
        } catch (PDOException $e) {
            echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
        }

        return $this->_connection;
    }
}
