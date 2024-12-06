<?php
include '../includes/connection.php';

$username = $_POST['username'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$city = $_POST['city'];
$vehicle_number = $_POST['vehicle_number'];

$query = "INSERT INTO rider (username, email, phone_no, city, vehicle_number) VALUES ('$username', '$email', '$phone', '$city', '$vehicle_number')";
if (mysqli_query($conn, $query)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
}
mysqli_close($conn);
?>
