<?php

class Connection
{
    private $dbh;

    public function __construct()
    {
        try {
            // Configure the PDO connection
            $this->dbh = new PDO(
                'mysql:host=localhost;port=3306;dbname=secondhand_market',
<<<<<<< HEAD
                'root', // Change if needed
                '1477'// Change if needed
=======
                'root',
                '1477'
>>>>>>> 0ab60871850137004719d39abbeefb33b0010bfa
            );
            // Set the PDO error mode to exception
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Log the error
            error_log($e->getMessage(), 3, 'error_log.txt');
            die('Could not connect to the database. Please try again later.');
        }
    }

    public function getConnection()
    {
        return $this->dbh;
    }
}
