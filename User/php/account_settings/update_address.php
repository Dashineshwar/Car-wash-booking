<?php
include '../../../includes/connection.php';
include '../../../includes/session.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id'];
    $address_line_1 = $_POST['address_line_1'];
    $address_line_2 = $_POST['address_line_2'];
    $postcode = $_POST['postcode'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $country = $_POST['country'];

    $query = "UPDATE users SET address_line_1 = ?, address_line_2 = ?, postcode = ?, city = ?, state = ?, country = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssi", $address_line_1, $address_line_2, $postcode, $city, $state, $country, $user_id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Address updated successfully.';
    } else {
        $response['message'] = 'Failed to update address.';
    }

    echo json_encode($response);
}
?>
