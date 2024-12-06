<?php
include '../../../includes/connection.php';
include '../../../includes/session.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $response['message'] = 'New passwords do not match.';
        echo json_encode($response);
        exit;
    }

    // Check old password
    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if (!password_verify($old_password, $result['password'])) {
        $response['message'] = 'Old password is incorrect.';
        echo json_encode($response);
        exit;
    }

    // Update to new password
    $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $update_query = "UPDATE users SET password = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $new_hashed_password, $user_id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Password updated successfully.';
    } else {
        $response['message'] = 'Failed to update password.';
    }

    echo json_encode($response);
}
?>
