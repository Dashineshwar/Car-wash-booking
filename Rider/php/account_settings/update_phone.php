<?php
include '../../../includes/connection.php';
include '../../../includes/rider_session.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rider_id = $_SESSION['riderId']; // Fetch rider ID from the session
    $phone_no = $_POST['phone_no'];

    // Validate phone number format
    if (!preg_match('/^[0-9]{10,15}$/', $phone_no)) {
        $response['message'] = 'Invalid phone number format. Must be between 10 to 15 digits.';
        echo json_encode($response);
        exit;
    }

    // Update phone number in the `rider` table
    $query = "UPDATE rider SET phone_no = ? WHERE rider_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $phone_no, $rider_id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Phone number updated successfully.';
    } else {
        $response['message'] = 'Failed to update phone number.';
    }

    echo json_encode($response);
}
?>
