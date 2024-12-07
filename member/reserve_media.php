<?php
session_start(); // Start the session
require("../databaseconnection.php");

// Check if the member is logged in and the media_id is provided
if (isset($_SESSION['member_id']) && isset($_POST['media_id'])) {
    $db = connectDatabase();
    
    $member_id = $_SESSION['member_id']; // Get the logged-in member's ID
    $media_id = $_POST['media_id'];

    // Check if the member has already reserved the item
    $checkReservationSQL = "SELECT * FROM reservation WHERE media_id = :media_id AND member_id = :member_id";
    $checkStmt = $db->prepare($checkReservationSQL);
    $checkStmt->bindValue(':media_id', $media_id, SQLITE3_INTEGER);
    $checkStmt->bindValue(':member_id', $member_id, SQLITE3_INTEGER);
    $checkResult = $checkStmt->execute();

    if ($checkResult->fetchArray(SQLITE3_ASSOC)) {
        // If a reservation exists, redirect or show a message
        echo "You have already reserved this item.";
    } else {
        // Insert new reservation
        $insertReservationSQL = "INSERT INTO reservation (member_id, media_id, reservation_date, reservation_expiry) VALUES (:member_id, :media_id, CURRENT_TIMESTAMP, DATE('now', '+7 days'))";
        $insertStmt = $db->prepare($insertReservationSQL);
        $insertStmt->bindValue(':member_id', $member_id, SQLITE3_INTEGER);
        $insertStmt->bindValue(':media_id', $media_id, SQLITE3_INTEGER);

        if ($insertStmt->execute()) {
            // Reservation successful
        } else {
            // Handle the error
            echo "Failed to reserve media item.";
        }
    }
} else {
    // Redirect to login or show an error if member_id is not set
    echo "You need to log in to reserve media.";
}
?>
