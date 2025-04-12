<?php

class Connection
{
    private $dbh;

    public function __construct()
    {
        try {
            // Establish the PDO connection
            $this->dbh = new PDO(
                'mysql:host=localhost;port=3306;dbname=challenge_3_database',
                'root',
                '123'
            );
            // Set the PDO error mode to exception
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Log the error and provide a user-friendly message
            error_log($e->getMessage(), 3, 'error_log.txt');
            die('Could not connect to the database. Please try again later.');
        }
    }

    public function getConnection()
    {
        return $this->dbh;
    }
}
