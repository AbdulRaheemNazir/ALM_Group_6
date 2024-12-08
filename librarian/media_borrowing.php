
<?php
// Start the session and check if the librarian is logged in
session_start();
if (!isset($_SESSION['librarian_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login_librarian.php");
    exit();
}

// Include the database connection file
require("../databaseconnection.php");
$db = connectDatabase();

// Handle adding a new borrowing entry
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_borrowing'])) {
    $member_id = $_POST['member_id'];
    $media_id = $_POST['media_id'];
    $borrow_date = date('Y-m-d H:i:s'); // Today's date
    $due_date = date('Y-m-d H:i:s', strtotime('+1 week')); // Due date, 1 week from today
    
    // Insert a new borrowing entry
    $insert_stmt = $db->prepare("INSERT INTO borrowing (member_id, media_id, borrow_date, due_date) 
                                 VALUES (:member_id, :media_id, :borrow_date, :due_date)");
    $insert_stmt->bindParam(':member_id', $member_id, SQLITE3_INTEGER);
    $insert_stmt->bindParam(':media_id', $media_id, SQLITE3_INTEGER);
    $insert_stmt->bindParam(':borrow_date', $borrow_date, SQLITE3_TEXT);
    $insert_stmt->bindParam(':due_date', $due_date, SQLITE3_TEXT);
    $insert_stmt->execute();
}

// Handle updating the return date and calculating overdue/fine
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_return'])) {
    $borrowing_id = $_POST['borrowing_id'];
    $return_date = $_POST['return_date'];

    // Fetch borrowing details for the given borrowing_id
    $borrow_stmt = $db->prepare("SELECT due_date FROM borrowing WHERE borrowing_id = :borrowing_id");
    $borrow_stmt->bindParam(':borrowing_id', $borrowing_id, SQLITE3_INTEGER);
    $result = $borrow_stmt->execute();
    
    // Check if any result is returned
    $row = $result->fetchArray(SQLITE3_ASSOC);
    if ($row) {
        $due_date = $row['due_date'];

        // Calculate overdue days and fines
        $overdue_days = max(0, (strtotime($return_date) - strtotime($due_date)) / (60 * 60 * 24)); // Days late
        $fine = $overdue_days * 1; // $1 fine per day overdue

        // Update the return date, overdue status, and fine
        $update_stmt = $db->prepare("UPDATE borrowing 
                                     SET return_date = :return_date, 
                                         overdue_status = :overdue_status, 
                                         fine = :fine 
                                     WHERE borrowing_id = :borrowing_id");
        $update_stmt->bindParam(':return_date', $return_date, SQLITE3_TEXT);
        $overdue_status = $overdue_days > 0 ? 1 : 0;
        $update_stmt->bindParam(':overdue_status', $overdue_status, SQLITE3_INTEGER);
        $update_stmt->bindParam(':fine', $fine, SQLITE3_FLOAT);
        $update_stmt->bindParam(':borrowing_id', $borrowing_id, SQLITE3_INTEGER);
        $update_stmt->execute();
    } else {
        // Handle the case when no borrowing record is found
        echo "No borrowing record found for the given ID.";
    }
}


// Fetch all borrowing records with member ID and media ID
$sql = "SELECT borrowing.borrowing_id, library_member.member_id, media.media_id, borrowing.borrow_date, borrowing.due_date, borrowing.return_date, borrowing.overdue_status, borrowing.fine 
        FROM borrowing
        JOIN library_member ON borrowing.member_id = library_member.member_id
        JOIN media ON borrowing.media_id = media.media_id";
        
$stmt = $db->prepare($sql);
$result = $stmt->execute();

// Initialize the borrowing array
$borrowings = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $borrowings[] = $row;
}
?>


<!-- Borrowing Table -->
<div class="container">
        <header>
            <div class="title">
                <h1>Media Borrowing</h1>
            </div>
        </header>
    <h2>Borrowing Records</h2>
    <?php require("../styles/darkmodeandreader.php"); ?>
    <?php require("librarianstartnavbar.php"); ?>
    <table class="borrow-table">
        <thead>
            <tr>
                <th>Borrowing ID</th>
                <th>Member ID</th>
                <th>Media ID</th>
                <th>Borrow Date</th>
                <th>Due Date</th>
                <th>Return Date</th>
                <th>Overdue Status</th>
                <th>Fine</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($borrowings as $borrow): ?>
                <tr>
                    <td><?php echo htmlspecialchars($borrow['borrowing_id']); ?></td>
                    <td><?php echo htmlspecialchars($borrow['member_id']); ?></td>
                    <td><?php echo htmlspecialchars($borrow['media_id']); ?></td>
                    <td><?php echo htmlspecialchars($borrow['borrow_date']); ?></td>
                    <td><?php echo htmlspecialchars($borrow['due_date']); ?></td>
                    <td><?php echo !empty($borrow['return_date']) ? htmlspecialchars($borrow['return_date']) : 'Not Returned'; ?></td>
                    <td><?php echo $borrow['overdue_status'] ? 'Overdue' : 'On Time'; ?></td>
                    <td><?php echo htmlspecialchars($borrow['fine']); ?></td>
                    <td>
                        <form action="media_borrowing.php" method="post">
                            <input type="hidden" name="borrowing_id" value="<?php echo $borrow['borrowing_id']; ?>">
                            <label for="return_date">Return Date:</label>
                            <input type="date" name="return_date" required>
                            <button type="submit" name="update_return">Update Return</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>


<!-- Footer -->
<?php require("../footer.php"); ?>


<!-- Add the CSS -->
<style>
    /* Center the container and table */
    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        min-height: 100vh;
        padding: 20px;
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    .borrow-table {
        width: 80%;
        margin: 0 auto;
        border-collapse: collapse;
    }

    .borrow-table th, .borrow-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }

    .borrow-table th {
        background-color: #f2f2f2;
    }

    .borrow-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .borrow-table tr:hover {
        background-color: #ddd;
    }

    .borrow-table th {
        padding-top: 12px;
        padding-bottom: 12px;
        background-color: #4CAF50;
        color: white;
    }

    /* Style form elements inside the table */
    form {
        display: inline;
    }

    input[type="date"] {
        padding: 5px;
    }

    button[type="submit"] {
        padding: 5px 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
    }

    button[type="submit"]:hover {
        background-color: #45a049;
    }
</style>
