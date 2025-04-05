<?php
session_start();
require '../includes/connection.php';

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("Location: welcome.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $email = $_SESSION["google_email"];
    $phone_no = $_POST['phone_no'];
    $address1 = $_POST['address_line_1'];
    $address2 = $_POST['address_line_2'];
    $postcode = $_POST['postcode'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $country = $_POST['country'];

    $sql = "INSERT INTO users (username, full_name, email, phone_no, address_line_1, address_line_2, postcode, city, state, country, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $username, $full_name, $email, $phone_no, $address1, $address2, $postcode, $city, $state, $country);

    if ($stmt->execute()) {
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = $conn->insert_id;
        $_SESSION["username"] = $username;
        $_SESSION["email"] = $email;
        $_SESSION["phone"] = $phone_no;

        header("Location: welcome.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Complete Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Poppins', sans-serif;
        }
        .registration-card {
            max-width: 600px;
            background-color: #fff;
            margin: 50px auto;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }
        h2 {
            font-weight: 600;
            color: #1f2937;
        }
        label {
            font-weight: 500;
            margin-top: 10px;
        }
        input[type="text"], input[type="email"] {
            border-radius: 10px;
        }
        button {
            background-color: #1f2937;
            color: white;
            font-weight: 500;
            padding: 12px;
            border: none;
            border-radius: 30px;
            width: 100%;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #111827;
        }
    </style>
</head>
<body>
<div class="registration-card">
    <h2 class="text-center mb-3">Welcome, <?php echo $_SESSION["google_name"] ?? 'Guest'; ?></h2>
    <p class="text-center mb-4">Please complete the form below to finish your registration.</p>

    <form method="POST" action="">
        <div class="form-group">
            <label>Username:</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="full_name" class="form-control" value="<?php echo $_SESSION["google_name"] ?? ''; ?>" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" value="<?php echo $_SESSION["google_email"] ?? ''; ?>" disabled>
        </div>
        <div class="form-group">
            <label>Phone Number:</label>
            <input type="text" name="phone_no" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Address Line 1:</label>
            <input type="text" name="address_line_1" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Address Line 2:</label>
            <input type="text" name="address_line_2" class="form-control">
        </div>
        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Postcode:</label>
                <input type="text" name="postcode" class="form-control" required>
            </div>
            <div class="form-group col-md-4">
                <label>City:</label>
                <input type="text" name="city" class="form-control" required>
            </div>
            <div class="form-group col-md-4">
                <label>State:</label>
                <input type="text" name="state" class="form-control" required>
            </div>
        </div>
        <div class="form-group">
            <label>Country:</label>
            <input type="text" name="country" class="form-control" required>
        </div>
        <button type="submit">Complete Registration</button>
    </form>
</div>
</body>
</html>