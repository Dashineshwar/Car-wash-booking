<?php
include '../includes/connection.php';
include '../includes/session.php';

if (isset($_POST['vehicle_type'])) {
    $vehicle_type = $_POST['vehicle_type'];
    $username = $_SESSION['username']; // Assuming the user's session contains the username

    // Base query to fetch vehicles based on username
    $sql = "SELECT * FROM vehicle WHERE username = ?";
    $params = [$username];
    $types = "s"; // Parameter type for username

    // Modify query based on vehicle type
    if ($vehicle_type !== 'All My Vehicle') {
        $sql .= " AND type = ?";
        $params[] = $vehicle_type;
        $types .= "s"; // Parameter type for vehicle type
    }

    // Add criteria filtering if provided
    if (isset($_POST['criteria']) && !empty($_POST['criteria'])) {
        $criteria = $_POST['criteria'];
        $sql .= " AND (make LIKE ? OR model LIKE ? OR plate_number LIKE ?)";
        $like_criteria = '%' . $criteria . '%';
        $params[] = $like_criteria;
        $params[] = $like_criteria;
        $params[] = $like_criteria;
        $types .= "sss"; // Parameter types for the three LIKE statements
    }

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If vehicles are found, display them in a dropdown
        echo "<h3>$vehicle_type</h3>";
        echo "<select id='vehicle-select' class='form-control'>";
        echo "<option value=''>Pick your ride and let’s get it sparkling!</option>";
        while ($row = $result->fetch_assoc()) {
            $vehicle_info = $row['number_plate'] . " (" . $row['brand'] . " " . $row['model'] . ")";
            $vehicle_id = $row['id']; // Assuming there is a unique ID for each vehicle
            echo "<option value='" .$row['number_plate']. "'>" . $vehicle_info . "</option>";
        }
        echo "</select>";
        echo "<div id='service-select-container' style='display: none; margin-top: 20px;'></div>";
    } else {
        echo "<p>No vehicles found for the selected type.</p>";
    }

    $stmt->close();
} else {
    echo "<p>Invalid request.</p>";
}

$conn->close();
?>
