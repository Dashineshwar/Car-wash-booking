<?php
include 'includes/rider_session.php'; // Rider-specific session file
include 'includes/connection.php';
include 'includes/topbar.php';

// Fetch registered vehicle number for the logged-in rider
$rider_id = $_SESSION['riderId']; // Assuming rider ID is stored in session
$query = "SELECT vehicle_number FROM rider WHERE rider_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $rider_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vehicle</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
            max-width: 600px;
        }
        .table {
            margin-top: 20px;
        }
        .action-btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .action-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center">Your Registered Vehicle</h2>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Vehicle Number</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['vehicle_number']); ?></td>
                    <td>
                        <button class="action-btn" onclick="notAuthorized()">Edit</button>
                        <button class="action-btn" onclick="notAuthorized()">Delete</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="2" class="text-center">No vehicle registered.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Show alert when attempting to edit or delete
    function notAuthorized() {
        alert("You are not authorized to edit or delete your registered vehicle.");
    }
</script>

<?php include '../includes/footer.php'; ?>

</body>
</html>
