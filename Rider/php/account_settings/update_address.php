<?php
include '../../includes/connection.php';
include '../../includes/rider_session.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rider_id = $_SESSION['riderId']; // Fetch rider ID from the session
    $address_line_1 = $_POST['address_line_1'];
    $address_line_2 = $_POST['address_line_2'];
    $postcode = $_POST['postcode'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $country = $_POST['country'];

    $query = "UPDATE rider SET address_line_1 = ?, address_line_2 = ?, postcode = ?, city = ?, state = ?, country = ? WHERE rider_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssi", $address_line_1, $address_line_2, $postcode, $city, $state, $country, $rider_id);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Address updated successfully.';
    } else {
        $response['message'] = 'Failed to update address.';
    }

    $stmt->close();
    $conn->close();

    echo json_encode($response);
}
?>
