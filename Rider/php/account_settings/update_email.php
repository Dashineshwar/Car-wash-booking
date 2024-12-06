<?php
include '../../includes/connection.php';
include '../../includes/rider_session.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rider_id = $_SESSION['riderId']; // Fetch rider ID from the session
    $email = $_POST['email'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format.';
        echo json_encode($response);
        exit;
    }

    // Update email in the database
    $query = "UPDATE rider SET email = ? WHERE rider_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $email, $rider_id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Email updated successfully.';
    } else {
        $response['message'] = 'Failed to update email.';
    }

    $stmt->close();
    $conn->close();

    echo json_encode($response);
}
?>
