<?php
include_once 'connection.php';
// Check if session is not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Assume this is how you retrieve the rider's wallet balance
$rider_id = $_SESSION["riderId"];  // Assuming rider_id is stored in the session
$query = "SELECT wallet FROM rider WHERE rider_id = '$rider_id'";
$result = mysqli_query($conn, $query);
$wallet_balance = 0;  // Default to 0 in case the query returns no result or the wallet is NULL

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $wallet_balance = (float)$row['wallet'];  // Cast to float to avoid errors
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
    /* General Styling */
    .navbar {
        background-color: #ffffff; /* White background for minimal design */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    }
    .navbar-brand img {
        height: 35px;
        width: 35px;
    }
    .nav-link {
        color: #333; /* Dark grey text */
        font-weight: 500;
    }
    .nav-link:hover {
        color: #007bff; /* Blue on hover */
    }
    .dropdown-menu {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .dropdown-item:hover {
        background-color: #f1f1f1;
    }
    .rounded-circle {
        object-fit: cover;
    }
    .dropdown-menu {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        border: none; /* Remove border */
    }
    .dropdown-item {
        color: #333; /* Dark grey font */
    }
    .dropdown-item:hover {
        background-color: #f1f1f1; /* Light grey hover */
    }
    .wallet-balance {
        font-weight: bold;
        color: #007bff; /* Highlight wallet in blue */
    }
    .img-profile {
        height: 30px;
        width: 30px;
        object-fit: cover;
        border-radius: 50%;
    }
    .navbar-toggler {
        border: none;
    }
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23333' viewBox='0 0 30 30'%3E%3Cpath stroke='%23333' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
    }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="rider_dashboard.php">
        <img src="../images/official_logo.jpg" alt="Logo" style="height: 35px; width: 35px;">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">

            <!-- Wallet -->
            <li class="nav-item">
                <span class="nav-link text-primary font-weight-bold">Wallet: RM <?php echo number_format($wallet_balance, 2); ?></span>
            </li>
            <!-- Account Link -->
            <li class="nav-item">
                <a class="nav-link" href="rider_dashboard.php">View Orders</a>
            </li>


            <!-- Account Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="accountDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Account
                </a>
                <div class="dropdown-menu" aria-labelledby="accountDropdown">
                    <a class="dropdown-item" href="account_settings.php">Account Settings</a>
                    <a class="dropdown-item" href="manage_vehicles.php">Manage Vehicles</a>
                </div>
            </li>
            <!-- Earnings Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="accountDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Earnings
                </a>
                <div class="dropdown-menu" aria-labelledby="accountDropdown">
                    <a class="dropdown-item" href="view_salary.php">View Salary</a>
                </div>
            </li>

            <!-- Profile Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle" src="../images/icons/user.png" alt="User" style="height: 30px; width: 30px;">
                    <span>User Name</span>
                </a>
                <div class="dropdown-menu" aria-labelledby="profileDropdown">
                    <a class="dropdown-item" href="#">Profile</a>
                    <a class="dropdown-item" href="#">Settings</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>


<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-primary" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
