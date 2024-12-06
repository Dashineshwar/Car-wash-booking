<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../includes/connection.php';


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $rider_id = isset($_GET['rider_id']) ? mysqli_real_escape_string($conn, $_GET['rider_id']) : '';

    if (!empty($rider_id)) {
        $query = "SELECT * FROM rider WHERE rider_id = '$rider_id'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $rider = mysqli_fetch_assoc($result);
            echo json_encode($rider);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Rider not found']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Rider ID is required']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}

mysqli_close($conn);
?>
