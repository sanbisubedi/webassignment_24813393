<?php
// Establishes a PDO connection to the MySQL database 'assignment1' with error handling.
function getDbConnection() {
    // try catch for error handling
    try {
        $dsn = 'mysql:host=mysql;dbname=assignment1;charset=utf8';
        $username = 'v.je';
        $password = 'v.je';
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>