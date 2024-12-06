<?php
include 'includes/admin_session.php';
include 'includes/connection.php';
include 'includes/topbar.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Riders</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center mb-4">Manage Riders</h1>

    <!-- Add Rider Button -->
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addRiderModal">Add New Rider</button>

    <!-- Rider List Table -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Phone</th>
                <th>City</th>
                <th>Available</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="riderTableBody">
            <!-- Populated via AJAX -->
        </tbody>
    </table>
</div>

<!-- Add Rider Modal -->
<div class="modal fade" id="addRiderModal" tabindex="-1" role="dialog" aria-labelledby="addRiderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="addRiderForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRiderModalLabel">Add New Rider</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form Fields -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="wallet">Wallet</label>
                                <input type="number" class="form-control" id="wallet" name="wallet">
                            </div>
                            <div class="form-group">
                                <label for="address_line_1">Address Line 1</label>
                                <input type="text" class="form-control" id="address_line_1" name="address_line_1">
                            </div>
                            <div class="form-group">
                                <label for="address_line_2">Address Line 2</label>
                                <input type="text" class="form-control" id="address_line_2" name="address_line_2">
                            </div>
                            <div class="form-group">
                                <label for="postcode">Postcode</label>
                                <input type="text" class="form-control" id="postcode" name="postcode">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" class="form-control" id="city" name="city">
                            </div>
                            <div class="form-group">
                                <label for="state">State</label>
                                <input type="text" class="form-control" id="state" name="state">
                            </div>
                            <div class="form-group">
                                <label for="country">Country</label>
                                <input type="text" class="form-control" id="country" name="country">
                            </div>
                            <div class="form-group">
                                <label for="vehicle_number">Vehicle Number</label>
                                <input type="text" class="form-control" id="vehicle_number" name="vehicle_number">
                            </div>
                            <div class="form-group">
                                <label for="phone_no">Phone Number</label>
                                <input type="text" class="form-control" id="phone_no" name="phone_no">
                            </div>
                            <div class="form-group">
                                <label for="available">Available</label>
                                <select class="form-control" id="available" name="available">
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="serving_postcode">Serving Postcode</label>
                                <input type="text" class="form-control" id="serving_postcode" name="serving_postcode">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Rider</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Rider Modal -->
<!-- Same as Add Modal but pre-filled -->
<div class="modal fade" id="editRiderModal" tabindex="-1" role="dialog" aria-labelledby="editRiderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editRiderForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRiderModalLabel">Edit Rider</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Same fields as Add Modal -->
                    <input type="hidden" id="edit_rider_id" name="rider_id">
                    <!-- Add all fields here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Rider</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Fetch Riders
    function fetchRiders() {
        $.ajax({
            url: 'php/fetch_riders_manage.php',
            type: 'GET',
            success: function (data) {
                const riders = JSON.parse(data);
                const tbody = $("#riderTableBody");
                tbody.empty();
                riders.forEach(rider => {
                    tbody.append(`
                        <tr>
                            <td>${rider.rider_id}</td>
                            <td>${rider.username}</td>
                            <td>${rider.email}</td>
                            <td>${rider.phone_no}</td>
                            <td>${rider.city}</td>
                            <td>${rider.available}</td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-btn" data-id="${rider.rider_id}">Edit</button>
                                <button class="btn btn-danger btn-sm delete-btn" data-id="${rider.rider_id}">Delete</button>
                            </td>
                        </tr>
                    `);
                });
            }
        });
    }

    // Add Rider
    $('#addRiderForm').on('submit', function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        $.post('php/add_rider.php', formData, function (response) {
            alert('Rider added successfully!');
            $('#addRiderModal').modal('hide');
            fetchRiders();
        });
    });

    // Fetch on page load
    fetchRiders();
});
</script>
</body>
</html>
