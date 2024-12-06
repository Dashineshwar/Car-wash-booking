<?php
include '../../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rider_id = isset($_POST['rider_id']) ? mysqli_real_escape_string($conn, $_POST['rider_id']) : '';

    if (!empty($rider_id)) {
        $query = "DELETE FROM rider WHERE rider_id = '$rider_id'";
        if (mysqli_query($conn, $query)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete rider']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Rider ID is required']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

mysqli_close($conn);
?>
