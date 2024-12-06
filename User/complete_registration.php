<?php
session_start();
require '../includes/connection.php';

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

    // Insert into database
    $sql = "INSERT INTO users (username, full_name, email, phone_no, address_line_1, address_line_2, postcode, city, state, country, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $username, $full_name, $email, $phone_no, $address1, $address2, $postcode, $city, $state, $country);

    if ($stmt->execute()) {
        // Start session and redirect to the main dashboard
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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f7f7f7;
        }
        .container {
            width: 90%;
            max-width: 500px;
            padding: 40px;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $_SESSION["google_name"]; ?></h2>
        <p>Please fill in the details below to complete your registration.</p>
        <form method="POST" action="">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Full Name:</label>
            <input type="text" name="full_name" value="<?php echo $_SESSION["google_name"]; ?>" required>

            <label>Email:</label>
            <input type="email" name="email" value="<?php echo $_SESSION["google_email"]; ?>" disabled>

            <label>Phone Number:</label>
            <input type="text" name="phone_no" required>

            <label>Address Line 1:</label>
            <input type="text" name="address_line_1" required>

            <label>Address Line 2:</label>
            <input type="text" name="address_line_2">

            <label>Postcode:</label>
            <input type="text" name="postcode" required>

            <label>City:</label>
            <input type="text" name="city" required>

            <label>State:</label>
            <input type="text" name="state" required>

            <label>Country:</label>
            <input type="text" name="country" required>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
