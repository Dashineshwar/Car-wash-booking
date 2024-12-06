<?php
include '../../includes/connection.php';
include '../../includes/session.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$response = ['bookings' => []];

if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $date_from = $_GET['date_from'] ?? '';
    $date_to = $_GET['date_to'] ?? '';

    // Prepare the base query
    $query = "SELECT * FROM booking WHERE user_id = ? AND (status = 'pending' OR status = 'done')";

    // Add date filters if provided
    if (!empty($date_from)) {
        $query .= " AND booking_time >= ?";
    }
    if (!empty($date_to)) {
        $query .= " AND booking_time <= ?";
    }

    $stmt = $conn->prepare($query);

    if (!empty($date_from) && !empty($date_to)) {
        $stmt->bind_param('iss', $user_id, $date_from, $date_to);
    } elseif (!empty($date_from)) {
        $stmt->bind_param('is', $user_id, $date_from);
    } elseif (!empty($date_to)) {
        $stmt->bind_param('is', $user_id, $date_to);
    } else {
        $stmt->bind_param('i', $user_id);
    }

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $response['bookings'][] = [
                'booking_id' => $row['booking_id'],
                'service_type' => $row['service_type'],
                'booking_type' => $row['booking_type'],
                'price' => $row['price'],
                'booking_time' => $row['booking_time'],
                'address' => $row['address_line_1'] . ', ' . $row['city'] . ', ' . $row['state'] . ', ' . $row['country'],
                'status' => $row['status']
            ];
        }
    } else {
        $response['error'] = 'Database query failed: ' . $stmt->error;
    }
} else {
    $response['error'] = 'User ID not provided.';
}

echo json_encode($response);
?>
