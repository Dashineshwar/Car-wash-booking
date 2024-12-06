<?php
include '../../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rider_id = isset($_POST['rider_id']) ? mysqli_real_escape_string($conn, $_POST['rider_id']) : '';
    $username = isset($_POST['username']) ? mysqli_real_escape_string($conn, $_POST['username']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $wallet = isset($_POST['wallet']) ? mysqli_real_escape_string($conn, $_POST['wallet']) : '';
    $phone_no = isset($_POST['phone_no']) ? mysqli_real_escape_string($conn, $_POST['phone_no']) : '';
    $current_location = isset($_POST['current_location']) ? mysqli_real_escape_string($conn, $_POST['current_location']) : '';
    $serving_postcode = isset($_POST['serving_postcode']) ? mysqli_real_escape_string($conn, $_POST['serving_postcode']) : '';
    $available = isset($_POST['available']) ? mysqli_real_escape_string($conn, $_POST['available']) : '';

    if (!empty($rider_id)) {
        $query = "UPDATE rider SET 
                  username = '$username', 
                  email = '$email', 
                  wallet = '$wallet', 
                  phone_no = '$phone_no', 
                  current_location = '$current_location', 
                  serving_postcode = '$serving_postcode', 
                  available = '$available' 
                  WHERE rider_id = '$rider_id'";

        if (mysqli_query($conn, $query)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update rider']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Rider ID is required']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

mysqli_close($conn);
?>
