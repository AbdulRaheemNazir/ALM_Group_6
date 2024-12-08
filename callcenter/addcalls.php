<?php
// Start session to access logged-in user's email
session_start();

// Include the database connection
require("../databaseconnection.php");
$db = connectDatabase();

// Get the logged-in user's email (assuming it exists in session)
$email = $_SESSION['email'] ?? null;  // Using null coalescing to avoid undefined error

if ($email) {
    // Get the operator's ID using their email
    $query = $db->prepare("SELECT operator_id FROM call_centre_operator WHERE email = :email");
    $query->bindParam(':email', $email, SQLITE3_TEXT);
    $result = $query->execute();
    $operator = $result->fetchArray(SQLITE3_ASSOC);

    if ($operator) {
        $operator_id = $operator['operator_id'];

        // Check if the form is submitted (to add a new call)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve the submitted form data
            $call_reason = $_POST['call_reason'] ?? null;
            $call_notes = $_POST['call_notes'] ?? null;
            $follow_up_required = $_POST['follow_up_required'] ?? null;

            // Automatically capture today's date
            $call_date = date('Y-m-d'); // Capture today's date

            // Validate that the required fields are provided
            if ($call_reason && $follow_up_required !== null) {
                // Convert the follow_up_required to a boolean integer (1 for 'Yes', 0 for 'No')
                $follow_up_required = ($follow_up_required == 1) ? 1 : 0;

                // Prepare the SQL query to insert the new call
                $insert_query = $db->prepare("INSERT INTO calls (operator_id, call_reason, call_notes, follow_up_required, call_date) VALUES (:operator_id, :call_reason, :call_notes, :follow_up_required, :call_date)");
                $insert_query->bindParam(':operator_id', $operator_id, SQLITE3_INTEGER);
                $insert_query->bindParam(':call_reason', $call_reason, SQLITE3_TEXT);
                $insert_query->bindParam(':call_notes', $call_notes, SQLITE3_TEXT);
                $insert_query->bindParam(':follow_up_required', $follow_up_required, SQLITE3_INTEGER);
                $insert_query->bindParam(':call_date', $call_date, SQLITE3_TEXT); // Use captured today's date

                // Execute the insert query
                if ($insert_query->execute()) {
                    $success_message = "New call added successfully!";
                } else {
                    $error_message = "Error: Could not add the call.";
                }
            } else {
                $error_message = "Error: Please fill in all required fields.";
            }
        }
    }
} else {
    // Handle the case when email is not set or user is not recognized
    echo "Error: No operator found with this email.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add New Call - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Add New Call</h1>
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("callcentrestartnavbar.php"); ?>
        <?php require("../styles/darkmodeandreader.php"); ?>

        <!-- Feedback Section -->
        <div class="message-section">
            <?php
            if (isset($success_message)) {
                echo "<p>$success_message</p>";
            } elseif (isset($error_message)) {
                echo "<p>$error_message</p>";
            }
            ?>
        </div>

        <!-- Add Call Form Section -->
        <div class="add-call-section">
            <h2>Log a New Call</h2>

            <form method="post" action="">
                <div>
                    <label for="call_reason">Call Reason (required):</label>
                    <input type="text" id="call_reason" name="call_reason" required>
                </div>
                <div>
                    <label for="call_notes">Call Notes:</label>
                    <textarea id="call_notes" name="call_notes"></textarea>
                </div>
                <div>
                    <label for="follow_up_required">Follow-Up Required:</label>
                    <select id="follow_up_required" name="follow_up_required" required>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <!-- No need to show the date field as it's automatically captured -->
                <div>
                    <button type="submit">Add Call</button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
