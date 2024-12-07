<?php
function connectDatabase() {
    $dbPath = 'C:\xampp\alm_database\alm_database.db';
    try {
        $db = new SQLite3($dbPath);
        $db->busyTimeout(5000);  // Set busy timeout to 5 seconds
        return $db;
    } catch (Exception $e) {
        echo "Failed to connect to the database: " . $e->getMessage();
        return null;
    }
}
//
// Mine and Elegohsa
// $dbPath = 'C:\xampp\alm_database\alm_database.db';

//Kieren
// $dbPath = 'C:\xampp2\alm_database\alm_database.db';




?>

