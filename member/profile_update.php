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

// Initialize error and success messages
$error_message = "";
$success_message = "";

// Get the logged-in user's member ID
$member_id = $_SESSION['member_id'];

// Fetch the current user data from the database
$stmt = $db->prepare("SELECT * FROM library_member WHERE member_id = :member_id");
$stmt->bindParam(':member_id', $member_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $preferences = $_POST['preferences'];
    $password = $_POST['password'];
    
    // Hash the password if it is provided
    $password_hash = $password ? password_hash($password, PASSWORD_DEFAULT) : $user['password_hash'];
    
    // Prepare SQL query to update the user profile
    $sql = "UPDATE library_member 
            SET first_name = :first_name, last_name = :last_name, email = :email, phone_number = :phone, 
                address = :address, date_of_birth = :dob, preferences = :preferences, password_hash = :password_hash
            WHERE member_id = :member_id";
    
    $update_stmt = $db->prepare($sql);

    // Bind the parameters
    $update_stmt->bindParam(':first_name', $first_name, SQLITE3_TEXT);
    $update_stmt->bindParam(':last_name', $last_name, SQLITE3_TEXT);
    $update_stmt->bindParam(':email', $email, SQLITE3_TEXT);
    $update_stmt->bindParam(':phone', $phone, SQLITE3_TEXT);
    $update_stmt->bindParam(':address', $address, SQLITE3_TEXT);
    $update_stmt->bindParam(':dob', $dob, SQLITE3_TEXT);
    $update_stmt->bindParam(':preferences', $preferences, SQLITE3_TEXT);
    $update_stmt->bindParam(':password_hash', $password_hash, SQLITE3_TEXT);
    $update_stmt->bindParam(':member_id', $member_id, SQLITE3_INTEGER);

    // Execute the update
    if ($update_stmt->execute()) {
        $success_message = "Profile updated successfully!";
        // Update session variables if needed
        $_SESSION['first_name'] = $first_name;
    } else {
        $error_message = "Failed to update profile. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profile Update - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Update Profile</h1>
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("memberstartnavbar.php"); ?>
        <?php require("../styles/darkmodeandreader.php"); ?>

        <!-- Profile Update Section -->
        <div class="profile-update-section">

            <!-- Display error or success message -->
            <?php if (!empty($error_message)): ?>
                <p class="error" style="color: red;"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <p class="success" style="color: green;"><?php echo $success_message; ?></p>
            <?php endif; ?>

            <form action="profile_update.php" method="post">
                <!-- First Name -->
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" class="search-bar" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>

                <!-- Last Name -->
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" class="search-bar" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" class="search-bar" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <!-- Phone Number -->
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" class="search-bar" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" name="address" class="search-bar" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                </div>

                <!-- Date of Birth -->
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" name="dob" class="search-bar" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>" required>
                </div>

                <!-- Preferences -->
                <div class="form-group">
                    <label for="preferences">Preferred Communication Method</label>
                    <select name="preferences" class="search-bar" required>
                        <option value="email" <?php echo ($user['preferences'] == 'email') ? 'selected' : ''; ?>>Email</option>
                        <option value="sms" <?php echo ($user['preferences'] == 'sms') ? 'selected' : ''; ?>>SMS</option>
                    </select>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">New Password</label>
                    <input type="password" name="password" class="search-bar" placeholder="New Password">
                </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <button type="submit" class="search-button">Update Profile</button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
