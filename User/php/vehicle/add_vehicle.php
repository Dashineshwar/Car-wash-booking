<?php
include '../../../includes/session.php';
include '../../../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Fallback: if username is not set, try to retrieve it from DB using user ID
    if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
        if (isset($_SESSION['id'])) {
            $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
            $stmt->bind_param("i", $_SESSION['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($user = $result->fetch_assoc()) {
                $_SESSION['username'] = $user['username'];
            } else {
                die("Error: User not found in DB.");
            }
        } else {
            die("Error: User session not found.");
        }
    }

    $username = $_SESSION['username'];
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
