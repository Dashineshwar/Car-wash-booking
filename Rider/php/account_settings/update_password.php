<?php
include '../../../includes/connection.php';
include '../../../includes/rider_session.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rider_id = $_SESSION['riderId']; // Fetch rider ID from the session
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the new passwords match
    if ($new_password !== $confirm_password) {
        $response['message'] = 'New passwords do not match.';
        echo json_encode($response);
        exit;
    }

    // Check old password
    $query = "SELECT password FROM rider WHERE rider_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $rider_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $response['message'] = 'Rider not found.';
        echo json_encode($response);
        exit;
    }

    $rider = $result->fetch_assoc();

    if (!password_verify($old_password, $rider['password'])) {
        $response['message'] = 'Old password is incorrect.';
        echo json_encode($response);
        exit;
    }

    // Hash the new password
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the password
    $update_query = "UPDATE rider SET password = ? WHERE rider_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $new_hashed_password, $rider_id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Password updated successfully.';
    } else {
        $response['message'] = 'Failed to update password.';
    }

    $stmt->close();
    $conn->close();

    echo json_encode($response);
}
?>
