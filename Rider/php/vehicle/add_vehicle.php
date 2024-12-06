<?php
include '../../../includes/session.php';
include '../../../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username']; // Get the logged-in user's username
    $number_plate = $_POST['number_plate'];
    $type = $_POST['type'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];

    // Insert the new vehicle into the database
    $query = "INSERT INTO vehicle (username, number_plate, type, brand, model, registered_date) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $username, $number_plate, $type, $brand, $model);
    $stmt->execute();

    // Redirect back to manage_vehicles.php
    header("Location: ../../manage_vehicles.php");
    exit();
}
?>
