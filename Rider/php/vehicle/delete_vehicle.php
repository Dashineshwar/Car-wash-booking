<?php
include '../../../includes/session.php';
include '../../../includes/connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the vehicle from the database
    $query = "DELETE FROM vehicle WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    // Redirect back to manage_vehicles.php
    header("Location: ../../manage_vehicles.php");
    exit();
}
?>
