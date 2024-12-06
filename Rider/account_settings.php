<?php
include 'includes/rider_session.php';  // Include rider session file
include 'includes/connection.php';    // Include database connection
include 'includes/topbar.php';        // Optional topbar

// Fetch rider details
$rider_id = $_SESSION['riderId']; // Assuming rider_id is stored in the session after login
$query = "SELECT * FROM rider WHERE rider_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $rider_id);
$stmt->execute();
$rider = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
            max-width: 800px;
        }
        .form-section {
            margin-bottom: 30px;
        }
        .form-section h4 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">Account Settings</h2>

    <!-- Update Password -->
    <div class="form-section">
        <h4>Update Password</h4>
        <form id="password-form">
            <div class="form-group">
                <label for="old-password">Old Password</label>
                <input type="password" id="old-password" name="old_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="new-password">New Password</label>
                <input type="password" id="new-password" name="new_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm New Password</label>
                <input type="password" id="confirm-password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
    </div>

    <!-- Update Address -->
    <div class="form-section">
        <h4>Update Address</h4>
        <form id="address-form">
            <div class="form-group">
                <label for="address-line-1">Address Line 1</label>
                <input type="text" id="address-line-1" name="address_line_1" class="form-control" value="<?= htmlspecialchars($rider['address_line_1']) ?>" required>
            </div>
            <div class="form-group">
                <label for="address-line-2">Address Line 2</label>
                <input type="text" id="address-line-2" name="address_line_2" class="form-control" value="<?= htmlspecialchars($rider['address_line_2']) ?>">
            </div>
            <div class="form-group">
                <label for="postcode">Postcode</label>
                <input type="text" id="postcode" name="postcode" class="form-control" value="<?= htmlspecialchars($rider['postcode']) ?>" required>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" class="form-control" value="<?= htmlspecialchars($rider['city']) ?>" required>
            </div>
            <div class="form-group">
                <label for="state">State</label>
                <input type="text" id="state" name="state" class="form-control" value="<?= htmlspecialchars($rider['state']) ?>" required>
            </div>
            <div class="form-group">
                <label for="country">Country</label>
                <input type="text" id="country" name="country" class="form-control" value="<?= htmlspecialchars($rider['country']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Address</button>
        </form>
    </div>

    <!-- Update Email -->
    <div class="form-section">
        <h4>Update Email</h4>
        <form id="email-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($rider['email']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Email</button>
        </form>
    </div>

    <!-- Update Phone Number -->
    <div class="form-section">
        <h4>Update Phone Number</h4>
        <form id="phone-form">
            <div class="form-group">
                <label for="phone-no">Phone Number</label>
                <input type="text" id="phone-no" name="phone_no" class="form-control" value="<?= htmlspecialchars($rider['phone_no']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Phone Number</button>
        </form>
    </div>
</div>

<script>
    // Update Password
    $('#password-form').on('submit', function (e) {
        e.preventDefault();
        const data = $(this).serialize();
        $.post('php/account_settings/update_password.php', data, function (response) {
            alert(response.message);
        }, 'json').fail(function () {
            alert('Failed to update password.');
        });
    });

    // Update Address
    $('#address-form').on('submit', function (e) {
        e.preventDefault();
        const data = $(this).serialize();
        $.post('php/account_settings/update_address.php', data, function (response) {
            alert(response.message);
        }, 'json').fail(function () {
            alert('Failed to update address.');
        });
    });

    // Update Email
    $('#email-form').on('submit', function (e) {
        e.preventDefault();
        const data = $(this).serialize();
        $.post('php/account_settings/update_email.php', data, function (response) {
            alert(response.message);
        }, 'json').fail(function () {
            alert('Failed to update email.');
        });
    });

    // Update Phone Number
    $('#phone-form').on('submit', function (e) {
        e.preventDefault();
        const data = $(this).serialize();
        $.post('php/account_settings/update_phone.php', data, function (response) {
            alert(response.message);
        }, 'json').fail(function () {
            alert('Failed to update phone number.');
        });
    });
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>
