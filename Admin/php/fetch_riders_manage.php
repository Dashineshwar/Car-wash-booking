<?php
include '../includes/connection.php';

$query = "SELECT * FROM rider";
$result = mysqli_query($conn, $query);

$riders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $riders[] = $row;
}
echo json_encode($riders);

mysqli_close($conn);
?>
