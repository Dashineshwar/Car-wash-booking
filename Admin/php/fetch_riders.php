<?php
include '../includes/connection.php';

$response = [];

// Fetch riders
$query = "SELECT rider_id, username FROM rider";
$result = mysqli_query($conn, $query);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response[] = $row;
    }
    echo json_encode($response);
} else {
    echo json_encode(['error' => 'Failed to fetch riders']);
}

mysqli_close($conn);
?>
