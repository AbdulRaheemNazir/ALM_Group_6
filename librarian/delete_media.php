<?php
session_start();
require("../databaseconnection.php");
$db = connectDatabase();

// Check if media_id is provided in the request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['media_id'])) {
    $media_id = $_POST['media_id'];

    // Prepare the delete statement
    $stmt = $db->prepare("DELETE FROM media WHERE media_id = :media_id");
    $stmt->bindParam(':media_id', $media_id, SQLITE3_INTEGER);

    // Execute the statement and check if it was successful
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "error";
}
?>
