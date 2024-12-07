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

// Fetch the borrowed media count from the library_member table
$stmt = $db->prepare("SELECT borrowed_media_count FROM library_member WHERE member_id = :member_id");
$stmt->bindParam(':member_id', $member_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$member = $result->fetchArray(SQLITE3_ASSOC);

// Fetch the borrowing details for the logged-in user from the borrowing table
$borrow_stmt = $db->prepare("SELECT b.borrow_date, b.due_date, b.return_date, b.overdue_status, m.title, m.author, m.type 
                            FROM borrowing b
                            JOIN media m ON b.media_id = m.media_id
                            WHERE b.member_id = :member_id");
$borrow_stmt->bindParam(':member_id', $member_id, SQLITE3_INTEGER);
$borrow_result = $borrow_stmt->execute();

// Initialize an array to store the borrowing data
$borrowed_items = [];
while ($row = $borrow_result->fetchArray(SQLITE3_ASSOC)) {
    $borrowed_items[] = $row;
}

?>

<style>
    .borrowed-media-table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
    font-size: 1em;
    text-align: left;
}

.borrowed-media-table th, .borrowed-media-table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
}

.borrowed-media-table th {
    background-color: black;
    color: white;
}

.borrowed-media-table tr:nth-child(even) {
    background-color: #f2f2f2;
}



</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Borrowed Media - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Borrowed Media</h1>

        <?php require("memberstartnavbar.php"); ?>                
                <p>You have borrowed <?php echo $member['borrowed_media_count']; ?> item(s).</p>
            </div>
        </header>

        <!-- Navigation Menu -->

        <?php require("../styles/darkmodeandreader.php"); ?>

        <!-- Borrowed Media Section -->
        <div class="borrowed-media-section">
            <?php if (!empty($borrowed_items)): ?>
                <table class="borrowed-media-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Type</th>
                            <th>Borrow Date</th>
                            <th>Due Date</th>
                            <th>Return Date</th>
                            <th>Overdue Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($borrowed_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['title']); ?></td>
                                <td><?php echo htmlspecialchars($item['author']); ?></td>
                                <td><?php echo htmlspecialchars($item['type']); ?></td>
                                <td><?php echo htmlspecialchars($item['borrow_date']); ?></td>
                                <td><?php echo htmlspecialchars($item['due_date']); ?></td>
                                <td><?php echo !empty($item['return_date']) ? htmlspecialchars($item['return_date']) : 'Not Returned'; ?></td>
                                <td><?php echo $item['overdue_status'] ? 'Overdue' : 'On Time'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You have no borrowed media at the moment.</p>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
