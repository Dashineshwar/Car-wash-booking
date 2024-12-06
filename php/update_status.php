<?php
// Include the database connection file
include '../includes/connection.php';

// Check if ID and status are set
if (isset($_POST['id']) && isset($_POST['status'])) {
    // Sanitize input to prevent SQL injection
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Update the status in the database
    $update_query = "UPDATE userinput SET status = '$status' WHERE id = $id";
    if ($conn->query($update_query) === TRUE) {
        echo "Status updated successfully";
    } else {
        echo "Error updating status: " . $conn->error;
    }
} else {
    echo "ID or status not set";
}

// Close the database connection
$conn->close();
?>
