<?php
session_start();
require("../databaseconnection.php");
$db = connectDatabase();

if (!isset($_SESSION['first_name'])) {
    header("Location: login_librarian.php");
    exit();
}

$first_name = $_SESSION['first_name'];

// Fetch reservations along with media title
$query = "
    SELECT reservation.*, media.title AS media_title 
    FROM reservation
    JOIN media ON reservation.media_id = media.media_id";
$reservations_result = $db->query($query);

// Update pick-up status if the request is made via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reservation_id']) && isset($_POST['status'])) {
    $reservation_id = $_POST['reservation_id'];
    $status = $_POST['status'];

    // Update the reservation status
    $stmt = $db->prepare("UPDATE reservation SET status = :status WHERE reservation_id = :reservation_id");
    $stmt->bindParam(':status', $status, SQLITE3_TEXT);
    $stmt->bindParam(':reservation_id', $reservation_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Status updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Error updating status']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery for AJAX -->
</head>
<body>
    <div class="container">
        <header>
            <div class="title">
                <h1>Manage Reservations</h1>
            </div>
        </header>

        <?php require("librarianstartnavbar.php"); ?>
        <?php require("../styles/darkmodeandreader.php"); ?>

        <div class="dashboard-section">
            <h2>Reservations</h2>
            <!-- Success/Failure message display -->
            <div id="message" style="display: none;"></div>

            <table>
                <thead>
                    <tr>
                        <th>Member ID</th>
                        <th>Media Title</th>
                        <th>Reservation Date</th>
                        <th>Pick-Up Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $reservations_result->fetchArray(SQLITE3_ASSOC)): ?>
                    <tr>
                        <td><?php echo $row['member_id']; ?></td>
                        <td><?php echo $row['media_title']; ?></td>
                        <td><?php echo $row['reservation_date']; ?></td>
                        <td>
                            <input type="checkbox" class="pickup-checkbox" data-id="<?php echo $row['reservation_id']; ?>" <?php echo ($row['status'] == 'Picked Up') ? 'checked' : ''; ?>> 
                            <?php echo ($row['status'] == 'Picked Up') ? 'Picked Up' : 'Reserved'; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php require("../footer.php"); ?>

        <!-- AJAX script to handle the pick-up status -->
        <script>
            $(document).ready(function() {
                $('.pickup-checkbox').on('change', function() {
                    var reservationId = $(this).data('id');
                    var status = $(this).is(':checked') ? 'Picked Up' : 'Reserved';

                    $.ajax({
                        url: 'reservations.php',  // Send the request to the same page
                        type: 'POST',
                        data: {
                            reservation_id: reservationId,
                            status: status
                        },
                        success: function(response) {
                            $('#message').show().html('<p style="color: green;">Status updated successfully!</p>').delay(3000).fadeOut();
                        },
                        error: function() {
                            $('#message').show().html('<p style="color: red;">Error updating status!</p>').delay(3000).fadeOut();
                        }
                    });
                });
            });
        </script>
    </div>
</body>
</html>
