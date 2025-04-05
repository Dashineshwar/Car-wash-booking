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
        $types .= "sss"; 
    }

    // Prepare and execute the statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h3>$vehicle_type</h3>";
        echo "<select id='vehicle-select' class='form-control'>";
        echo "<option value=''>Pick your ride and let’s get it sparkling!</option>";
        while ($row = $result->fetch_assoc()) {
            $vehicle_info = $row['number_plate'] . " (" . $row['brand'] . " " . $row['model'] . ")";
            echo "<option value='" . $row['number_plate'] . "'>$vehicle_info</option>";
        }
        echo "</select>";
    
        // 👇👇 Add this container to allow JS to inject service and booking buttons
        echo "<div id='service-select-container' class='card-ui mt-4' style='display: none;'></div>";
    
    } else {
        echo "
            <div class='text-center'>
                <p>No vehicles found for the selected type.</p>
                <a href='manage_vehicles.php' class='btn btn-danger rounded-pill px-4 mt-3'>
                    Please register a vehicle first
                </a>
            </div>
        ";
    }
    
    

    $stmt->close();
} else {
    echo "<p>Invalid request.</p>";
}

$conn->close();
?>
