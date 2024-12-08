<?php
// Start session to access logged-in user's email
session_start();

// Include the database connection
require("../databaseconnection.php");
$db = connectDatabase();

// Check if a form has been submitted to update follow-up status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['call_id'])) {
    $call_id = $_POST['call_id'];
    $follow_up_required = $_POST['follow_up_required'] === '1'; // Convert to boolean

    // Prepare the SQL statement to update follow-up status
    $update_query = $db->prepare("UPDATE calls SET follow_up_required = :follow_up_required WHERE call_id = :call_id");
    $update_query->bindParam(':follow_up_required', $follow_up_required, SQLITE3_INTEGER);
    $update_query->bindParam(':call_id', $call_id, SQLITE3_INTEGER);
    
    // Execute the update
    if ($update_query->execute()) {
        echo "Follow-up status updated successfully.";
    } else {
        echo "Error updating follow-up status.";
    }
}

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

        // Fetch all calls related to this operator
        $calls_query = $db->prepare("SELECT * FROM calls WHERE operator_id = :operator_id");
        $calls_query->bindParam(':operator_id', $operator_id, SQLITE3_INTEGER);
        $calls_result = $calls_query->execute();
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
    <title>Call History - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Your Call History</h1>
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("callcentrestartnavbar.php"); ?>
        <?php require("../styles/darkmodeandreader.php"); ?>

        <!-- Call History Section -->
        <div class="call-history-section">
            <h2>All Calls Handled by You</h2>

            <!-- Display Call Data in Table -->
            <table>
                <thead>
                    <tr>
                        <th>Call Reason</th>
                        <th>Notes</th>
                        <th>Follow-Up Required</th>
                        <th>Call Date</th>
                        <th>Update Follow-Up</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($calls_result)) {
                        while ($call = $calls_result->fetchArray(SQLITE3_ASSOC)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($call['call_reason']) . "</td>";
                            echo "<td>" . htmlspecialchars($call['call_notes']) . "</td>";
                            echo "<td>" . ($call['follow_up_required'] ? 'Yes' : 'No') . "</td>";
                            echo "<td>" . htmlspecialchars($call['call_date']) . "</td>";
                            echo "<td>
                                <form method='post' action='calls.php'>
                                    <input type='hidden' name='call_id' value='" . htmlspecialchars($call['call_id']) . "' />
                                    <select name='follow_up_required'>
                                        <option value='1'" . ($call['follow_up_required'] ? ' selected' : '') . ">Yes</option>
                                        <option value='0'" . (!$call['follow_up_required'] ? ' selected' : '') . ">No</option>
                                    </select>
                                    <button type='submit'>Update</button>
                                </form>
                            </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No calls found for this operator.</td></tr>";
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
