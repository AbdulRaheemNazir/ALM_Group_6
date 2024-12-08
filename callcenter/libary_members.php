<?php
// Start session (if required)
session_start();

// Include database connection file
require("../databaseconnection.php");
$db = connectDatabase();

// Fetch all library members
$query = $db->prepare("SELECT * FROM library_member");
$members_result = $query->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Library Members - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Library Members</h1>
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("../styles/darkmodeandreader.php"); ?>
                 <?php require("callcentrestartnavbar.php"); ?>

        <!-- Library Members Section -->
        <div class="members-section">
            <h2>All Registered Library Members</h2>

            <!-- Display Library Members in a Table -->
            <table>
                <thead>
                    <tr>
                        <th>Member ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Address</th>
                        <th>Date of Birth</th>
                        <th>Status</th>
                        <th>Borrowed Media</th>
                        <th>Overdue Items</th>
                        <th>Fines Due</th>
                        <th>Preferences</th>
                        <th>Role</th>
                        <th>Update</th> <!-- Update link column -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch and display each member
                    while ($member = $members_result->fetchArray(SQLITE3_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($member['member_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($member['first_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($member['last_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($member['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($member['phone_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($member['address']) . "</td>";
                        echo "<td>" . htmlspecialchars($member['date_of_birth']) . "</td>";
                        echo "<td>" . htmlspecialchars($member['membership_status']) . "</td>";
                        echo "<td>" . htmlspecialchars($member['borrowed_media_count']) . "</td>";
                        echo "<td>" . htmlspecialchars($member['overdue_items_count']) . "</td>";
                        echo "<td>" . htmlspecialchars($member['fines_due']) . "</td>";
                        echo "<td>" . htmlspecialchars($member['preferences']) . "</td>";
                        echo "<td>" . htmlspecialchars($member['role']) . "</td>";
                        echo "<td><a href='update_member.php?member_id=" . $member['member_id'] . "'>Update</a></td>"; // Update link
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
