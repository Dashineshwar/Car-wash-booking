<?php
include_once '../includes/connection.php';
session_start();

$rider_id = $_SESSION["riderId"];  // Assuming rider_id is stored in the session

$wallet_balance = 0;  // Default to 0 if no result is returned

$query = "SELECT wallet FROM rider WHERE rider_id = '$rider_id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $wallet_balance = (float)$row['wallet'];
}

// Return the wallet balance as JSON
echo json_encode(['wallet_balance' => $wallet_balance]);
mysqli_close($conn);
?>
