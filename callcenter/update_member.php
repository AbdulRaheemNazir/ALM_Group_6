<?php
// Start session (if required)
session_start();

// Include database connection
require("../databaseconnection.php");
$db = connectDatabase();

// Get the member_id from the URL
$member_id = $_GET['member_id'] ?? null;

// Initialize a message variable
$message = "";

// If form is submitted, update the member's details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the updated form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $membership_status = $_POST['membership_status'];
    $borrowed_media_count = $_POST['borrowed_media_count'];
    $overdue_items_count = $_POST['overdue_items_count'];
    $fines_due = $_POST['fines_due'];
    $preferences = $_POST['preferences'];
    $role = $_POST['role'];

    // Prepare and execute update query
    $update_query = $db->prepare("
        UPDATE library_member
        SET first_name = :first_name,
            last_name = :last_name,
            email = :email,
            phone_number = :phone_number,
            address = :address,
            date_of_birth = :dob,
            membership_status = :membership_status,
            borrowed_media_count = :borrowed_media_count,
            overdue_items_count = :overdue_items_count,
            fines_due = :fines_due,
            preferences = :preferences,
            role = :role
        WHERE member_id = :member_id
    ");

    // Bind parameters
    $update_query->bindParam(':first_name', $first_name, SQLITE3_TEXT);
    $update_query->bindParam(':last_name', $last_name, SQLITE3_TEXT);
    $update_query->bindParam(':email', $email, SQLITE3_TEXT);
    $update_query->bindParam(':phone_number', $phone_number, SQLITE3_TEXT);
    $update_query->bindParam(':address', $address, SQLITE3_TEXT);
    $update_query->bindParam(':dob', $dob, SQLITE3_TEXT);
    $update_query->bindParam(':membership_status', $membership_status, SQLITE3_TEXT);
    $update_query->bindParam(':borrowed_media_count', $borrowed_media_count, SQLITE3_INTEGER);
    $update_query->bindParam(':overdue_items_count', $overdue_items_count, SQLITE3_INTEGER);
    $update_query->bindParam(':fines_due', $fines_due, SQLITE3_FLOAT);
    $update_query->bindParam(':preferences', $preferences, SQLITE3_TEXT);
    $update_query->bindParam(':role', $role, SQLITE3_TEXT);
    $update_query->bindParam(':member_id', $member_id, SQLITE3_INTEGER);

    // Execute the query
    if ($update_query->execute()) {
        $message = "Member details updated successfully.";
    } else {
        $message = "Failed to update member details.";
    }
}

// Fetch member details based on member_id
if ($member_id) {
    $query = $db->prepare("SELECT * FROM library_member WHERE member_id = :member_id");
    $query->bindParam(':member_id', $member_id, SQLITE3_INTEGER);
    $result = $query->execute();
    $member = $result->fetchArray(SQLITE3_ASSOC);

    if (!$member) {
        echo "No member found with this ID.";
        exit();
    }
} else {
    echo "No member ID provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Update Member - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Update Member</h1>
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("../styles/darkmodeandreader.php"); ?>

        <!-- Update Member Form -->
        <div class="update-member-section">
            <h2>Edit Member Details</h2>

            <!-- Display success or error message -->
            <?php if ($message): ?>
                <p style="color: green;"><?php echo $message; ?></p>
            <?php endif; ?>

            <form action="update_member.php?member_id=<?php echo $member_id; ?>" method="post">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($member['first_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($member['last_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" name="phone_number" value="<?php echo htmlspecialchars($member['phone_number']); ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" name="address" value="<?php echo htmlspecialchars($member['address']); ?>">
                </div>
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" name="dob" value="<?php echo htmlspecialchars($member['date_of_birth']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="membership_status">Membership Status</label>
                    <input type="text" name="membership_status" value="<?php echo htmlspecialchars($member['membership_status']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="borrowed_media_count">Borrowed Media Count</label>
                    <input type="number" name="borrowed_media_count" value="<?php echo htmlspecialchars($member['borrowed_media_count']); ?>">
                </div>
                <div class="form-group">
                    <label for="overdue_items_count">Overdue Items Count</label>
                    <input type="number" name="overdue_items_count" value="<?php echo htmlspecialchars($member['overdue_items_count']); ?>">
                </div>
                <div class="form-group">
                    <label for="fines_due">Fines Due</label>
                    <input type="text" name="fines_due" value="<?php echo htmlspecialchars($member['fines_due']); ?>">
                </div>
                <div class="form-group">
                    <label for="preferences">Preferences</label>
                    <input type="text" name="preferences" value="<?php echo htmlspecialchars($member['preferences']); ?>">
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <input type="text" name="role" value="<?php echo htmlspecialchars($member['role']); ?>" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="search-button">Update Member</button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
