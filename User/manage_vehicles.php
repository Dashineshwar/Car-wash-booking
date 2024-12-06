<?php
include '../includes/session.php';
include '../includes/connection.php';
include '../includes/topbar.php';

// Fetch all vehicles belonging to the logged-in user
$username = $_SESSION['username']; // Assuming the username is stored in the session
$query = "SELECT * FROM vehicle WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vehicles</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- For icons -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            max-width: 900px;
            margin: 20px auto;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Manage Your Vehicles</h2>
    <button class="btn btn-success mb-3" data-toggle="modal" data-target="#addVehicleModal">Add New Vehicle</button>
    <a href="welcome.php" class="btn btn-primary mb-3">Book a car wash</a>


    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Number Plate</th>
                        <th>Type</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Registered Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['number_plate']); ?></td>
                        <td><?php echo htmlspecialchars($row['type']); ?></td>
                        <td><?php echo htmlspecialchars($row['brand']); ?></td>
                        <td><?php echo htmlspecialchars($row['model']); ?></td>
                        <td><?php echo htmlspecialchars($row['registered_date']); ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm edit-btn" 
                                    data-id="<?php echo $row['id']; ?>" 
                                    data-number-plate="<?php echo $row['number_plate']; ?>" 
                                    data-type="<?php echo $row['type']; ?>" 
                                    data-brand="<?php echo $row['brand']; ?>" 
                                    data-model="<?php echo $row['model']; ?>" 
                                    data-toggle="modal" data-target="#editVehicleModal">Edit</button>
                            <a href="php/vehicle/delete_vehicle.php?id=<?php echo $row['id']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this vehicle?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Vehicle Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="php/vehicle/add_vehicle.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Vehicle</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Number Plate</label>
                        <input type="text" class="form-control" name="number_plate" required>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select class="form-control" name="type" required>
                            <option value="Sedan">Sedan</option>
                            <option value="SUV">SUV</option>
                            <option value="MPV">MPV</option>
                            <option value="Van">Van</option>
                            <option value="Lorry">Lorry</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Brand</label>
                        <input type="text" class="form-control" name="brand" required>
                    </div>
                    <div class="form-group">
                        <label>Model</label>
                        <input type="text" class="form-control" name="model" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Vehicle</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Vehicle Modal -->
<div class="modal fade" id="editVehicleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="php/vehicle/edit_vehicle.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Vehicle</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="form-group">
                        <label>Number Plate</label>
                        <input type="text" class="form-control" name="number_plate" id="edit-number-plate" required>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select class="form-control" name="type" id="edit-type" required>
                            <option value="Sedan">Sedan</option>
                            <option value="SUV">SUV</option>
                            <option value="MPV">MPV</option>
                            <option value="Van">Van</option>
                            <option value="Lorry">Lorry</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Brand</label>
                        <input type="text" class="form-control" name="brand" id="edit-brand" required>
                    </div>
                    <div class="form-group">
                        <label>Model</label>
                        <input type="text" class="form-control" name="model" id="edit-model" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Populate the edit modal with vehicle data
    $('.edit-btn').on('click', function() {
        const id = $(this).data('id');
        const numberPlate = $(this).data('number-plate');
        const type = $(this).data('type');
        const brand = $(this).data('brand');
        const model = $(this).data('model');

        $('#edit-id').val(id);
        $('#edit-number-plate').val(numberPlate);
        $('#edit-type').val(type);
        $('#edit-brand').val(brand);
        $('#edit-model').val(model);
    });
</script>

<?php include '../includes/footer.php'; ?>

</body>
</html>
