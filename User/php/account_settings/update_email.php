<?php
include '../../../includes/connection.php';
include '../../../includes/session.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id'];
    $email = $_POST['email'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format.';
        echo json_encode($response);
        exit;
    }

    $query = "UPDATE users SET email = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $email, $user_id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Email updated successfully.';
    } else {
        $response['message'] = 'Failed to update email.';
    }

    echo json_encode($response);
}
?>
