<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashing</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
    /* General Navbar Styling */
    .navbar {
        background-color: white; /* Yellow background */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    }

    /* Navbar links */
    .navbar .nav-link {
        color: black; /* Black font */
        font-weight: 500;
    }



    .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23000' viewBox='0 0 30 30'%3E%3Cpath stroke='%23000' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
    width: 24px;
    height: 24px;
    }


    /* Profile Image Styling */
    .img-profile {
        height: 30px;
        width: 30px;
        object-fit: cover;
        border-radius: 50%;

    }
</style>
</head>
<body>


<nav class="navbar navbar-expand-lg" style="background-color: white;">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="../User/welcome.php">
            <img src="../images/icons/Logo.png" alt="Logo" style="height: 35px; width: 80px;">
        </a>

        <!-- Custom Navbar Toggler -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span style="display: block; width: 25px; height: 3px; background-color: black; margin: 5px 0;"></span>
            <span style="display: block; width: 25px; height: 3px; background-color: black; margin: 5px 0;"></span>
            <span style="display: block; width: 25px; height: 3px; background-color: black; margin: 5px 0;"></span>
        </button>

        <!-- Navbar Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../User/welcome.php">Book a Car Wash</a>
                </li>
                
                <!-- Account Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="accountDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Account
                    </a>
                    <div class="dropdown-menu" aria-labelledby="accountDropdown">
                        <a class="dropdown-item" href="account_settings.php">Account Settings</a>
                        <a class="dropdown-item" href="view_booking.php">View My Bookings</a>
                        <a class="dropdown-item" href="manage_vehicles.php">Manage Vehicles</a>
                    </div>
                </li>

                <!-- Profile Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img class="rounded-circle img-profile" src="../images/icons/user.png" alt="User">
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
    </div>
</nav>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
