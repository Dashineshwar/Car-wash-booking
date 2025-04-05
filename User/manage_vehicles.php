<?php
include '../includes/session.php';
include '../includes/connection.php';
include '../includes/topbar.php';

$username = $_SESSION['username'];
$query = "SELECT * FROM vehicle WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$vehicles = [];
while ($row = $result->fetch_assoc()) {
    $vehicles[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Vehicles</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { margin-top: 30px; }
        .card-vehicle {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .card-vehicle h5 { margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Manage Your Vehicles</h2>
    <div class="text-center mb-3">
        <button class="btn btn-success" data-toggle="modal" data-target="#addVehicleModal">Add New Vehicle</button>
        <a href="welcome.php" class="btn btn-primary">Book a car wash</a>
    </div>

    <!-- Desktop Table View -->
    <div class="table-responsive d-none d-lg-block">
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
                <?php foreach ($vehicles as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['number_plate']); ?></td>
                        <td><?= htmlspecialchars($row['type']); ?></td>
                        <td><?= htmlspecialchars($row['brand']); ?></td>
                        <td><?= htmlspecialchars($row['model']); ?></td>
                        <td><?= htmlspecialchars($row['registered_date']); ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm edit-btn"
                                data-id="<?= $row['id']; ?>"
                                data-number-plate="<?= $row['number_plate']; ?>"
                                data-type="<?= $row['type']; ?>"
                                data-brand="<?= $row['brand']; ?>"
                                data-model="<?= $row['model']; ?>"
                                data-toggle="modal" data-target="#editVehicleModal">Edit</button>
                            <a href="php/vehicle/delete_vehicle.php?id=<?= $row['id']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="d-lg-none">
        <?php foreach ($vehicles as $row): ?>
            <div class="card-vehicle bg-white shadow-sm">
                <h5><?= htmlspecialchars($row['number_plate']); ?> (<?= htmlspecialchars($row['brand']); ?>)</h5>
                <p><strong>Type:</strong> <?= htmlspecialchars($row['type']); ?></p>
                <p><strong>Model:</strong> <?= htmlspecialchars($row['model']); ?></p>
                <p><strong>Registered:</strong> <?= htmlspecialchars($row['registered_date']); ?></p>
                <button class="btn btn-warning btn-sm edit-btn"
                    data-id="<?= $row['id']; ?>"
                    data-number-plate="<?= $row['number_plate']; ?>"
                    data-type="<?= $row['type']; ?>"
                    data-brand="<?= $row['brand']; ?>"
                    data-model="<?= $row['model']; ?>"
                    data-toggle="modal" data-target="#editVehicleModal">Edit</button>
                <a href="php/vehicle/delete_vehicle.php?id=<?= $row['id']; ?>"
                   class="btn btn-danger btn-sm ml-2"
                   onclick="return confirm('Are you sure?');">Delete</a>
            </div>
        <?php endforeach; ?>
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
                    <input type="text" name="number_plate" class="form-control mb-2" placeholder="Number Plate" required>
                    <select name="type" class="form-control mb-2" required>
                        <option value="Sedan">Sedan</option>
                        <option value="SUV">SUV</option>
                        <option value="MPV">MPV</option>
                        <option value="Van">Van</option>
                        <option value="Lorry">Lorry</option>
                    </select>
                    <input type="text" name="brand" class="form-control mb-2" placeholder="Brand" required>
                    <input type="text" name="model" class="form-control" placeholder="Model" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Add Vehicle</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
                    <input type="text" name="number_plate" id="edit-number-plate" class="form-control mb-2" required>
                    <select name="type" id="edit-type" class="form-control mb-2" required>
                        <option value="Sedan">Sedan</option>
                        <option value="SUV">SUV</option>
                        <option value="MPV">MPV</option>
                        <option value="Van">Van</option>
                        <option value="Lorry">Lorry</option>
                    </select>
                    <input type="text" name="brand" id="edit-brand" class="form-control mb-2" required>
                    <input type="text" name="model" id="edit-model" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function () {
        $('.edit-btn').on('click', function () {
            $('#edit-id').val($(this).data('id'));
            $('#edit-number-plate').val($(this).data('number-plate'));
            $('#edit-type').val($(this).data('type'));
            $('#edit-brand').val($(this).data('brand'));
            $('#edit-model').val($(this).data('model'));
        });
    });
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
