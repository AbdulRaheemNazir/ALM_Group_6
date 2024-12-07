<?php
// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['member_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login_member.php");
    exit();
}

// Include the database connection file
require("../databaseconnection.php");
$db = connectDatabase();

// Get the logged-in user's member ID
$member_id = $_SESSION['member_id'];

// Fetch the reserved media details for the logged-in user from the reservation table
$reserve_stmt = $db->prepare("SELECT r.reservation_date, r.pick_up_date, r.status, m.title, m.author, m.type 
                              FROM reservation r
                              JOIN media m ON r.media_id = m.media_id
                              WHERE r.member_id = :member_id");
$reserve_stmt->bindParam(':member_id', $member_id, SQLITE3_INTEGER);
$reserve_result = $reserve_stmt->execute();

// Initialize an array to store the reserved media data
$reserved_items = [];
while ($row = $reserve_result->fetchArray(SQLITE3_ASSOC)) {
    $reserved_items[] = $row;
}
?>

<style>
    .reserved-media-table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 1em;
        text-align: left;
    }

    .reserved-media-table th, .reserved-media-table td {
        padding: 12px 15px;
        border: 1px solid #ddd;
    }

    .reserved-media-table th {
        background-color: black;
        color: white;
    }

    .reserved-media-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reserved Media - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Reserved Media</h1>
                <?php require("memberstartnavbar.php"); ?>                
                <p>You have reserved <?php echo count($reserved_items); ?> item(s).</p>
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("../styles/darkmodeandreader.php"); ?>

        <!-- Reserved Media Section -->
        <div class="reserved-media-section">
            <?php if (!empty($reserved_items)): ?>
                <table class="reserved-media-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Type</th>
                            <th>Reservation Date</th>
                            <th>Pick-Up Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reserved_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td><?php echo htmlspecialchars($item['author']); ?></td>
                                <td><?php echo htmlspecialchars($item['type']); ?></td>
                                <td><?php echo htmlspecialchars($item['reservation_date']); ?></td>
                                <td><?php echo !empty($item['pick_up_date']) ? htmlspecialchars($item['pick_up_date']) : 'Pending'; ?></td>
                                <td><?php echo htmlspecialchars($item['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You have no reserved media at the moment.</p>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
