<?php
include '../../../includes/connection.php';
include '../../../includes/session.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id'];
    $phone_no = $_POST['phone_no'];

    if (!preg_match('/^[0-9]{10,15}$/', $phone_no)) {
        $response['message'] = 'Invalid phone number format. Must be between 10 to 15 digits.';
        echo json_encode($response);
        exit;
    }

    $query = "UPDATE users SET phone_no = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $phone_no, $user_id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Phone number updated successfully.';
    } else {
        $response['message'] = 'Failed to update phone number.';
    }

    echo json_encode($response);
}
?>
