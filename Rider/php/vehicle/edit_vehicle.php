<?php
include '../../../includes/session.php';
include '../../../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $number_plate = $_POST['number_plate'];
    $type = $_POST['type'];
    $brand = $_POST['brand'];
    $model = $_POST['model'];

    // Update the vehicle information in the database
    $query = "UPDATE vehicle SET number_plate = ?, type = ?, brand = ?, model = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $number_plate, $type, $brand, $model, $id);
    $stmt->execute();

    // Redirect back to manage_vehicles.php
    header("Location: ../../manage_vehicles.php");
    exit();
}
?>
